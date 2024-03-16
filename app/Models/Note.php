<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

use App\Models\User;
use App\Models\Group;

class Note extends Model
{
    protected $guarded = ['id'];


    // RELATIONS

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }


    // OTHER

    public function decrypt()
    {
        try {
            $this->note = Crypt::decryptString($this->note);
        } catch (\Throwable $th) {
            // nothing, just try
        }
    }
}
