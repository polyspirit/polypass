<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Group;
use App\Models\Remote;

class Credential extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['is_place' => 'boolean'];

    // RELATIONS

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function remote()
    {
        return $this->hasOne(Remote::class);
    }

    // OTHER

    protected static function booted()
    {
        static::retrieved(function ($credential) {
            $credential->decrypt();
        });
    }

    public function decrypt()
    {
        try {
            $this->login = Crypt::decryptString($this->login);
            $this->password = Crypt::decryptString($this->password);
            if (!empty($this->note)) {
                try {
                    $this->note = Crypt::decryptString($this->note);
                } catch (\Throwable $th) {
                    // Note field is not encrypted, keep as is
                }
            }
        } catch (\Throwable $th) {
            // nothing, just try
        }
    }
}
