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
}
