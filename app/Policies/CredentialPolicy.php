<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CredentialPolicy extends PolicyBase
{
    protected $entitiesName = 'credentials';

    public function view(User $user, Credential $credential): \Illuminate\Auth\Access\Response
    {
        if ($user->id === $credential->user->id) {
            return Response::allow();
        }

        return Response::deny('Wrong user! ', 403);
    }

    public function update(User $user, Credential $credential): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'modify-any', $credential);
    }

    public function delete(User $user, Credential $credential): \Illuminate\Auth\Access\Response
    {
        return $this->checkPermission($user, 'modify-any', $credential);
    }
}
