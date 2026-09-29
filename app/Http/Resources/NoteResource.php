<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Note with decrypted content, for a single item.
 *
 * @mixin \App\Models\Note
 */
class NoteResource extends NoteSummaryResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'note' => $this->note,
        ]);
    }
}
