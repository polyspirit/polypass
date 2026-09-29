<?php

namespace Tests\Feature\Api;

class GroupNoteTest extends ApiTestCase
{
    public function testGroupCrudWithItems()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);

        $groupId = $this->withBearer($token)
            ->postJson('/api/v1/groups', ['name' => 'Work', 'type' => 'credential'])
            ->assertCreated()
            ->json('data.id');

        $credentialId = $this->withBearer($token)->postJson('/api/v1/credentials', [
            'name' => 'Srv', 'login' => 'l', 'password' => 'p', 'group_id' => $groupId,
            'remote' => ['host' => 'h', 'port' => 22, 'protocol' => 'ssh'],
        ])->json('data.id');

        $this->withBearer($token)->getJson("/api/v1/groups/{$groupId}")
            ->assertOk()
            ->assertJsonPath('data.credentials.0.id', $credentialId);

        $this->withBearer($token)->getJson('/api/v1/groups?type=credential')->assertOk()->assertJsonCount(1, 'data');

        $this->withBearer($token)->deleteJson("/api/v1/groups/{$groupId}")->assertNoContent();
        $this->assertDatabaseMissing('credentials', ['id' => $credentialId]);
        $this->assertDatabaseMissing('remotes', ['credential_id' => $credentialId]);
    }

    public function testCannotCreateRootGroup()
    {
        $user = $this->createUser();

        $this->withBearer($this->accessToken($user))
            ->postJson('/api/v1/groups', ['name' => 'r', 'type' => 'root'])
            ->assertStatus(422);
    }

    public function testRootGroupIsNotAccessibleDirectly()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);
        $root = $this->rootGroup();

        $this->withBearer($token)->getJson('/api/v1/groups/root')->assertOk()->assertJsonPath('data.id', $root->id);
        $this->withBearer($token)->deleteJson("/api/v1/groups/{$root->id}")->assertForbidden();
    }

    public function testNoteCrud()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);

        $id = $this->withBearer($token)
            ->postJson('/api/v1/notes', ['name' => 'Todo', 'note' => 'secret text'])
            ->assertCreated()
            ->assertJson(['data' => ['favorite' => false, 'note' => 'secret text']])
            ->json('data.id');

        $this->withBearer($token)->getJson('/api/v1/notes')->assertOk()->assertJsonMissingPath('data.0.note');

        $this->withBearer($token)
            ->patchJson("/api/v1/notes/{$id}", ['note' => 'changed'])
            ->assertOk()
            ->assertJsonPath('data.note', 'changed');

        $this->withBearer($token)->deleteJson("/api/v1/notes/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('notes', ['id' => $id]);
    }


    public function testNoteListContainsDates()
    {
        $user = $this->createUser();
        $token = $this->accessToken($user);
        $this->withBearer($token)->postJson('/api/v1/notes', ['name' => 'Todo', 'note' => 'text']);

        $this->withBearer($token)->getJson('/api/v1/notes')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }
}
