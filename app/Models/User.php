<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Credential;
use App\Models\Group;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // RELATIONS

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }


    // OTHER

    public static function getUsersByRole(string $roleName): \Illuminate\Support\Collection
    {
        return self::whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        })->get();
    }
}
