<!-- resources/views/departments/index.blade.php -->
@extends('layouts.app')

@section('title', 'Departemen')

@section('breadcrumb')
    <li class="breadcrumb-item active">Departemen</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Departemen</h3>
        <div class="card-tools">
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Departemen
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Pengguna</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->description ?? '-' }}</td>
                    <td>
                        <span class="badge badge-info">{{ $department->users_count }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $department->is_active ? 'success' : 'danger' }}">
                            {{ $department->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Hapus departemen ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection