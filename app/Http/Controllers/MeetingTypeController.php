<?php
// app/Http/Controllers/MeetingTypeController.php
namespace App\Http\Controllers;

use App\Models\MeetingType;
use Illuminate\Http\Request;

class MeetingTypeController extends Controller
{
    public function index()
    {
        $meetingTypes = MeetingType::all();
        return view('meeting-types.index', compact('meetingTypes'));
    }

    public function create()
    {
        return view('meeting-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:meeting_types',
            'description' => 'nullable|string',
            'required_fields' => 'nullable|array',
        ]);

        MeetingType::create($validated);

        return redirect()->route('meeting-types.index')
            ->with('success', 'Jenis meeting berhasil dibuat.');
    }

    public function show(MeetingType $meetingType)
    {
        return view('meeting-types.show', compact('meetingType'));
    }

    public function edit(MeetingType $meetingType)
    {
        return view('meeting-types.edit', compact('meetingType'));
    }

    public function update(Request $request, MeetingType $meetingType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:meeting_types,name,' . $meetingType->id,
            'description' => 'nullable|string',
            'required_fields' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $meetingType->update($validated);

        return redirect()->route('meeting-types.index')
            ->with('success', 'Jenis meeting berhasil diperbarui.');
    }

    public function destroy(MeetingType $meetingType)
    {
        if ($meetingType->meetings()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus jenis meeting yang masih digunakan.');
        }

        $meetingType->delete();

        return redirect()->route('meeting-types.index')
            ->with('success', 'Jenis meeting berhasil dihapus.');
    }
}