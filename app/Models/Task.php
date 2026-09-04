<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'type',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
        'project_id',
        'assigned_to',
        'attachment',
        'estimated_hours',
    ];

    protected function casts(): array
    {
        return [
            'estimated_hours' => 'float',
        ];
    }

    public const TYPES = [
        'feature' => 'Feature',
        'bug_fix' => 'Bug Fix',
        'maintenance' => 'Maintenance',
        'support' => 'Support Ticket',
        'cr' => 'Change Request',
    ];

    public function getTypeDisplayAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucwords(str_replace('_', ' ', $this->type ?? 'feature'));
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'bug_fix' => 'badge-danger',
            'maintenance' => 'badge-warning',
            'support' => 'badge-info',
            'cr' => 'badge-secondary',
            default => 'badge-primary',
        };
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }

    public function getAttachmentNameAttribute()
    {
        return $this->attachment ? basename($this->attachment) : null;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'asc');
    }

    public function timeLogs()
    {
        return $this->hasMany(TaskTimeLog::class)->orderBy('logged_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function getTotalLoggedHoursAttribute(): float
    {
        if ($this->relationLoaded('timeLogs')) {
            return (float) round($this->timeLogs->sum('hours'), 2);
        }

        return (float) round($this->timeLogs()->sum('hours'), 2);
    }

    public function getTimeProgressPercentageAttribute(): int
    {
        $estimated = (float) $this->estimated_hours;
        if ($estimated <= 0) {
            return 0;
        }

        return (int) min(round(($this->total_logged_hours / $estimated) * 100), 100);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        $estimated = (float) $this->estimated_hours;
        return $estimated > 0 && $this->total_logged_hours > $estimated;
    }
}

