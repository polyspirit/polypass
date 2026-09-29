<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Credential without secrets, for lists.
 *
 * @mixin \App\Models\Credential
 */
class CredentialSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'name' => $this->name,
            'url' => $this->url,
            'favorite' => (bool) $this->favorite,
            'updated_at' => $this->updated_at,
        ];
    }
}
