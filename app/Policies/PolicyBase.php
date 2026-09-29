<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class PolicyBase
{
    use HandlesAuthorization;

    protected $entitiesName = null;

    public function __construct()
    {
        if (!isset($this->entitiesName)) {
            throw new \Exception('entitiesName property not specified');
        }
    }

    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $this->checkPermission($user, 'create');
    }

    protected function checkPermission(User $user, string $permission, $entity = null): Response
    {
        if ($user->hasPermissionTo($this->entitiesName . '-' . $permission)) {
            if (isset($entity)) {
                if ((int) $entity->user_id === $user->id) {
                    return Response::allow();
                } else {
                    return Response::deny('Wrong user! ', 403);
                }
            }

            return Response::allow();
        }

        return Response::deny('User do not have a permission to ' . $permission, 403);
    }

    /**
     * Update/delete own entity: "modify" permission is for own entities, "modify-any" includes it.
     * Ownership is checked in both cases: users never modify entities of other users.
     */
    protected function checkModifyPermission(User $user, $entity): Response
    {
        foreach (['modify-any', 'modify'] as $permission) {
            if ($user->checkPermissionTo($this->entitiesName . '-' . $permission)) {
                return $this->checkPermission($user, $permission, $entity);
            }
        }

        return Response::deny('User do not have a permission to modify', 403);
    }
}
