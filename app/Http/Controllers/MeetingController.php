<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\MeetingType;
use App\Models\User;
use App\Models\Department;
use App\Models\MeetingParticipant;
use App\Models\MeetingFile;
use App\Models\MeetingMinute;
use App\Models\ActionItem;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingInvitation;
use App\Mail\MinuteTakerAssigned;
use App\Mail\ActionTakerAssigned;
use App\Mail\ActionItemAssigned;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get filter parameters dengan tambahan type
        $filters = [
            'status' => $request->get('status'),
            'department_id' => $request->get('department_id'),
            'meeting_type_id' => $request->get('meeting_type_id'),
            'sort' => $request->get('sort', 'desc'),
            'type' => $request->get('type', 'all'), // all, created, participating
        ];

        // Base query dengan eager loading
        $query = Meeting::with(['meetingType', 'organizer', 'department']);

        // Filter berdasarkan hak akses user
        if (!$user->isAdmin()) {
            if ($user->isManager()) {
                $query->where(function($q) use ($user) {
                    $q->where('organizer_id', $user->id)
                    ->orWhereHas('participants', function($participantQuery) use ($user) {
                        $participantQuery->where('user_id', $user->id);
                    });
                });
            } else {
                $query->where(function($q) use ($user) {
                    $q->whereHas('participants', function($participantQuery) use ($user) {
                        $participantQuery->where('user_id', $user->id);
                    })
                    ->orWhere('organizer_id', $user->id)
                    ->orWhere('department_id', $user->department_id);
                });
            }
        }

        // Filter tambahan untuk admin/manager - PENGELOMPOKAN BARU
        if (($user->isAdmin() || $user->isManager()) && $filters['type'] !== 'all') {
            if ($filters['type'] === 'created') {
                // Hanya meeting yang dibuat oleh user
                $query->where('organizer_id', $user->id);
            } elseif ($filters['type'] === 'participating') {
                // Hanya meeting yang diikuti user (bukan sebagai organizer)
                $query->whereHas('participants', function($participantQuery) use ($user) {
                    $participantQuery->where('user_id', $user->id);
                })->where('organizer_id', '!=', $user->id);
            }
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['meeting_type_id'])) {
            $query->where('meeting_type_id', $filters['meeting_type_id']);
        }

        // Sorting
        $sortOrder = $filters['sort'] === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortOrder);

        // Execute query dengan pagination
        $meetings = $query->paginate(10)->appends($request->query());

        // Get counts for summary cards dan tabs
        $statusCountsQuery = Meeting::query();
        $stats = [];
        
        // Apply hak akses yang sama untuk statistik
        if (!$user->isAdmin()) {
            if ($user->isManager()) {
                $statusCountsQuery->where(function($q) use ($user) {
                    $q->where('organizer_id', $user->id)
                    ->orWhereHas('participants', function($participantQuery) use ($user) {
                        $participantQuery->where('user_id', $user->id);
                    });
                });
            } else {
                $statusCountsQuery->where(function($q) use ($user) {
                    $q->whereHas('participants', function($participantQuery) use ($user) {
                        $participantQuery->where('user_id', $user->id);
                    })
                    ->orWhere('organizer_id', $user->id)
                    ->orWhere('department_id', $user->department_id);
                });
            }
        }

        $statusCounts = $statusCountsQuery->selectRaw('status, count(*) as count')
                                        ->groupBy('status')
                                        ->pluck('count', 'status')
                                        ->toArray();

        // Hitung statistik untuk tabs (hanya untuk admin/manager)
        if ($user->isAdmin() || $user->isManager()) {
            $stats['all'] = $statusCountsQuery->clone()->count();
            $stats['created'] = Meeting::where('organizer_id', $user->id)->count();
            $stats['participating'] = Meeting::whereHas('participants', function($participantQuery) use ($user) {
                $participantQuery->where('user_id', $user->id);
            })->where('organizer_id', '!=', $user->id)->count();
        }

        // Get data untuk dropdown filters
        $departments = Department::active()->get();
        $meetingTypes = MeetingType::active()->get();

        return view('meetings.index', compact(
            'meetings', 
            'statusCounts', 
            'departments', 
            'meetingTypes',
            'filters',
            'stats'
        ));
    }

    public function create()
    {
        if (!auth()->user()->canManageMeetings()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat meeting.');
        }

        $meetingTypes = MeetingType::active()->get();
        $departments = Department::active()->get();
        $users = User::active()->get();
        
        return view('meetings.create', compact('meetingTypes', 'departments', 'users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageMeetings()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat meeting.');
        }

        // DEBUG: Lihat data yang dikirim
        \Log::info('Meeting Store Request:', $request->all());

        // VALIDASI DASAR TANPA AGENDAS
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'department_id' => 'required|exists:departments,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'required_without:is_online|nullable|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // CREATE MEETING
            $meeting = Meeting::create([
                'title' => $validated['title'],
                'description' => $request->description,
                'meeting_type_id' => $validated['meeting_type_id'],
                'organizer_id' => auth()->id(),
                'department_id' => $validated['department_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'location' => $validated['location'],
                'is_online' => $request->boolean('is_online'),
                'meeting_link' => $request->meeting_link,
                'meeting_platform' => $request->meeting_platform,
                'meeting_id' => $request->meeting_id,
                'meeting_password' => $request->meeting_password,
                'status' => 'scheduled',
            ]);

            // ADD PARTICIPANTS
            foreach ($validated['participants'] as $participantId) {
                MeetingParticipant::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $participantId,
                    'role' => 'participant',
                ]);
            }

            // ADD ORGANIZER AS CHAIRPERSON
            MeetingParticipant::create([
                'meeting_id' => $meeting->id,
                'user_id' => auth()->id(),
                'role' => 'chairperson',
            ]);

            DB::commit();

            $invitedUsers = User::whereIn('id', $validated['participants'])->get();
            $delay = 0; // Initialize delay in seconds
            
            $senderName = auth()->user()->name;
            $senderEmail = auth()->user()->email;

            foreach ($invitedUsers as $invitedUser) {
                // Queue the email with a 20-second incremental delay to safely bypass Mailtrap rate limits
                Mail::to($invitedUser->email)
                    ->later(now()->addSeconds($delay), new MeetingInvitation($meeting, $invitedUser, $senderName, $senderEmail));
                $delay += 20;
            }

            return redirect()->route('meetings.index')
                ->with('success', 'Meeting berhasil dibuat dan notifikasi undangan telah dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Meeting Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Meeting $meeting)
    {
        $this->checkMeetingAccess($meeting);

        $meeting->load([
            'meetingType',
            'organizer',
            'department',
            'participants.user',
            'files',
            'minutes.minuteTaker',
            'actionItems.assignedTo',
            'actionItems.department',
             'assignedMinuteTaker' // PASTIKAN INI ADA
        ]);

        // Filter action items berdasarkan hak akses
        if (!auth()->user()->canManageMeetings() && $meeting->organizer_id != auth()->id()) {
            // Untuk partisipan biasa, hanya tampilkan action items yang ditugaskan ke mereka
            $meeting->setRelation('actionItems', $meeting->actionItems->where('assigned_to', auth()->id()));
        }

        $users = User::active()->get();
        $departments = Department::active()->get();

        return view('meetings.show', compact('meeting', 'users', 'departments'));
    }

    public function edit(Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit meeting ini.');
        }

        $meetingTypes = MeetingType::active()->get();
        $departments = Department::active()->get();
        $users = User::active()->get();
        $currentParticipants = $meeting->participants->pluck('user_id')->toArray();

        return view('meetings.edit', compact('meeting', 'meetingTypes', 'departments', 'users', 'currentParticipants'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit meeting ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'department_id' => 'required|exists:departments,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'required_without:is_online|nullable|string|max:255',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|url',
            'meeting_platform' => 'nullable|string',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $meeting->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'meeting_type_id' => $validated['meeting_type_id'],
                'department_id' => $validated['department_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'location' => $validated['location'],
                'is_online' => $validated['is_online'] ?? false,
                'meeting_link' => $validated['meeting_link'] ?? null,
                'meeting_platform' => $validated['meeting_platform'] ?? null,
                'meeting_id' => $validated['meeting_id'] ?? null,
                'meeting_password' => $validated['meeting_password'] ?? null,
            ]);

            // Dapatkan ID peserta yang sudah ada sebelumnya
            $existingParticipantIds = $meeting->participants()
                ->where('role', 'participant')
                ->pluck('user_id')
                ->toArray();

            // Hapus peserta yang lama, lalu tambahkan ulang sesuai pilihan baru di form
            $meeting->participants()->where('role', 'participant')->delete();
            
            $newParticipantIds = [];
            foreach ($validated['participants'] as $participantId) {
                if ($participantId != auth()->id()) {
                    MeetingParticipant::create([
                        'meeting_id' => $meeting->id,
                        'user_id' => $participantId,
                        'role' => 'participant',
                        'is_required' => true,
                    ]);

                    if (!in_array($participantId, $existingParticipantIds)) {
                        $newParticipantIds[] = $participantId;
                    }
                }
            }

            DB::commit();

            // Kirim notifikasi email ke peserta yang baru ditambahkan
            if (!empty($newParticipantIds)) {
                $newUsers = User::whereIn('id', $newParticipantIds)->get();
                $delay = 0;
                
                $senderName = auth()->user()->name;
                $senderEmail = auth()->user()->email;

                foreach ($newUsers as $newUser) {
                    Mail::to($newUser->email)
                        ->later(now()->addSeconds($delay), new MeetingInvitation($meeting, $newUser, $senderName, $senderEmail));
                    $delay += 20;
                }
            }

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Meeting berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update meeting error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus meeting ini.');
        }

        try {
            $meeting->delete();
            return redirect()->route('meetings.index')
                ->with('success', 'Meeting berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Timer Methods
    public function startMeeting(Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk memulai meeting ini.');
        }

        // Validasi apakah meeting sudah bisa dimulai
        if ($meeting->status !== 'scheduled') {
            return redirect()->back()
                ->with('error', 'Meeting sudah dimulai atau selesai.');
        }

        $meeting->startMeeting();
        
        return redirect()->route('meetings.running', $meeting)
            ->with('success', 'Meeting telah dimulai.');
    }

    public function completeMeeting(Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan meeting ini.');
        }

        if ($meeting->status !== 'ongoing') {
            return redirect()->back()
                ->with('error', 'Meeting belum dimulai atau sudah selesai.');
        }

        $meeting->completeMeeting();
        
        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting telah selesai.');
    }

    // Notulensi Methods
    public function storeMinutes(Request $request, Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting) && !$this->isAssignedMinuteTaker($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk membuat notulensi.');
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'decisions' => 'nullable|string',
            'is_finalized' => 'boolean',
        ]);

        $decisions = $validated['decisions'] ? 
            array_filter(array_map('trim', explode("\n", $validated['decisions']))) : 
            null;

        $minuteData = [
            'meeting_id' => $meeting->id,
            'minute_taker_id' => auth()->id(),
            'content' => $validated['content'],
            'decisions' => $decisions,
            'is_finalized' => $validated['is_finalized'] ?? false,
        ];

        if ($validated['is_finalized'] ?? false) {
            $minuteData['finalized_at'] = now();
        }

        MeetingMinute::create($minuteData);

        return redirect()->back()
            ->with('success', 'Notulensi berhasil disimpan.');
    }

    public function updateMinutes(Request $request, Meeting $meeting, MeetingMinute $minute)
    {
        if (!$this->canEditMeeting($meeting) && !$this->isAssignedMinuteTaker($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit notulensi.');
        }

        if ($minute->is_finalized) {
            return redirect()->back()
                ->with('error', 'Notulensi sudah difinalisasi dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'decisions' => 'nullable|string',
            'is_finalized' => 'boolean',
        ]);

        $decisions = $validated['decisions'] ? 
            array_filter(array_map('trim', explode("\n", $validated['decisions']))) : 
            null;

        $updateData = [
            'content' => $validated['content'],
            'decisions' => $decisions,
        ];

        if ($validated['is_finalized'] ?? false) {
            $updateData['is_finalized'] = true;
            $updateData['finalized_at'] = now();
        }

        $minute->update($updateData);

        return redirect()->back()
            ->with('success', 'Notulensi berhasil diperbarui.');
    }

    // Assign Minute Taker
    public function assignMinuteTaker(Request $request, Meeting $meeting)
    {
        if (!$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk menunjuk penulis notulensi.');
        }

        $validated = $request->validate([
            'minute_taker_id' => 'required|exists:users,id',
        ]);

        // Update atau create minute taker assignment
        $meeting->update([
            'assigned_minute_taker_id' => $validated['minute_taker_id']
        ]);

        $user = User::find($validated['minute_taker_id']);
        if ($user) {
            Mail::to($user->email)->send(new MinuteTakerAssigned(
                $meeting, 
                $user, 
                auth()->user()->name, 
                auth()->user()->email
            ));
        }

        return redirect()->back()
            ->with('success', 'Penulis notulensi berhasil ditunjuk dan notifikasi telah dikirim.');
    }

    // Action Items Methods
// Action Items Methods
public function storeActionItem(Request $request, Meeting $meeting)
{
    // PERBAIKAN: Izinkan organizer DAN assigned action taker
    if (!$this->canEditMeeting($meeting) && !$this->isAssignedActionTaker($meeting)) {
        abort(403, 'Anda tidak memiliki akses untuk menambah tindak lanjut.');
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'assigned_to' => 'required|exists:users,id',
        'department_id' => 'required|exists:departments,id',
        'due_date' => 'required|date|after:today',
        'priority' => 'required|in:1,2,3',
    ]);

    $actionItem = ActionItem::create([
        'meeting_id' => $meeting->id,
        'title' => $validated['title'],
        'description' => $validated['description'],
        'assigned_to' => $validated['assigned_to'],
        'department_id' => $validated['department_id'],
        'due_date' => $validated['due_date'],
        'priority' => $validated['priority'],
        'status' => 'pending',
        'created_by' => auth()->id(),
    ]);

    $user = User::find($validated['assigned_to']);
    if ($user) {
        Mail::to($user->email)->send(new ActionItemAssigned(
            $actionItem, 
            $user, 
            auth()->user()->name, 
            auth()->user()->email
        ));
    }

    return redirect()->back()
        ->with('success', 'Tindak lanjut berhasil ditambahkan dan notifikasi telah dikirim.');
}

    // File Methods
    public function uploadFile(Request $request, Meeting $meeting)
    {
        $this->checkMeetingAccess($meeting);

        $request->validate([
            'file' => 'required|file|max:10240',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('meeting_files/' . $meeting->id, 'public');

            MeetingFile::create([
                'meeting_id' => $meeting->id,
                'uploaded_by' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => $request->description,
            ]);

            return redirect()->back()
                ->with('success', 'File berhasil diupload.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadFile(Meeting $meeting, MeetingFile $file)
    {
        $this->checkMeetingAccess($meeting);
        
        // Validasi bahwa file ini milik meeting yang dimaksud
        if ($file->meeting_id !== $meeting->id) {
            abort(404);
        }
        
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function deleteFile(Meeting $meeting, MeetingFile $file)
    {
        if ($file->uploaded_by != auth()->id() && !$this->canEditMeeting($meeting)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus file ini.');
        }

        // Validasi bahwa file ini milik meeting yang dimaksud
        if ($file->meeting_id !== $meeting->id) {
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

    // Running Meeting Page
    // Running Meeting Page
public function runningMeeting(Meeting $meeting)
{
    // PERBAIKAN: Izinkan akses untuk organizer, assigned minute taker, DAN assigned action taker
    if (!$this->canEditMeeting($meeting) && 
        !$this->isAssignedMinuteTaker($meeting) && 
        !$this->isAssignedActionTaker($meeting)) {
        abort(403, 'Anda tidak memiliki akses untuk mengelola meeting ini.');
    }

    if ($meeting->status !== 'ongoing') {
        return redirect()->route('meetings.show', $meeting)
            ->with('error', 'Meeting belum dimulai atau sudah selesai.');
    }

    // Load relasi
    $meeting->load([
        'participants.user', 
        'minutes.minuteTaker',
        'actionItems.assignedTo',
        'actionItems.department',
        'assignedMinuteTaker',
        'assignedActionTaker',
        'files.uploader',
        'meetingType',
        'department',
        'organizer'
    ]);

    // Set default untuk relasi yang mungkin null
    if (!$meeting->actionItems) {
        $meeting->setRelation('actionItems', collect());
    }
    if (!$meeting->files) {
        $meeting->setRelation('files', collect());
    }
    if (!$meeting->minutes) {
        $meeting->setRelation('minutes', null);
    }

    $participants = $meeting->participants->pluck('user');
    $departments = Department::active()->get();
    $users = User::active()->get();

    return view('meetings.running', compact('meeting', 'participants', 'departments', 'users'));
}

        // Helper methods
    private function checkMeetingAccess($meeting)
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        // Untuk manajer: hanya meeting yang mereka buat atau ikuti
        if ($user->isManager()) {
            if ($meeting->organizer_id === $user->id || 
                $meeting->participants()->where('user_id', $user->id)->exists()) {
                return true;
            }
            abort(403, 'Anda tidak memiliki akses ke meeting ini.');
        }
        
        // Untuk user biasa
        if ($meeting->organizer_id === $user->id) {
            return true;
        }
        
        if ($meeting->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }
        
        if ($meeting->department_id === $user->department_id) {
            return true;
        }
        
        abort(403, 'Anda tidak memiliki akses ke meeting ini.');
    }

    private function canEditMeeting($meeting)
    {
        $user = auth()->user();
        return $user->isAdmin() || $user->isManager() || $meeting->organizer_id === $user->id;
    }

    private function isAssignedMinuteTaker($meeting)
    {
        return $meeting->assigned_minute_taker_id === auth()->id();
    }

    public function assignActionTaker(Request $request, Meeting $meeting)
{
    if (!$this->canEditMeeting($meeting)) {
        abort(403, 'Anda tidak memiliki akses untuk menunjuk penulis tindak lanjut.');
    }

    $validated = $request->validate([
        'action_taker_id' => 'required|exists:users,id',
    ]);

    // Update atau create action taker assignment
    $meeting->update([
        'assigned_action_taker_id' => $validated['action_taker_id']
    ]);

    $user = User::find($validated['action_taker_id']);
    if ($user) {
        Mail::to($user->email)->send(new ActionTakerAssigned(
            $meeting, 
            $user, 
            auth()->user()->name, 
            auth()->user()->email
        ));
    }

    return redirect()->back()
        ->with('success', 'Penulis tindak lanjut berhasil ditunjuk dan notifikasi telah dikirim.');
}

private function isAssignedActionTaker($meeting)
{
    return $meeting->assigned_action_taker_id === auth()->id();
}

    // Rate Participant
    public function rateParticipant(Request $request, Meeting $meeting, MeetingParticipant $participant)
    {
        // Hanya organizer yang boleh memberi nilai
        if ($meeting->organizer_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Hanya pembuat meeting yang dapat memberi nilai peserta.');
        }

        // Meeting harus sudah selesai
        if ($meeting->status !== 'completed') {
            return redirect()->back()->with('error', 'Penilaian hanya bisa dilakukan setelah meeting selesai.');
        }

        // Validasi participant adalah bagian dari meeting ini
        if ($participant->meeting_id !== $meeting->id) {
            abort(404);
        }

        $validated = $request->validate([
            'score'      => 'required|integer|min:1|max:100',
            'score_note' => 'nullable|string|max:500',
        ]);

        $participant->update([
            'score'      => $validated['score'],
            'score_note' => $validated['score_note'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Nilai untuk ' . $participant->user->name . ' berhasil disimpan.');
    }

}