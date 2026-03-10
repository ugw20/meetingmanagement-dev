<!-- resources/views/departments/show.blade.php -->
@extends('layouts.app')

@section('title', $department->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departemen</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Departemen</h3>
                <div class="card-tools">
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Nama</th>
                        <td>{{ $department->name }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $department->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pengguna</th>
                        <td>
                            <span class="badge badge-info">{{ $department->users_count }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge badge-{{ $department->is_active ? 'success' : 'danger' }}">
                                {{ $department->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $department->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate</th>
                        <td>{{ $department->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengguna di Departemen Ini</h3>
            </div>
            <div class="card-body p-0">
                @if($department->users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Posisi</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('users.show', $user) }}">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->position }}</td>
                                <td>
                                    <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'manager' ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-3 text-center text-muted">
                    Belum ada pengguna di departemen ini.
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Statistik Tindak Lanjut</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="small-box bg-success p-2">
                            <div class="inner">
                                <h3>{{ $department->getCompletedActionItemsCountAttribute() }}</h3>
                                <p>Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small-box bg-warning p-2">
                            <div class="inner">
                                <h3>{{ $department->actionItems()->whereIn('status', ['pending', 'in_progress'])->count() }}</h3>
                                <p>Belum Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small-box bg-danger p-2">
                            <div class="inner">
                                <h3>{{ $department->actionItems()->overdue()->count() }}</h3>
                                <p>Terlambat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection