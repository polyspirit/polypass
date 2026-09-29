<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Hash;

class ProfileTokenTest extends ApiTestCase
{
    public function testUpdateName()
    {
        $user = $this->createUser();

        $this->withBearer($this->accessToken($user))
            ->patchJson('/api/v1/me', ['name' => 'New Name'])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');
    }

    public function testPasswordChangeRequiresCurrentPassword()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);
        $data = ['password' => 'newpass1', 'password_confirmation' => 'newpass1'];

        $this->withBearer($token)->patchJson('/api/v1/me', $data)->assertStatus(422)->assertJsonValidationErrors('current_password');
        $this->withBearer($token)->patchJson('/api/v1/me', $data + ['current_password' => 'wrong'])->assertStatus(422);
        $this->withBearer($token)->patchJson('/api/v1/me', $data + ['current_password' => 'password'])->assertOk();

        $this->assertTrue(Hash::check('newpass1', $user->fresh()->password));
    }

    public function testTokensListAndRevoke()
    {
        $user = $this->createUser();
        $current = $this->accessToken($user, 'Phone');
        $this->accessToken($user, 'Tablet');
        $currentId = (int) explode('|', $current)[0];

        $response = $this->withBearer($current)->getJson('/api/v1/tokens')->assertOk()->assertJsonCount(2, 'data');
        $otherId = collect($response->json('data'))->firstWhere('is_current', false)['id'];

        $this->withBearer($current)->deleteJson("/api/v1/tokens/{$currentId}")->assertStatus(422);
        $this->withBearer($current)->deleteJson("/api/v1/tokens/{$otherId}")->assertNoContent();
        $this->withBearer($current)->deleteJson("/api/v1/tokens/{$otherId}")->assertNotFound();
    }

    public function testRevokeOtherTokens()
    {
        $user = $this->createUser();
        $current = $this->accessToken($user);
        $other = $this->accessToken($user);

        $this->withBearer($current)->deleteJson('/api/v1/tokens')->assertOk()->assertJson(['count' => 1]);

        $this->withBearer($other)->getJson('/api/v1/me')->assertUnauthorized();
        $this->withBearer($current)->getJson('/api/v1/me')->assertOk();
    }

    public function testCannotRevokeForeignToken()
    {
        $user = $this->createUser();
        $other = $this->createUser();
        $foreignId = (int) explode('|', $this->accessToken($other))[0];

        $this->withBearer($this->accessToken($user))->deleteJson("/api/v1/tokens/{$foreignId}")->assertNotFound();
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $foreignId]);
    }


    public function testTokenIpIsSavedOnLoginAndUpdatedOnUse()
    {
        config(['app.2fa_enabled' => false]);
        $user = $this->createUser();

        $token = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password', 'device_name' => 'Phone'])
            ->assertOk()
            ->json('token');
        $tokenId = (int) explode('|', $token)[0];

        $this->assertDatabaseHas('personal_access_tokens', ['id' => $tokenId, 'last_ip' => '10.0.0.1']);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
            ->withBearer($token)
            ->getJson('/api/v1/tokens')
            ->assertOk()
            ->assertJsonPath('data.0.ip', '10.0.0.2');

        $this->assertDatabaseHas('personal_access_tokens', ['id' => $tokenId, 'last_ip' => '10.0.0.2']);
    }

    public function testPendingTokenIpIsNotSaved()
    {
        \Illuminate\Support\Facades\Mail::fake();
        $user = $this->createUser();

        $token = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password', 'device_name' => 'Phone'])
            ->json('token');

        $this->assertDatabaseHas('personal_access_tokens', ['id' => (int) explode('|', $token)[0], 'last_ip' => null]);
    }
}
