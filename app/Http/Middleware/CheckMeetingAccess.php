<?php
// app/Http/Middleware/CheckMeetingAccess.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Meeting;

class CheckMeetingAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $meetingId = $request->route('meeting') ?? $request->route('id');
        $meeting = Meeting::findOrFail($meetingId);
        
        $user = auth()->user();
        
        // Admin dan Manager bisa akses semua meeting
        if ($user->isAdmin() || $user->isManager()) {
            return $next($request);
        }
        
        // Organizer meeting bisa akses
        if ($meeting->organizer_id === $user->id) {
            return $next($request);
        }
        
        // Participant meeting bisa akses
        if ($meeting->participants()->where('user_id', $user->id)->exists()) {
            return $next($request);
        }
        
        // User dalam department yang sama bisa akses
        if ($meeting->department_id === $user->department_id) {
            return $next($request);
        }
        
        abort(403, 'Anda tidak memiliki akses ke meeting ini.');
    }
}