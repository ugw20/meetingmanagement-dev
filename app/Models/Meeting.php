<?php
// app/Models/Meeting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Meeting extends Model
{
        use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'meeting_type_id',
        'organizer_id',
        'department_id',
        'start_time',
        'end_time',
        'location',
        'status',
        'meeting_link',
        'is_online',
        'started_at',
        'ended_at',
        'meeting_platform',
        'meeting_id',
        'meeting_password',
        'assigned_minute_taker_id', // Tambahkan ini
        'assigned_action_taker_id', // Tambahkan ini
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_online' => 'boolean',
         'deleted_at' => 'datetime', // Tambahkan ini
    ];

    // Relationships
    public function meetingType()
    {
        return $this->belongsTo(MeetingType::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    public function files()
    {
        return $this->hasMany(MeetingFile::class);
    }

    public function minutes()
    {
        return $this->hasOne(MeetingMinute::class);
    }

    public function actionItems()
    {
        return $this->hasMany(ActionItem::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }

    // Methods
    public function isUpcoming()
    {
        return $this->start_time > now();
    }

    public function isOngoing()
    {
        return $this->status === 'ongoing';
    }

    public function isPast()
    {
        return $this->end_time < now();
    }

    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getElapsedTimeAttribute()
    {
        if (!$this->started_at) return 0;
        
        $endTime = $this->ended_at ?: now();
        return $this->started_at->diffInSeconds($endTime);
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->started_at) return $this->duration * 60;
        
        $elapsed = $this->elapsed_time;
        $totalSeconds = $this->duration * 60;
        return max(0, $totalSeconds - $elapsed);
    }

    public function getParticipantCountAttribute()
    {
        return $this->participants()->count();
    }

    public function getCompletedActionItemsCountAttribute()
    {
        return $this->actionItems()->where('status', 'completed')->count();
    }

    public function getPendingActionItemsCountAttribute()
    {
        return $this->actionItems()->whereIn('status', ['pending', 'in_progress'])->count();
    }

    // Method untuk memulai meeting
    public function startMeeting()
    {
        $this->update([
            'status' => 'ongoing',
            'started_at' => now(),
            'ended_at' => null,
        ]);
    }

    // Method untuk menyelesaikan meeting
    public function completeMeeting()
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
    }

    // Timer data methods
    public function getMeetingTimerDataAttribute()
    {
        if (!$this->started_at) {
            $totalDuration = $this->duration * 60;
            return [
                'elapsed' => 0,
                'remaining' => $totalDuration,
                'total' => $totalDuration,
                'is_running' => false
            ];
        }

        $endTime = $this->ended_at ?: now();
        $elapsed = $this->started_at->diffInSeconds($endTime);
        
        $total = $this->duration * 60;
        $remaining = max(0, $total - $elapsed);
        
        return [
            'elapsed' => $elapsed,
            'remaining' => $remaining,
            'total' => $total,
            'is_running' => $this->status === 'ongoing' && !$this->ended_at
        ];
    }

    // Method untuk menghitung progress persentase
    public function getProgressPercentageAttribute()
    {
        if (!$this->started_at) return 0;
        
        $totalDuration = $this->duration * 60; // dalam detik
        $elapsed = $this->elapsed_time;
        return $totalDuration > 0 ? min(100, ($elapsed / $totalDuration) * 100) : 0;
    }

    // Method untuk mendapatkan data timer real-time
    public function getRealTimeTimerData()
    {
        $meetingData = $this->getMeetingTimerDataAttribute();
        
        return [
            'meeting' => $meetingData,
            'overall_progress' => $this->progress_percentage,
            'meeting_status' => $this->status,
        ];
    }

    // Method untuk mengecek apakah meeting bisa dimulai
    public function canStartMeeting()
    {
        return $this->status === 'scheduled';
    }

    // Method untuk mendapatkan waktu yang sudah berjalan dalam format jam:menit:detik
    public function getElapsedTimeFormattedAttribute()
    {
        $seconds = $this->elapsed_time;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    // Method untuk mendapatkan waktu tersisa dalam format jam:menit:detik
    public function getRemainingTimeFormattedAttribute()
    {
        $seconds = $this->remaining_time;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function assignedMinuteTaker()
    {
        return $this->belongsTo(User::class, 'assigned_minute_taker_id');
    }

    // Tambahkan relationship
public function assignedActionTaker()
{
    return $this->belongsTo(User::class, 'assigned_action_taker_id');
}

}