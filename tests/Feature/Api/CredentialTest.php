<?php

namespace Tests\Feature\Api;

use App\Models\Credential;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class CredentialTest extends ApiTestCase
{
    public function testCreateCredentialStoresSecretsEncrypted()
    {
        $user = $this->createUser();

        $response = $this->withBearer($this->accessToken($user))->postJson('/api/v1/credentials', [
            'name' => 'Server',
            'login' => 'root',
            'password' => 'p@ss',
            'note' => 'note text',
            'remote' => ['host' => '1.2.3.4', 'port' => 22, 'protocol' => 'ssh'],
        ]);

        $response->assertCreated()->assertJson(['data' => [
            'group_id' => $this->rootGroup()->id,
            'login' => 'root',
            'password' => 'p@ss',
            'note' => 'note text',
            'remote' => ['host' => '1.2.3.4', 'port' => 22, 'protocol' => 'ssh'],
        ]]);

        $raw = DB::table('credentials')->find($response->json('data.id'));
        $this->assertNotEquals('root', $raw->login);
        $this->assertNotEquals('p@ss', $raw->password);
        $this->assertNotEquals('note text', $raw->note);
    }

    public function testPartialUpdateKeepsSecretsEncrypted()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);
        $id = $this->withBearer($token)->postJson('/api/v1/credentials', [
            'name' => 'Mail', 'login' => 'me', 'password' => 'secret',
        ])->json('data.id');

        $this->withBearer($token)
            ->patchJson("/api/v1/credentials/{$id}", ['favorite' => true])
            ->assertOk()
            ->assertJson(['data' => ['favorite' => true, 'login' => 'me', 'password' => 'secret']]);

        $raw = DB::table('credentials')->find($id);
        $this->assertNotEquals('me', $raw->login);
        $this->assertNotEquals('secret', $raw->password);
    }

    public function testRemoteCanBeRemoved()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);
        $id = $this->withBearer($token)->postJson('/api/v1/credentials', [
            'name' => 'Srv', 'login' => 'l', 'password' => 'p',
            'remote' => ['host' => 'h', 'port' => 22, 'protocol' => 'ssh'],
        ])->json('data.id');

        $this->withBearer($token)
            ->patchJson("/api/v1/credentials/{$id}", ['remote' => null])
            ->assertOk()
            ->assertJson(['data' => ['remote' => null]]);

        $this->assertDatabaseMissing('remotes', ['credential_id' => $id]);
    }

    public function testListHasNoSecretsAndOnlyOwnItems()
    {
        $user = $this->createUser();
        $other = $this->createUser();
        $token = $this->accessToken($user);
        $this->withBearer($token)->postJson('/api/v1/credentials', ['name' => 'Mine', 'login' => 'l', 'password' => 'p']);
        $this->withBearer($this->accessToken($other))->postJson('/api/v1/credentials', ['name' => 'Foreign', 'login' => 'l', 'password' => 'p']);

        $response = $this->withBearer($token)->getJson('/api/v1/credentials')->assertOk();

        $response->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Mine');
        $this->assertArrayNotHasKey('password', $response->json('data.0'));
    }

    public function testCannotAccessForeignCredential()
    {
        $user = $this->createUser();
        $other = $this->createUser();
        $foreign = Credential::create(Credential::encryptAttributes([
            'user_id' => $other->id, 'group_id' => $this->rootGroup()->id, 'name' => 'x', 'login' => 'l', 'password' => 'p',
        ]));
        $token = $this->accessToken($user);

        $this->withBearer($token)->getJson("/api/v1/credentials/{$foreign->id}")->assertForbidden();
        $this->withBearer($token)->patchJson("/api/v1/credentials/{$foreign->id}", ['name' => 'y'])->assertForbidden();
        $this->withBearer($token)->deleteJson("/api/v1/credentials/{$foreign->id}")->assertForbidden();
    }

    public function testCannotCreateInForeignGroup()
    {
        $user = $this->createUser();
        $other = $this->createUser();
        $foreignGroup = Group::create(['user_id' => $other->id, 'name' => 'g', 'type' => 'credential']);

        $this->withBearer($this->accessToken($user))
            ->postJson('/api/v1/credentials', ['name' => 'x', 'login' => 'l', 'password' => 'p', 'group_id' => $foreignGroup->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('group_id');
    }

    public function testDeleteCredentialWithRemote()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);
        $id = $this->withBearer($token)->postJson('/api/v1/credentials', [
            'name' => 'Srv', 'login' => 'l', 'password' => 'p',
            'remote' => ['host' => 'h', 'port' => 22, 'protocol' => 'ssh'],
        ])->json('data.id');

        $this->withBearer($token)->deleteJson("/api/v1/credentials/{$id}")->assertNoContent();

        $this->assertDatabaseMissing('credentials', ['id' => $id]);
        $this->assertDatabaseMissing('remotes', ['credential_id' => $id]);
    }
}
