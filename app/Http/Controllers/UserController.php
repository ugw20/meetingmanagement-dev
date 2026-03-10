<?php
// app/Http/Controllers\UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,manager,user',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dibuat.');
    }

    public function show(User $user)
    {
        $user->load('department', 'organizedMeetings', 'assignedActions');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $departments = Department::active()->get();
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,user',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->organizedMeetings()->exists() || $user->assignedActions()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus pengguna yang masih memiliki data terkait.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}