<?php

namespace Tests\Feature\Api;

use App\Mail\Send2FACodeMail;
use App\Models\RemoteAccess;
use Illuminate\Support\Facades\Mail;

class AuthTest extends ApiTestCase
{
    private function login(string $email, string $password = 'password')
    {
        return $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => $password,
            'device_name' => 'Pixel',
        ]);
    }

    private function sentCode(): string
    {
        $code = null;
        Mail::assertSent(Send2FACodeMail::class, function (Send2FACodeMail $mail) use (&$code) {
            $code = (fn () => $this->code)->call($mail);

            return true;
        });

        return $code;
    }

    public function testLoginWithWrongPasswordFails()
    {
        $user = $this->createUser();

        $this->login($user->email, 'wrong')->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function testLoginWithout2FAReturnsAccessToken()
    {
        config(['app.2fa_enabled' => false]);
        $user = $this->createUser();

        $response = $this->login($user->email);

        $response->assertOk()
            ->assertJson(['two_factor_required' => false, 'user' => ['id' => $user->id]])
            ->assertJsonStructure(['token', 'expires_at']);

        $this->withBearer($response->json('token'))->getJson('/api/v1/me')->assertOk();
    }

    public function testLoginWith2FAFlow()
    {
        Mail::fake();
        $user = $this->createUser();

        $response = $this->login($user->email)->assertOk()->assertJson(['two_factor_required' => true]);
        $pendingToken = $response->json('token');

        // Pending token gives no access to data
        $this->withBearer($pendingToken)->getJson('/api/v1/me')->assertForbidden();

        $this->withBearer($pendingToken)
            ->postJson('/api/v1/auth/2fa/verify', ['code' => '000000'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('code');

        $response = $this->withBearer($pendingToken)
            ->postJson('/api/v1/auth/2fa/verify', ['code' => $this->sentCode()])
            ->assertOk()
            ->assertJson(['two_factor_required' => false]);

        $this->withBearer($pendingToken)->getJson('/api/v1/me')->assertUnauthorized();
        $this->withBearer($response->json('token'))->getJson('/api/v1/me')->assertOk()->assertJson(['data' => ['id' => $user->id]]);
    }

    public function testTooManyWrongCodesRevokePendingToken()
    {
        Mail::fake();
        $user = $this->createUser();
        $pendingToken = $this->login($user->email)->json('token');

        for ($i = 0; $i < 4; $i++) {
            $this->withBearer($pendingToken)->postJson('/api/v1/auth/2fa/verify', ['code' => '000000'])->assertStatus(422);
        }

        $this->withBearer($pendingToken)->postJson('/api/v1/auth/2fa/verify', ['code' => '000000'])->assertUnauthorized();
        $this->withBearer($pendingToken)->postJson('/api/v1/auth/2fa/verify', ['code' => $this->sentCode()])->assertUnauthorized();
    }

    public function testFullTokenCannotBeUsedFor2FARoutes()
    {
        $user = $this->createUser();

        $this->withBearer($this->accessToken($user))
            ->postJson('/api/v1/auth/2fa/resend')
            ->assertForbidden();
    }

    public function testBruteForceBlocksIp()
    {
        $user = $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->login($user->email, 'wrong')->assertStatus(422);
        }

        $this->login($user->email, 'wrong')->assertStatus(429);
        $this->assertTrue(RemoteAccess::isIpBlocked('127.0.0.1'));
        $this->login($user->email)->assertNotFound();
    }

    public function testLogoutRevokesToken()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);

        $this->withBearer($token)->postJson('/api/v1/auth/logout')->assertNoContent();
        $this->withBearer($token)->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function testResponsesAreJsonWithoutAcceptHeader()
    {
        $this->get('/api/v1/me')->assertUnauthorized()->assertJson(['message' => __('Unauthenticated.')]);
    }
}
