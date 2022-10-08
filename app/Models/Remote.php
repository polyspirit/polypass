<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Credential;

class Remote extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    const PROTOCOLS = [
        'ssh' => 22, 
        'ftp' => 21
    ];
    

    // RELATIONS

    public function credential()
    {
        return $this->belongsTo(Credential::class);
    }
}
