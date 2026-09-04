<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public const ROLES = [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'developer' => 'Developer',
        'backend_dev' => 'Backend Developer',
        'frontend_dev' => 'Frontend Developer',
    ];

    public function getRoleDisplayAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucwords(str_replace('_', ' ', $this->role ?? 'Developer'));
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        $fallbackIndex = (($this->id % 8) + 1);
        return asset('assets/img/users/user_' . $fallbackIndex . '.jpg');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isDeveloper(): bool
    {
        return in_array($this->role, ['developer', 'backend_dev', 'frontend_dev']);
    }

    public function isBackendDev(): bool
    {
        return $this->role === 'backend_dev';
    }

    public function isFrontendDev(): bool
    {
        return $this->role === 'frontend_dev';
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members', 'user_id', 'project_id')->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TaskTimeLog::class);
    }
}
