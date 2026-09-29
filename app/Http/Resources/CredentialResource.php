<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Credential with decrypted secrets, for a single item.
 *
 * @mixin \App\Models\Credential
 */
class CredentialResource extends CredentialSummaryResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'password' => $this->password,
            'note' => $this->note,
        ]);
    }
}
