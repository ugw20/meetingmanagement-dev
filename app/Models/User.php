<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @method bool isAdmin()
 * @method bool isManager()
 * @method bool canManageMeetings()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'position',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function organizedMeetings()
    {
        return $this->hasMany(Meeting::class, 'organizer_id');
    }

    public function meetingParticipants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    public function assignedActions()
    {
        return $this->hasMany(ActionItem::class, 'assigned_to');
    }

    public function takenMinutes()
    {
        return $this->hasMany(MeetingMinute::class, 'minute_taker_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function canManageMeetings()
{
    return $this->isAdmin() || $this->isManager();
}


public function isSecretaryForMeeting($meeting)
{
    return $this->participations()
        ->where('meeting_id', $meeting->id)
        ->where('role', 'secretary')
        ->exists();
}

// Cek apakah user adalah minute taker untuk meeting tertentu
    public function isMinuteTakerForMeeting($meeting)
    {
        return $meeting->assigned_minute_taker_id === $this->id;
    }

    // Cek apakah user bisa mengelola notulensi meeting
    public function canManageMinutes($meeting)
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        if ($meeting->organizer_id === $this->id) {
            return true;
        }

        if ($meeting->assigned_minute_taker_id === $this->id) {
            return true;
        }

        return false;
    }

}