<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): \Illuminate\Auth\Access\Response
    {
        return Response::allow();
    }

    public function view(User $currentUser, User $user): \Illuminate\Auth\Access\Response
    {
        return Response::allow();
    }

    public function create(User $user): \Illuminate\Auth\Access\Response
    {
        if ($user->hasPermissionTo('users-register')) {
            return Response::allow();
        }

        return Response::deny('User do not have a permission to create another users', 403);
    }

    public function update(User $currentUser, User $user): \Illuminate\Auth\Access\Response
    {
        return $this->modify($currentUser, $user);
    }

    public function delete(User $currentUser, User $user): \Illuminate\Auth\Access\Response
    {
        return $this->modify($currentUser, $user);
    }

    private function modify(User $currentUser, User $user): \Illuminate\Auth\Access\Response
    {
        if ($currentUser->hasPermissionTo('users-modify-any') || ($user->id === $currentUser->id)) {
            return Response::allow();
        }
        
        return Response::deny('User do not have a permission to modify this user', 403);
    }

    public function changeRole(User $currentUser, User $user): \Illuminate\Auth\Access\Response
    {
        $request = request();
        if (
            ($request->has('role') && $currentUser->hasPermissionTo('users-change-roles-any')) ||
            $user->getRoleNames()->first() === $request->input('role')
        ) {
            return Response::allow();
        }

        return Response::deny('User do not have a permission to change role of this user', 403);
    }
}
