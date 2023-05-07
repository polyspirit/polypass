<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteAccess extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['blocked' => 'boolean'];

    public static function isIpBlocked(string $ip): bool
    {
        return RemoteAccess::where('ip', $ip)->where('blocked', true)->exists();
    }

    public static function getBlockedIps(): \Illuminate\Database\Eloquent\Collection
    {
        return RemoteAccess::where('blocked', true)->get();
    }

    public static function getBlockedIpsList(): array
    {
        return self::getBlockedIps()->pluck('ip')->toArray();
    }
}
