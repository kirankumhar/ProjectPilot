<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'url',
        'start_date',
        'due_date',
        'user_id',
        'status',
        'priority',
    ];

    public const TYPES = [
        'new_development' => 'New Project',
        'maintenance' => 'Maintenance Project',
    ];

    public function getTypeDisplayAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucwords(str_replace('_', ' ', $this->type ?? 'new_development'));
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return $this->type === 'maintenance' ? 'badge-warning' : 'badge-info';
    }

    public function isMaintenance(): bool
    {
        return $this->type === 'maintenance';
    }

    public function isNewDevelopment(): bool
    {
        return $this->type === 'new_development' || empty($this->type);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')->withTimestamps();
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->relationLoaded('tasks')) {
            $total = $this->tasks->count();
            if ($total === 0) {
                return $this->status === 'completed' ? 100 : 0;
            }
            $completed = $this->tasks->where('status', 'completed')->count();
            return (int) round(($completed / $total) * 100);
        }

        $total = $this->tasks()->count();
        if ($total === 0) {
            return $this->status === 'completed' ? 100 : 0;
        }
        $completed = $this->tasks()->where('status', 'completed')->count();
        return (int) round(($completed / $total) * 100);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }
}
