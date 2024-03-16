<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\Group;
use App\Models\User;

class GroupPolicy extends PolicyBase
{
    protected $entitiesName = 'groups';

    public function view(User $user, Group $group): Response
    {
        if ($user->id === $group->user->id) {
            return Response::allow();
        }

        return Response::deny('Wrong user! ', 403);
    }

    public function update(User $user, Group $group): Response
    {
        return $this->checkPermission($user, 'modify-any', $group);
    }

    public function delete(User $user, Group $group): Response
    {
        return $this->checkPermission($user, 'modify-any', $group);
    }
}
