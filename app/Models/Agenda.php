<?php
// app/Models/Agenda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'topic',
        'description',
        'duration',
        'order',
        'presenter',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // Accessors
    public function getElapsedTimeAttribute()
    {
        if (!$this->started_at) return 0;
        
        $endTime = $this->completed_at ?: now();
        return $this->started_at->diffInSeconds($endTime);
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->duration) return 0;
        
        $elapsed = $this->elapsed_time;
        $totalSeconds = $this->duration * 60;
        return max(0, $totalSeconds - $elapsed);
    }

    // PERBAIKAN: Tambahkan formatters untuk waktu
    public function getElapsedTimeFormattedAttribute()
    {
        $seconds = $this->elapsed_time;
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getRemainingTimeFormattedAttribute()
    {
        $seconds = $this->remaining_time;
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getIsActiveAttribute()
    {
        return $this->started_at && !$this->completed_at;
    }

    public function getIsCompletedAttribute()
    {
        return (bool) $this->completed_at;
    }

    public function getProgressPercentageAttribute()
    {
        if (!$this->duration || !$this->started_at) return 0;
        
        $totalSeconds = $this->duration * 60;
        $elapsed = $this->elapsed_time;
        
        return min(100, ($elapsed / $totalSeconds) * 100);
    }

    // PERBAIKAN: Tambahkan accessor untuk status
    public function getStatusAttribute()
    {
        if ($this->completed_at) {
            return 'completed';
        } elseif ($this->started_at) {
            return 'active';
        } else {
            return 'pending';
        }
    }

    // PERBAIKAN: Tambahkan accessor untuk display duration
    public function getDurationFormattedAttribute()
    {
        if (!$this->duration) return '-';
        return $this->duration . ' menit';
    }

    // Methods
    public function start()
    {
        return $this->update([
            'started_at' => now(),
            'completed_at' => null
        ]);
    }

    public function complete()
    {
        return $this->update(['completed_at' => now()]);
    }

    public function updateNotes($notes)
    {
        return $this->update(['notes' => $notes]);
    }

    // PERBAIKAN: Tambahkan method untuk reset agenda
    public function reset()
    {
        return $this->update([
            'started_at' => null,
            'completed_at' => null,
            'notes' => null
        ]);
    }

    // PERBAIKAN: Tambahkan method untuk cek apakah bisa dimulai
    public function canStart()
    {
        return !$this->started_at && !$this->completed_at;
    }

    // PERBAIKAN: Tambahkan method untuk cek apakah bisa diselesaikan
    public function canComplete()
    {
        return $this->started_at && !$this->completed_at;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('started_at')->whereNull('completed_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('started_at')->whereNull('completed_at');
    }

    public function scopeByOrder($query)
    {
        return $query->orderBy('order');
    }

    // PERBAIKAN: Tambahkan scope untuk agenda yang sedang berjalan
    public function scopeRunning($query)
    {
        return $query->active()->orderBy('started_at', 'desc');
    }

    // PERBAIKAN: Tambahkan scope untuk meeting tertentu
    public function scopeForMeeting($query, $meetingId)
    {
        return $query->where('meeting_id', $meetingId);
    }

    // PERBAIKAN: Tambahkan method untuk mendapatkan agenda berikutnya
    public function getNextAgenda()
    {
        return self::where('meeting_id', $this->meeting_id)
            ->where('order', '>', $this->order)
            ->pending()
            ->orderBy('order')
            ->first();
    }

    // PERBAIKAN: Tambahkan method untuk mendapatkan agenda sebelumnya
    public function getPreviousAgenda()
    {
        return self::where('meeting_id', $this->meeting_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    // PERBAIKAN: Tambahkan method untuk estimasi waktu selesai
    public function getEstimatedCompletionTimeAttribute()
    {
        if (!$this->started_at || !$this->duration) return null;
        
        return $this->started_at->addMinutes($this->duration);
    }

    // PERBAIKAN: Tambahkan method untuk cek apakah overtime
    public function getIsOvertimeAttribute()
    {
        if (!$this->started_at || !$this->duration || $this->completed_at) return false;
        
        return now()->greaterThan($this->estimated_completion_time);
    }

    // PERBAIKAN: Tambahkan method untuk mendapatkan overtime duration
    public function getOvertimeDurationAttribute()
    {
        if (!$this->is_overtime) return 0;
        
        return now()->diffInMinutes($this->estimated_completion_time);
    }
}