<?php
// app/Models/MeetingMinute.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'minute_taker_id',
        'content',
        'decisions',
        'is_finalized',
        'finalized_at',
    ];

    protected $casts = [
        'decisions' => 'array',
        'is_finalized' => 'boolean',
        'finalized_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function minuteTaker()
    {
        return $this->belongsTo(User::class, 'minute_taker_id');
    }

    public function finalize()
    {
        $this->update([
            'is_finalized' => true,
            'finalized_at' => now()
        ]);
    }

    // Helper method untuk mengecek apakah user bisa edit notulensi
    public function canEdit($user = null)
    {
        $user = $user ?: auth()->user();
        
        if ($this->is_finalized) {
            return false;
        }

        // Organizer meeting bisa edit
        if ($this->meeting->organizer_id === $user->id) {
            return true;
        }

        // Minute taker yang ditunjuk bisa edit
        if ($this->meeting->assigned_minute_taker_id === $user->id) {
            return true;
        }

        // Pembuat notulensi bisa edit
        if ($this->minute_taker_id === $user->id) {
            return true;
        }

        // Admin dan manager bisa edit
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        return false;
    }
}