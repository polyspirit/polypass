<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Mock 2FA cookie for testing
     */
    private function mock2FACookie(User $user)
    {
        $code = '123456';
        $user->auth_code = $code;
        $user->save();

        $linkCode = encrypt($user->id . '|' . $code . '|' . (time() + 3600));

        return $this->withCookie('user_2fa', $linkCode);
    }

    public function testUserCanViewSessionsPage()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withCookie('user_2fa', encrypt($user->id . '|123456|' . (time() + 3600)))
            ->get('/sessions');

        $response->assertStatus(200);
        $response->assertSee(__('sessions.title'));
    }

    public function testSessionIsCreatedOnLogin()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Mock 2FA cookie
        $code = '123456';
        $user->auth_code = $code;
        $user->save();

        $linkCode = encrypt($user->id . '|' . $code . '|' . (time() + 3600));

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Session should be created after successful 2FA
        $this->assertDatabaseHas('user_sessions', [
            'user_id' => $user->id,
        ]);
    }

    public function testUserCanTerminateOtherSession()
    {
        $user = User::factory()->create();

        // Create a session for the user
        $session = UserSession::create([
            'user_id' => $user->id,
            'session_id' => 'test-session-id',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser',
            'last_activity' => now(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withCookie('user_2fa', encrypt($user->id . '|123456|' . (time() + 3600)))
            ->delete("/sessions/{$session->session_id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('user_sessions', [
            'id' => $session->id,
            'is_active' => false,
        ]);
    }

    public function testUserCannotTerminateCurrentSession()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withCookie('user_2fa', encrypt($user->id . '|123456|' . (time() + 3600)))
            ->delete('/sessions/' . session()->getId());

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function testUserCanTerminateAllOtherSessions()
    {
        $user = User::factory()->create();

        // Create multiple sessions
        UserSession::create([
            'user_id' => $user->id,
            'session_id' => 'session-1',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser 1',
            'last_activity' => now(),
            'is_active' => true,
        ]);

        UserSession::create([
            'user_id' => $user->id,
            'session_id' => 'session-2',
            'ip_address' => '127.0.0.2',
            'user_agent' => 'Test Browser 2',
            'last_activity' => now(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withCookie('user_2fa', encrypt($user->id . '|123456|' . (time() + 3600)))
            ->delete('/sessions');

        $response->assertRedirect();
        $this->assertEquals(0, UserSession::where('user_id', $user->id)->where('is_active', true)->count());
    }

    public function testSessionStatsEndpoint()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withCookie('user_2fa', encrypt($user->id . '|123456|' . (time() + 3600)))
            ->get('/sessions/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_sessions',
            'active_sessions',
            'current_session',
        ]);
    }

    public function testSessionIsNotCreatedWithout2FA()
    {
        $user = User::factory()->create();

        // Try to access sessions without 2FA cookie
        $response = $this->actingAs($user)
            ->get('/sessions');

        // Should redirect to 2FA page
        $response->assertRedirect('/2fa');
    }
}
