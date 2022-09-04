<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy extends PolicyBase
{
    protected $entitiesName = 'groups';

    public function view(User $user, Group $group): \Illuminate\Auth\Access\Response
    {
        if ($user->id === $group->user->id) {
            return Response::allow();
        }

        return Response::deny('Wrong user! ', 403);
    }

    public function update(User $user, Group $group): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'modify-any', $group);
    }

    public function delete(User $user, Group $group): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'modify-any', $group);
    }
}
