<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Credential;
use App\Models\Note;

class Group extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;


    // RELATIONS

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    // OTHER
    public function isRoot()
    {
        return ($this->name === 'root');
    }
}
