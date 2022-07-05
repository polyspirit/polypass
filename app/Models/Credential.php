<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\Remote;

class Credential extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];


    // RELATIONS

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function remote()
    {
        return $this->hasOne(Remote::class);
    }
}
