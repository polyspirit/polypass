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
        return Response::allow();
    }

    public function update(User $user, Group $group): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'modify-any');
    }

    public function delete(User $user, Group $group): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'modify-any');
    }
}
