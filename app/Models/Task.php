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
    ];

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
}

