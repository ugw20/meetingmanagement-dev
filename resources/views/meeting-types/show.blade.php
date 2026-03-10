<!-- resources/views/meeting-types/show.blade.php -->
@extends('layouts.app')

@section('title', $meetingType->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meeting-types.index') }}">Jenis Meeting</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Jenis Meeting</h3>
                <div class="card-tools">
                    <a href="{{ route('meeting-types.edit', $meetingType) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Nama</th>
                        <td>{{ $meetingType->name }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $meetingType->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Field Wajib</th>
                        <td>
                            @if($meetingType->required_fields)
                                @foreach($meetingType->required_fields as $field)
                                    <span class="badge badge-info">{{ $field }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge badge-{{ $meetingType->is_active ? 'success' : 'danger' }}">
                                {{ $meetingType->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $meetingType->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate</th>
                        <td>{{ $meetingType->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Meeting dengan Jenis Ini</h3>
            </div>
            <div class="card-body p-0">
                @if($meetingType->meetings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul Meeting</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meetingType->meetings->take(5) as $meeting)
                            <tr>
                                <td>
                                    <a href="{{ route('meetings.show', $meeting) }}">
                                        {{ Str::limit($meeting->title, 30) }}
                                    </a>
                                </td>
                                <td>{{ $meeting->start_time->format('d M Y') }}</td>
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
                @if($meetingType->meetings->count() > 5)
                <div class="card-footer">
                    <small class="text-muted">
                        Menampilkan 5 dari {{ $meetingType->meetings->count() }} meeting
                    </small>
                </div>
                @endif
                @else
                <div class="p-3 text-center text-muted">
                    Belum ada meeting dengan jenis ini.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection