<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

use App\Http\Controllers\ChatController;

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::resource('tasks', TaskController::class);

    // Task Comments Routes
    Route::post('/tasks/{task}/comments', [\App\Http\Controllers\TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');

    // Team Chat Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/fetch/{user}', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::delete('/chat/{message}', [ChatController::class, 'destroy'])->name('chat.destroy');

    // In-App Notifications Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'readAndRedirect'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Interactive Calendar Routes
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [\App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events');

    // Time Tracking & Work Logs Routes
    Route::get('/timesheets', [\App\Http\Controllers\TimeLogController::class, 'index'])->name('timesheets.index');
    Route::post('/tasks/{task}/time-logs', [\App\Http\Controllers\TimeLogController::class, 'store'])->name('tasks.time-logs.store');
    Route::delete('/time-logs/{timeLog}', [\App\Http\Controllers\TimeLogController::class, 'destroy'])->name('tasks.time-logs.destroy');

    // Task Checklists & Subtasks Routes
    Route::post('/tasks/{task}/checklists', [\App\Http\Controllers\TaskChecklistController::class, 'store'])->name('tasks.checklists.store');
    Route::patch('/checklists/{checklist}/toggle', [\App\Http\Controllers\TaskChecklistController::class, 'toggle'])->name('tasks.checklists.toggle');
    Route::delete('/checklists/{checklist}', [\App\Http\Controllers\TaskChecklistController::class, 'destroy'])->name('tasks.checklists.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});
