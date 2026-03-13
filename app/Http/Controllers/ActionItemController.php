<?php
// app/Http/Controllers/ActionItemController.php
namespace App\Http\Controllers;

use App\Models\ActionItem;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\ActionItemFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActionItemReviewRequested;
use App\Mail\ActionItemVerified;
use App\Mail\ActionItemRevisionRequested;
use App\Mail\ActionItemUpdated;

class ActionItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status');
        $type = $request->get('type', $this->getDefaultType($user));

        // Gunakan with untuk mencegah N+1 query dan handle relasi yang mungkin null
        $query = ActionItem::with([
            'meeting' => function ($query) {
                $query->withTrashed(); // Include meeting yang sudah dihapus
            },
            'meeting.organizer',
            'assignedTo',
            'department'
        ]);

        // Filter berdasarkan user role dan type
        if ($user->isAdmin()) {
            // Admin bisa lihat semua - tidak perlu filter tambahan
            if ($type === 'created') {
                $query->whereHas('meeting', function ($q) use ($user) {
                    $q->where('organizer_id', $user->id);
                });
            } elseif ($type === 'assigned') {
                $query->where('assigned_to', $user->id);
            }
            // Jika type kosong atau 'all', admin lihat semua (tidak ada filter)

        } elseif ($user->isManager()) {
            // Manager hanya bisa lihat yang dibuat dan yang ditugaskan
            if ($type === 'created') {
                $query->whereHas('meeting', function ($q) use ($user) {
                    $q->where('organizer_id', $user->id);
                });
            } elseif ($type === 'assigned') {
                $query->where('assigned_to', $user->id);
            } else {
                // Default untuk manager: gabungan created dan assigned
                $query->where(function ($q) use ($user) {
                    $q->whereHas('meeting', function ($meetingQuery) use ($user) {
                        $meetingQuery->where('organizer_id', $user->id);
                    })->orWhere('assigned_to', $user->id);
                });
            }
        } else {
            // User biasa hanya bisa lihat yang ditugaskan ke mereka
            $query->where('assigned_to', $user->id);
            $type = 'assigned'; // Force type untuk user biasa
        }

        // Filter status
        if ($status && in_array($status, ['pending', 'in_progress', 'waiting_review', 'needs_revision', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $actionItems = $query->orderBy('due_date')->paginate(10);

        // Hitung statistik
        $stats = $this->calculateStats($user);

        return view('action-items.index', compact('actionItems', 'stats', 'type'));
    }

    /**
     * Tentukan default type berdasarkan role user
     */
    private function getDefaultType($user)
    {
        if ($user->isAdmin()) {
            return 'all'; // Admin default lihat semua
        } elseif ($user->isManager()) {
            return 'assigned'; // Manager default lihat yang ditugaskan
        } else {
            return 'assigned'; // User biasa hanya lihat yang ditugaskan
        }
    }

    /**
     * Hitung statistik berdasarkan role user
     */
    private function calculateStats($user)
    {
        $stats = [];

        if ($user->isAdmin()) {
            $stats['all'] = ActionItem::count();
            $stats['created'] = ActionItem::whereHas('meeting', function ($q) use ($user) {
                $q->where('organizer_id', $user->id);
            })->count();
            $stats['assigned'] = ActionItem::where('assigned_to', $user->id)->count();
        } elseif ($user->isManager()) {
            // Untuk manager, hanya hitung created dan assigned
            $stats['created'] = ActionItem::whereHas('meeting', function ($q) use ($user) {
                $q->where('organizer_id', $user->id);
            })->count();
            $stats['assigned'] = ActionItem::where('assigned_to', $user->id)->count();
        } else {
            // Untuk user biasa, hanya assigned
            $stats['assigned'] = ActionItem::where('assigned_to', $user->id)->count();
        }

        return $stats;
    }

    public function store(Request $request, Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk membuat tindak lanjut.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'due_date' => 'required|date|after:today',
            'priority' => 'required|in:1,2,3',
        ]);

        $validated['meeting_id'] = $meeting->id;
        $validated['status'] = 'pending';

        ActionItem::create($validated);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Tindak lanjut berhasil ditambahkan.');
    }

    public function show(ActionItem $actionItem)
    {
        // Check access
        $this->checkActionItemAccess($actionItem);

        // Load relasi dengan pengecekan meeting yang mungkin sudah dihapus
        $actionItem->load([
            'meeting' => function ($query) {
                $query->withTrashed()->with('organizer');
            },
            'assignedTo',
            'department',
            'files.uploader'
        ]);

        return view('action-items.show', compact('actionItem'));
    }

    public function edit(ActionItem $actionItem)
    {
        // Hanya assigned user, organizer meeting, admin, atau manager yang bisa edit
        if (!$this->canEditActionItem($actionItem)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tindak lanjut ini.');
        }

        $actionItem->load(['meeting', 'assignedTo', 'department']);
        $users = User::active()->get();
        $departments = Department::active()->get();

        return view('action-items.edit', compact('actionItem', 'users', 'departments'));
    }

    public function update(Request $request, ActionItem $actionItem)
    {
        // Hanya assigned user, organizer meeting, admin, atau manager yang bisa edit
        if (!$this->canEditActionItem($actionItem)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tindak lanjut ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'due_date' => 'required|date',
            'priority' => 'required|in:1,2,3',
            'status' => 'required|in:pending,in_progress,needs_revision,completed,cancelled',
            'completion_notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && $actionItem->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $actionItem->update($validated);

        // Kirim notifikasi email ke penanggung jawab
        $user = User::find($validated['assigned_to']);
        if ($user) {
            try {
                Mail::to($user->email)->send(new ActionItemUpdated(
                    $actionItem, 
                    $user, 
                    auth()->user()->name, 
                    auth()->user()->email
                ));
            } catch (\Exception $e) {
                \Log::error('Gagal kirim email update action item: ' . $e->getMessage());
            }
        }

        return redirect()->route('action-items.show', $actionItem)
            ->with('success', 'Tindak lanjut berhasil diperbarui dan notifikasi email telah dikirim.');
    }

    public function updateStatus(Request $request, ActionItem $actionItem)
    {
        $user = auth()->user();
        $isAssignee = $actionItem->assigned_to == $user->id;
        $isOrganizer = $actionItem->meeting->organizer_id == $user->id;
        $isAdmin = $user->isAdmin();

        // Cegah orang luar yang iseng (Tapi izinkan Admin)
        if (!$isAssignee && !$isOrganizer && !$isAdmin) {
            abort(403, 'Anda tidak memiliki akses ke fitur ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,waiting_review,needs_revision,completed,cancelled',
            'completion_notes' => 'nullable|string',
            'revision_notes' => 'nullable|string',
        ]);

        // Cek hak akses untuk menutup/membatalkan tugas (Hanya Organizer & Admin yang boleh)
        if (in_array($validated['status'], ['completed', 'cancelled']) && !$isOrganizer && !$isAdmin) {
            return redirect()->back()
                ->with('error', 'Akses Ditolak: Hanya penyelenggara meeting atau Admin yang dapat mengubah status ini.');
        }

        // Jika revisi dikirim (status needs_revision), hapus completion_notes agar bisa diisi ulang nanti
        // dan set status menjadi needs_revision bukan in_progress
        if ($validated['status'] === 'needs_revision' && !empty($validated['revision_notes'])) {
            $validated['completion_notes'] = null;
        } else if ($validated['status'] === 'in_progress' && !empty($validated['revision_notes'])) {
            // Backward compatibility atau jika diset in_progress secara manual
            $validated['completion_notes'] = null;
        }

        // Catat waktu selesai
        if ($validated['status'] === 'completed' && $actionItem->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        // Deteksi transisi status sebelum update
        $prevStatus         = $actionItem->status;
        $isNewWaitingReview = $validated['status'] === 'waiting_review' && $prevStatus !== 'waiting_review';
        $isNewCompleted     = $validated['status'] === 'completed'      && $prevStatus !== 'completed';
        $isNewRevision      = $validated['status'] === 'needs_revision'  && $prevStatus !== 'needs_revision';

        $actionItem->update($validated);

        $hasMeeting   = isset($actionItem->meeting) && $actionItem->meeting;
        $hasOrganizer = $hasMeeting && $actionItem->meeting->organizer;
        $hasAssignee  = $actionItem->assignedTo;
        $currentUser  = auth()->user();

        try {
            // 1. Assignee lapor selesai → notif ke organizer
            if ($isNewWaitingReview && $hasOrganizer) {
                $organizer = $actionItem->meeting->organizer;
                $files     = $actionItem->files()->with('uploader')->get();
                Mail::to($organizer->email)
                    ->queue(new ActionItemReviewRequested($actionItem, $organizer, $currentUser, $files));
            }

            // 2. Organizer verifikasi → notif ke assignee
            if ($isNewCompleted && $hasAssignee && $hasOrganizer) {
                Mail::to($actionItem->assignedTo->email)
                    ->queue(new ActionItemVerified($actionItem, $actionItem->assignedTo, $actionItem->meeting->organizer));
            }

            // 3. Organizer tolak/minta revisi → notif ke assignee
            if ($isNewRevision && $hasAssignee && $hasOrganizer) {
                $notes = $validated['revision_notes'] ?? '-';
                Mail::to($actionItem->assignedTo->email)
                    ->queue(new ActionItemRevisionRequested($actionItem, $actionItem->assignedTo, $actionItem->meeting->organizer, $notes));
            }
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email notifikasi action item: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Status tindak lanjut berhasil diperbarui. Notifikasi email telah dikirim.');
    }

    public function destroy(ActionItem $actionItem)
    {
        try {
            // Check authorization - hanya organizer meeting, admin, atau yang membuat tindak lanjut
            if (!$this->canDeleteActionItem($actionItem)) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus tindak lanjut ini.');
            }

            $meetingId = $actionItem->meeting_id;

            // Hapus file terkait terlebih dahulu (jika ada)
            if ($actionItem->files()->exists()) {
                foreach ($actionItem->files as $file) {
                    Storage::disk('public')->delete($file->file_path);
                    $file->delete();
                }
            }

            // Hapus tindak lanjut
            $actionItem->delete();

            return redirect()->route('meetings.show', $meetingId)
                ->with('success', 'Tindak lanjut berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function canDeleteActionItem($actionItem)
    {
        $user = auth()->user();

        // Admin bisa hapus semua
        if ($user->isAdmin()) {
            return true;
        }

        // Manager bisa hapus jika:
        // - Mereka organizer meeting
        // - Meeting di departemen mereka  
        // - Mereka yang buat tindak lanjut
        if ($user->isManager()) {
            return $actionItem->meeting->organizer_id == $user->id ||
                $actionItem->meeting->department_id == $user->department_id ||
                $actionItem->created_by == $user->id;
        }

        // User biasa hanya bisa hapus jika:
        // - Mereka yang buat tindak lanjut
        // - Mereka organizer meeting
        return $actionItem->created_by == $user->id ||
            $actionItem->meeting->organizer_id == $user->id;
    }

    // Helper methods
    private function checkActionItemAccess($actionItem)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        if ($actionItem->assigned_to === $user->id) {
            return true;
        }

        if ($actionItem->department_id === $user->department_id) {
            return true;
        }

        if ($actionItem->meeting->organizer_id === $user->id) {
            return true;
        }

        abort(403, 'Anda tidak memiliki akses ke tindak lanjut ini.');
    }

    private function canEditActionItem($actionItem)
    {
        $user = auth()->user();
        return $user->isAdmin() ||
            $user->isManager() ||
            $actionItem->assigned_to === $user->id ||
            $actionItem->meeting->organizer_id === $user->id;
    }

    private function canEditMeeting($meeting)
    {
        $user = auth()->user();
        return $user->isAdmin() || $user->isManager() || $meeting->organizer_id === $user->id;
    }

    public function uploadFile(Request $request, ActionItem $actionItem)
    {
        // Validasi bahwa user adalah yang ditugaskan atau memiliki hak akses (admin/manager/organizer)
        if (!$this->canEditActionItem($actionItem)) {
            abort(403, 'Anda tidak diizinkan mengupload file untuk tindak lanjut ini.');
        }

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('action_item_files/' . $actionItem->id, 'public');

            ActionItemFile::create([
                'action_item_id' => $actionItem->id,
                'uploaded_by' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => $request->description
            ]);

            return redirect()->back()
                ->with('success', 'File berhasil diupload.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadFile(ActionItem $actionItem, ActionItemFile $file)
    {
        // Validasi bahwa file ini milik action item yang dimaksud
        if ($file->action_item_id !== $actionItem->id) {
            abort(404);
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function deleteFile(ActionItem $actionItem, ActionItemFile $file)
    {
        // Hanya uploader atau admin/manager/organizer yang bisa hapus file
        if ($file->uploaded_by != auth()->id() && !$this->canEditActionItem($actionItem)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus file ini.');
        }

        // Validasi bahwa file ini milik action item yang dimaksud
        if ($file->action_item_id !== $actionItem->id) {
            abort(404);
        }

        try {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            return redirect()->back()
                ->with('success', 'File berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
