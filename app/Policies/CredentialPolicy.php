<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\Credential;
use App\Models\User;

class CredentialPolicy extends PolicyBase
{
    protected $entitiesName = 'credentials';

    public function view(User $user, Credential $credential): Response
    {
        if ($user->id === $credential->user->id) {
            return Response::allow();
        }

        return Response::deny('Wrong user! ', 403);
    }

    public function update(User $user, Credential $credential): Response
    {
        return $this->checkPermission($user, 'modify-any', $credential);
    }

    public function delete(User $user, Credential $credential): Response
    {
        return $this->checkPermission($user, 'modify-any', $credential);
    }
}
