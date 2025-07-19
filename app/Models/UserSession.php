<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'is_active',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include sessions for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Update the last activity timestamp.
     */
    public function updateActivity()
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Deactivate the session.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get active sessions count for a user.
     */
    public static function getActiveSessionsCount($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Clean up old inactive sessions.
     */
    public static function cleanupOldSessions($days = 30)
    {
        return self::where('last_activity', '<', now()->subDays($days))
            ->where('is_active', false)
            ->delete();
    }
}
