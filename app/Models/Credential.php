<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
}
