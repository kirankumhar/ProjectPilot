<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'action',
        'description',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Record an activity log entry safely.
     */
    public static function record(int $projectId, string $action, string $description, ?int $taskId = null, array $properties = [], ?int $userId = null): ?self
    {
        try {
            return self::create([
                'user_id' => $userId ?? Auth::id(),
                'project_id' => $projectId,
                'task_id' => $taskId,
                'action' => $action,
                'description' => $description,
                'properties' => !empty($properties) ? $properties : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to record activity log: ' . $e->getMessage());
            return null;
        }
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'project_created' => 'fa fa-folder-open',
            'project_updated' => 'fa fa-edit',
            'task_created' => 'fa fa-plus-circle',
            'task_status_changed' => 'fa fa-exchange',
            'task_reassigned' => 'fa fa-user-plus',
            'comment_added' => 'fa fa-comments',
            'time_logged' => 'fa fa-clock-o',
            'checklist_completed' => 'fa fa-check-square-o',
            default => 'fa fa-bell-o',
        };
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'project_created', 'task_created' => 'badge-primary',
            'task_status_changed' => 'badge-info',
            'checklist_completed' => 'badge-success',
            'time_logged' => 'badge-warning',
            'comment_added' => 'badge-secondary',
            default => 'badge-light',
        };
    }
}
