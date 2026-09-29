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

        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mine')
            ->assertJsonPath('data.0.login', 'l');
        $this->assertArrayNotHasKey('password', $response->json('data.0'));
        $this->assertArrayNotHasKey('note', $response->json('data.0'));
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


    /**
     * Create credentials of user directly: half of them with remote access.
     */
    private function createCredentials(\App\Models\User $user, int $count, ?int $groupId = null): void
    {
        for ($i = 0; $i < $count; $i++) {
            $credential = Credential::create(Credential::encryptAttributes([
                'user_id' => $user->id,
                'group_id' => $groupId ?? $this->rootGroup()->id,
                'name' => 'item ' . $i,
                'login' => 'l',
                'password' => 'p',
            ]));

            if ($i % 2 === 0) {
                $credential->remote()->create(['host' => '10.0.0.' . $i, 'port' => 22, 'protocol' => 'ssh']);
            }
        }
    }

    private function countQueries(callable $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $callback();
        DB::disableQueryLog();

        return count(DB::getQueryLog());
    }

    public function testListContainsRemoteAndDates()
    {
        $user = $this->createUser();
        $this->createCredentials($user, 2);

        $response = $this->withBearer($this->accessToken($user))->getJson('/api/v1/credentials')->assertOk();

        $response->assertJsonPath('data.0.remote', ['host' => '10.0.0.0', 'port' => 22, 'protocol' => 'ssh'])
            ->assertJsonPath('data.1.remote', null)
            ->assertJsonStructure(['data' => [['id', 'remote', 'created_at', 'updated_at']]]);
        $this->assertArrayHasKey('remote', $response->json('data.1'));
        $this->assertArrayNotHasKey('password', $response->json('data.0'));
    }

    public function testListQueryCountDoesNotDependOnItemsCount()
    {
        // Sanctum updates last_used_at only when it changes: a second boundary would add a query
        $this->freezeTime();

        $small = $this->createUser();
        $big = $this->createUser();
        $this->createCredentials($small, 1);
        $this->createCredentials($big, 10);
        $smallToken = $this->accessToken($small);
        $bigToken = $this->accessToken($big);

        // Warm up: first request saves token IP
        $this->withBearer($smallToken)->getJson('/api/v1/credentials');
        $this->withBearer($bigToken)->getJson('/api/v1/credentials');

        $smallQueries = $this->countQueries(fn () => $this->withBearer($smallToken)->getJson('/api/v1/credentials')->assertJsonCount(1, 'data'));
        $bigQueries = $this->countQueries(fn () => $this->withBearer($bigToken)->getJson('/api/v1/credentials')->assertJsonCount(10, 'data'));

        $this->assertSame($smallQueries, $bigQueries);
    }

    public function testGroupShowQueryCountDoesNotDependOnItemsCount()
    {
        // Sanctum updates last_used_at only when it changes: a second boundary would add a query
        $this->freezeTime();

        $small = $this->createUser();
        $big = $this->createUser();
        $smallGroup = Group::create(['user_id' => $small->id, 'name' => 's', 'type' => 'credential']);
        $bigGroup = Group::create(['user_id' => $big->id, 'name' => 'b', 'type' => 'credential']);
        $this->createCredentials($small, 1, $smallGroup->id);
        $this->createCredentials($big, 10, $bigGroup->id);
        $smallToken = $this->accessToken($small);
        $bigToken = $this->accessToken($big);

        $this->withBearer($smallToken)->getJson("/api/v1/groups/{$smallGroup->id}");
        $this->withBearer($bigToken)->getJson("/api/v1/groups/{$bigGroup->id}");

        $smallQueries = $this->countQueries(fn () => $this->withBearer($smallToken)->getJson("/api/v1/groups/{$smallGroup->id}")
            ->assertJsonPath('data.credentials.0.remote.host', '10.0.0.0'));
        $bigQueries = $this->countQueries(fn () => $this->withBearer($bigToken)->getJson("/api/v1/groups/{$bigGroup->id}")
            ->assertJsonCount(10, 'data.credentials'));

        $this->assertSame($smallQueries, $bigQueries);
    }
}
