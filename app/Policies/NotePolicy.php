<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\Note;
use App\Models\User;

class NotePolicy extends PolicyBase
{
    protected $entitiesName = 'notes';

    public function view(User $user, Note $note): Response
    {
        if ($user->id === $note->user->id) {
            return Response::allow();
        }

        return Response::deny('Wrong user! ', 403);
    }

    public function update(User $user, Note $note): Response
    {
        return $this->checkPermission($user, 'modify-any', $note);
    }

    public function delete(User $user, Note $note): Response
    {
        return $this->checkPermission($user, 'modify-any', $note);
    }
}
