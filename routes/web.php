<?php
// routes/web.php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingTypeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActionItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrashMeetingController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/meeting/{id}', [DashboardController::class, 'getMeetingDetails'])->name('dashboard.meeting.details');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/debug', [DashboardController::class, 'debugData'])->name('dashboard.debug');
    Route::get('/dashboard/real-time-stats', [DashboardController::class, 'getRealTimeStats'])->name('dashboard.real-time-stats');


    // Meetings
    Route::resource('meetings', MeetingController::class);
    
    // Meeting Actions
    Route::post('/meetings/{meeting}/start', [MeetingController::class, 'startMeeting'])->name('meetings.start');
    Route::get('/meetings/{meeting}/running', [MeetingController::class, 'runningMeeting'])->name('meetings.running');
    Route::post('/meetings/{meeting}/complete', [MeetingController::class, 'completeMeeting'])->name('meetings.complete');
    Route::post('/meetings/{meeting}/attendance', [MeetingController::class, 'updateAttendance'])->name('meetings.attendance');
    
    // TAMBAHAN ROUTES UNTUK NOTULENSI

     // Notulensi routes
    Route::post('/meetings/{meeting}/minutes', [MeetingController::class, 'storeMinutes'])
         ->name('meetings.minutes.store');
    Route::put('/meetings/{meeting}/minutes/{minute}', [MeetingController::class, 'updateMinutes'])
         ->name('meetings.minutes.update');
    
    // Assign minute taker
    Route::post('/meetings/{meeting}/assign-minute-taker', [MeetingController::class, 'assignMinuteTaker'])
         ->name('meetings.assign-minute-taker');

    
    // TAMBAHAN ROUTES UNTUK AGENDA & TIMER
    Route::post('/meetings/{meeting}/agendas/{agenda}/start', [MeetingController::class, 'startAgenda'])->name('meetings.agendas.start');
Route::post('/meetings/{meeting}/agendas/{agenda}/complete', [MeetingController::class, 'completeAgenda'])
    ->name('meetings.agendas.complete');;
    Route::post('/meetings/{meeting}/agendas/{agenda}/notes', [MeetingController::class, 'updateAgendaNotes'])->name('meetings.agendas.notes');
    Route::post('/meetings/{meeting}/agendas/{agenda}/notes', [MeetingController::class, 'updateAgendaNotes'])
    ->name('meetings.agendas.notes');

    // File Routes
Route::post('/meetings/{meeting}/files/upload', [MeetingController::class, 'uploadFile'])->name('meetings.files.upload');
Route::get('/meetings/{meeting}/files/{file}/download', [MeetingController::class, 'downloadFile'])->name('meetings.files.download');
Route::get('/meetings/{meeting}/files/{file}/preview', [MeetingController::class, 'previewFile'])->name('meetings.files.preview');
Route::delete('/meetings/{meeting}/files/{file}/delete', [MeetingController::class, 'deleteFile'])->name('meetings.files.delete');

    // TAMBAHAN ROUTES UNTUK ACTION ITEMS DARI MEETING
    Route::post('/meetings/{meeting}/action-items', [MeetingController::class, 'storeActionItem'])->name('meetings.action-items.store');

    // Trash Routes
    Route::prefix('trash')->name('trash.')->group(function () {
        Route::get('/meetings', [TrashMeetingController::class, 'index'])->name('index');
        Route::post('/meetings/{id}/restore', [TrashMeetingController::class, 'restore'])->name('restore');
        Route::delete('/meetings/{id}/force-delete', [TrashMeetingController::class, 'forceDelete'])->name('force-delete');
        Route::delete('/meetings/empty', [TrashMeetingController::class, 'emptyTrash'])->name('empty');
    });

    // Action Items
    Route::resource('action-items', ActionItemController::class)->except(['create', 'store']);
    Route::post('/action-items/{actionItem}/update-status', [ActionItemController::class, 'updateStatus'])->name('action-items.update-status');
    Route::post('/meetings/{meeting}/action-items', [MeetingController::class, 'storeActionItem'])->name('meetings.action-items.store');

    Route::post('/meetings/{meeting}/assign-minute-taker', [MeetingController::class, 'assignMinuteTaker'])
         ->name('meetings.assign-minute-taker');

    Route::post('/action-items/{actionItem}/upload-file', [ActionItemController::class, 'uploadFile'])->name('action-items.upload-file');
    Route::get('/action-items/{actionItem}/download-file/{file}', [ActionItemController::class, 'downloadFile'])->name('action-items.download-file');
    Route::get('/action-items/{actionItem}/preview-file/{file}', [ActionItemController::class, 'previewFile'])->name('action-items.preview-file');
    Route::delete('/action-items/{actionItem}/delete-file/{file}', [ActionItemController::class, 'deleteFile'])->name('action-items.delete-file');

    Route::post('/meetings/{meeting}/assign-action-taker', [MeetingController::class, 'assignActionTaker'])
     ->name('meetings.assign-action-taker');
     Route::post('/meetings/{meeting}/action-items', [MeetingController::class, 'storeActionItem'])
    ->name('meetings.action-items.store');

    // Rate participant
    Route::post('/meetings/{meeting}/participants/{participant}/rate', [MeetingController::class, 'rateParticipant'])
        ->name('meetings.participants.rate');

    Route::delete('/action-items/{actionItem}', [ActionItemController::class, 'destroy'])
    ->name('action-items.destroy');



// routes/web.php - tambahkan route test

Route::get('/test-agenda', function() {
    // Test create meeting dengan multiple agendas
    $meeting = \App\Models\Meeting::create([
        'title' => 'Test Meeting',
        'meeting_type_id' => 1,
        'organizer_id' => 1,
        'department_id' => 1,
        'start_time' => now(),
        'end_time' => now()->addHours(2),
        'location' => 'Test Location',
        'status' => 'scheduled'
    ]);

    // Create multiple agendas
    $agendas = [
        ['topic' => 'Agenda 1', 'duration' => 30, 'presenter' => 'John'],
        ['topic' => 'Agenda 2', 'duration' => 45, 'presenter' => 'Jane'],
        ['topic' => 'Agenda 3', 'duration' => 20, 'presenter' => 'Bob'],
    ];

    foreach ($agendas as $index => $agenda) {
        \App\Models\Agenda::create([
            'meeting_id' => $meeting->id,
            'topic' => $agenda['topic'],
            'duration' => $agenda['duration'],
            'order' => $index,
            'presenter' => $agenda['presenter'],
        ]);
    }

    return "Meeting created with " . $meeting->agendas->count() . " agendas";
});

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('meeting-types', MeetingTypeController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('users', UserController::class);
    });
});