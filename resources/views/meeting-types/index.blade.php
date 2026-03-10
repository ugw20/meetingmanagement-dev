<!-- resources/views/meeting-types/index.blade.php -->
@extends('layouts.app')

@section('title', 'Jenis Meeting')

@section('breadcrumb')
    <li class="breadcrumb-item active">Jenis Meeting</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Jenis Meeting</h3>
        <div class="card-tools">
            <a href="{{ route('meeting-types.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Jenis Meeting
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Field Wajib</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($meetingTypes as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->description ?? '-' }}</td>
                    <td>
                        @if($type->required_fields)
                            @foreach($type->required_fields as $field)
                                <span class="badge badge-info">{{ $field }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $type->is_active ? 'success' : 'danger' }}">
                            {{ $type->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('meeting-types.edit', $type) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('meeting-types.destroy', $type) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Hapus jenis meeting ini?')">
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