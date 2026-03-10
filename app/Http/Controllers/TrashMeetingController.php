<?php
// app/Http/Controllers/TrashMeetingController.php
namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrashMeetingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admin melihat semua meeting yang dihapus
            $deletedMeetings = Meeting::onlyTrashed()
                ->with(['meetingType', 'organizer', 'department'])
                ->latest('deleted_at')
                ->paginate(10);
        } else {
            // Manager hanya melihat meeting yang mereka buat dan dihapus
            $deletedMeetings = Meeting::onlyTrashed()
                ->where('organizer_id', $user->id)
                ->with(['meetingType', 'organizer', 'department'])
                ->latest('deleted_at')
                ->paginate(10);
        }

        return view('trash.index', compact('deletedMeetings'));
    }

    public function restore($id)
    {
        $meeting = Meeting::onlyTrashed()->findOrFail($id);
        
        // Cek authorization
        if (!auth()->user()->isAdmin() && $meeting->organizer_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $meeting->restore();

        return redirect()->route('trash.index')
            ->with('success', 'Meeting berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $meeting = Meeting::onlyTrashed()->findOrFail($id);
        
        // Cek authorization
        if (!auth()->user()->isAdmin() && $meeting->organizer_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($meeting) {
            // Hapus relasi terkait jika ada
            $meeting->participants()->delete();
            $meeting->files()->delete();
            $meeting->actionItems()->delete();
            
            // Hapus permanen
            $meeting->forceDelete();
        });

        return redirect()->route('trash.index')
            ->with('success', 'Meeting berhasil dihapus permanen.');
    }

    public function emptyTrash()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $query = Meeting::onlyTrashed();
        } else {
            $query = Meeting::onlyTrashed()->where('organizer_id', $user->id);
        }

        $count = $query->count();
        
        DB::transaction(function () use ($query) {
            $query->get()->each(function ($meeting) {
                $meeting->participants()->delete();
                $meeting->files()->delete();
                $meeting->actionItems()->delete();
                $meeting->forceDelete();
            });
        });

        return redirect()->route('trash.index')
            ->with('success', "{$count} meeting berhasil dihapus permanen.");
    }
}