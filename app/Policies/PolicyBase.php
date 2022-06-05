<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

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

    public function viewAny(User $user): \Illuminate\Auth\Access\Response
    {
        return Response::allow();
    }

    public function create(User $user): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'create');
    }

    protected function checkPermission(User $user, string $permission): \Illuminate\Auth\Access\Response
    {
        if ($user->hasPermissionTo($this->entitiesName . '-' . $permission)) {
            return Response::allow();
        }

        return Response::deny('User do not have a permission to ' . $permission, 403);
    }
}