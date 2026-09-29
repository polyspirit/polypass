<?php

namespace Tests\Feature;

use App\Enums\GroupTypeEnum;
use App\Models\Credential;
use App\Models\Group;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(?string $role): User
    {
        $user = User::factory()->create();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    /**
     * Credential, group and note owned by user.
     */
    private function entitiesOf(User $user): array
    {
        $rootId = Group::where('type', GroupTypeEnum::Root->value)->value('id');

        return [
            Credential::create(Credential::encryptAttributes([
                'user_id' => $user->id, 'group_id' => $rootId, 'name' => 'c', 'login' => 'l', 'password' => 'p',
            ])),
            Group::create(['user_id' => $user->id, 'name' => 'g', 'type' => GroupTypeEnum::Credential->value]),
            Note::create(['user_id' => $user->id, 'group_id' => $rootId, 'name' => 'n']),
        ];
    }

    public static function rolesProvider(): array
    {
        return [
            'user (modify)' => ['user'],
            'superadmin (modify-any)' => ['superadmin'],
        ];
    }

    #[DataProvider('rolesProvider')]
    public function testRoleCanModifyOwnEntities(string $role)
    {
        $user = $this->createUser($role);

        foreach ($this->entitiesOf($user) as $entity) {
            $this->assertTrue(Gate::forUser($user)->allows('update', $entity), $role . ' update ' . $entity::class);
            $this->assertTrue(Gate::forUser($user)->allows('delete', $entity), $role . ' delete ' . $entity::class);
        }
    }

    #[DataProvider('rolesProvider')]
    public function testRoleCannotModifyForeignEntities(string $role)
    {
        $user = $this->createUser($role);
        $owner = $this->createUser('user');

        foreach ($this->entitiesOf($owner) as $entity) {
            $this->assertFalse(Gate::forUser($user)->allows('view', $entity), $role . ' view ' . $entity::class);
            $this->assertFalse(Gate::forUser($user)->allows('update', $entity), $role . ' update ' . $entity::class);
            $this->assertFalse(Gate::forUser($user)->allows('delete', $entity), $role . ' delete ' . $entity::class);
        }
    }

    public function testUserWithoutPermissionsCannotModifyOwnEntities()
    {
        $user = $this->createUser(null);

        foreach ($this->entitiesOf($user) as $entity) {
            $this->assertFalse(Gate::forUser($user)->allows('update', $entity), $entity::class);
            $this->assertFalse(Gate::forUser($user)->allows('delete', $entity), $entity::class);
        }
    }

    public function testRootGroupCannotBeModified()
    {
        $user = $this->createUser('superadmin');
        $root = Group::where('type', GroupTypeEnum::Root->value)->firstOrFail();

        $this->assertFalse(Gate::forUser($user)->allows('update', $root));
        $this->assertFalse(Gate::forUser($user)->allows('delete', $root));
    }
}
