<?php
// app/Models/MeetingParticipant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'role',
        'is_required',
        'attended',
        'excuse',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'attended' => 'boolean',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'chairperson' => 'Pemimpin Rapat',
            'secretary' => 'Notulis',
            'participant' => 'Peserta',
            default => 'Tidak Diketahui'
        };
    }
}