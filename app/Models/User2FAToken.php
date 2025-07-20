<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User2FAToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'ip_address',
        'user_agent',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the 2FA token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if token is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Deactivate the token.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Find active token for user and device.
     */
    public static function findActiveToken($userId, $ipAddress, $userAgent)
    {
        return self::where('user_id', $userId)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Clean up expired tokens.
     */
    public static function cleanupExpiredTokens()
    {
        return self::where('expires_at', '<', now())
            ->orWhere('is_active', false)
            ->delete();
    }
}
