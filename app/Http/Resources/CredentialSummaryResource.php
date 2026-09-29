<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Credential without password and note, for lists. Login is shown in list rows.
 * Load "remote" relation to avoid N+1.
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
            'login' => $this->login,
            'url' => $this->url,
            'favorite' => (bool) $this->favorite,
            'remote' => $this->remote ? [
                'host' => $this->remote->host,
                'port' => $this->remote->port,
                'protocol' => $this->remote->protocol,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
