<!-- resources/views/users/show.blade.php -->
@extends('layouts.app')

@section('title', $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- User Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengguna</h3>
                <div class="card-tools">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="user-panel mt-3 pb-3 mb-3">
                        <div class="image">
                            <i class="fas fa-user-circle img-circle elevation-2" style="font-size: 60px; color: #6c757d;"></i>
                        </div>
                        <div class="info">
                            <h4 class="mb-0">{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->position }}</p>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $user->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>
                            <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'manager' ? 'warning' : 'info') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Bergabung</th>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistik Cepat</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="small-box bg-info p-2">
                            <div class="inner">
                                <h3>{{ $user->organizedMeetings->count() }}</h3>
                                <p>Meeting Diatur</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="small-box bg-success p-2">
                            <div class="inner">
                                <h3>{{ $user->assignedActions->where('status', 'completed')->count() }}</h3>
                                <p>Tugas Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Assigned Action Items -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tugas yang Ditugaskan</h3>
            </div>
            <div class="card-body p-0">
                @if($user->assignedActions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul Tugas</th>
                                <th>Meeting</th>
                                <th>Batas Waktu</th>
                                <th>Status</th>
                                <th>Prioritas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->assignedActions->take(10) as $action)
                            <tr>
                                <td>
                                    <a href="{{ route('action-items.show', $action) }}">
                                        {{ Str::limit($action->title, 30) }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('meetings.show', $action->meeting) }}">
                                        {{ Str::limit($action->meeting->title, 25) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $action->isOverdue() ? 'danger' : 'secondary' }}">
                                        {{ $action->due_date->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $action->status_badge }}">
                                        {{ $action->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $action->priority_badge }}">
                                        {{ $action->priority_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($user->assignedActions->count() > 10)
                <div class="card-footer">
                    <small class="text-muted">
                        Menampilkan 10 dari {{ $user->assignedActions->count() }} tugas
                    </small>
                </div>
                @endif
                @else
                <div class="p-3 text-center text-muted">
                    Belum ada tugas yang ditugaskan ke pengguna ini.
                </div>
                @endif
            </div>
        </div>

        <!-- Organized Meetings -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Meeting yang Diatur</h3>
            </div>
            <div class="card-body p-0">
                @if($user->organizedMeetings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul Meeting</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->organizedMeetings->take(10) as $meeting)
                            <tr>
                                <td>
                                    <a href="{{ route('meetings.show', $meeting) }}">
                                        {{ Str::limit($meeting->title, 40) }}
                                    </a>
                                </td>
                                <td>{{ $meeting->start_time->format('d M Y') }}</td>
                                <td>{{ $meeting->meetingType->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $meeting->status === 'scheduled' ? 'primary' : ($meeting->status === 'ongoing' ? 'warning' : 'success') }}">
                                        {{ $meeting->status === 'scheduled' ? 'Terjadwal' : ($meeting->status === 'ongoing' ? 'Berlangsung' : 'Selesai') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($user->organizedMeetings->count() > 10)
                <div class="card-footer">
                    <small class="text-muted">
                        Menampilkan 10 dari {{ $user->organizedMeetings->count() }} meeting
                    </small>
                </div>
                @endif
                @else
                <div class="p-3 text-center text-muted">
                    Pengguna ini belum mengatur meeting.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection