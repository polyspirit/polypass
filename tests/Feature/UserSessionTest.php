<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\User2FAToken;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class UserSessionTest extends TestCase
{
    use RefreshDatabase;

    private string $sessionId;

    protected function setUp(): void
    {
        parent::setUp();

        // Fixed session id, so the id inside request is known to the test
        $this->sessionId = Str::random(40);
    }

    /**
     * Act as user who passed 2FA on this device.
     */
    private function actingAsVerified(User $user): static
    {
        $token = Str::random(64);

        User2FAToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'expires_at' => now()->addDay(),
            'is_active' => true,
        ]);

        return $this->actingAs($user)
            ->withCookie('user_2fa_token', $token)
            ->withCookie(config('session.cookie'), $this->sessionId);
    }

    private function createSession(User $user, string $sessionId): UserSession
    {
        return UserSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser',
            'last_activity' => now(),
            'is_active' => true,
        ]);
    }

    public function testUserCanViewSessionsPage()
    {
        $user = User::factory()->create();

        $response = $this->actingAsVerified($user)->get('/sessions');

        $response->assertStatus(200);
        $response->assertSee(__('sessions.title'));
    }

    public function testSessionIsCreatedAfter2FA()
    {
        $user = User::factory()->create();
        $user->auth_code = 'abc123';
        $user->save();

        $linkCode = encrypt($user->id . '|abc123|' . (time() + 3600));

        $response = $this->actingAs($user)->get('/2fa/' . $linkCode);

        $response->assertRedirect('/');
        $response->assertCookie('user_2fa_token');
        $this->assertDatabaseHas('user_sessions', ['user_id' => $user->id, 'is_active' => true]);
        $this->assertDatabaseHas('user_2fa_tokens', ['user_id' => $user->id, 'is_active' => true]);
    }

    public function testUserCanTerminateOtherSession()
    {
        $user = User::factory()->create();
        $session = $this->createSession($user, 'test-session-id');

        $response = $this->actingAsVerified($user)->delete("/sessions/{$session->session_id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertFalse($session->fresh()->is_active);
    }

    public function testUserCannotTerminateCurrentSession()
    {
        $user = User::factory()->create();

        $response = $this->actingAsVerified($user)->delete('/sessions/' . $this->sessionId);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('user_sessions', ['session_id' => $this->sessionId, 'is_active' => true]);
    }

    public function testUserCanTerminateAllOtherSessions()
    {
        $user = User::factory()->create();
        $first = $this->createSession($user, 'session-1');
        $second = $this->createSession($user, 'session-2');

        $response = $this->actingAsVerified($user)->delete('/sessions');

        $response->assertRedirect();
        $this->assertFalse($first->fresh()->is_active);
        $this->assertFalse($second->fresh()->is_active);
        $this->assertDatabaseHas('user_sessions', ['session_id' => $this->sessionId, 'is_active' => true]);
    }

    public function testSessionStatsEndpoint()
    {
        $user = User::factory()->create();

        $response = $this->actingAsVerified($user)->get('/sessions/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_sessions',
            'active_sessions',
            'current_session',
        ]);
        $response->assertJson(['current_session' => $this->sessionId]);
    }

    public function testSessionsRequire2FA()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/sessions');

        $response->assertRedirect('/2fa');
        $this->assertDatabaseMissing('user_sessions', ['user_id' => $user->id]);
    }
}
