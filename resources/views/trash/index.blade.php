<!-- resources/views/trash/index.blade.php -->
@extends('layouts.app')

@section('title', 'Tempat Sampah - Meeting')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meeting</a></li>
    <li class="breadcrumb-item active">Tempat Sampah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Meeting yang Dihapus</h3>
        <div class="card-tools">
            @if($deletedMeetings->total() > 0)
            <form action="{{ route('trash.empty') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" 
                        onclick="return confirm('Hapus semua meeting secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                    <i class="fas fa-trash mr-1"></i> Kosongkan Tempat Sampah
                </button>
            </form>
            @endif
        </div>
    </div>
    
    <div class="card-body">
        @if($deletedMeetings->total() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Organizer</th>
                        <th>Departemen</th>
                        <th>Dihapus Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deletedMeetings as $meeting)
                    <tr>
                        <td>
                            <strong>{{ $meeting->title }}</strong>
                            @if($meeting->description)
                            <br><small class="text-muted">{{ Str::limit($meeting->description, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $meeting->meetingType->name }}</td>
                        <td>{{ $meeting->organizer->name }}</td>
                        <td>{{ $meeting->department->name }}</td>
                        <td>
                            <small>{{ $meeting->deleted_at->format('d M Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form action="{{ route('trash.restore', $meeting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Pulihkan">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('trash.force-delete', $meeting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Hapus permanen meeting ini?')" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $deletedMeetings->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-trash fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tempat sampah kosong</h5>
            <p class="text-muted">Tidak ada meeting yang dihapus</p>
            <a href="{{ route('meetings.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Meeting
            </a>
        </div>
        @endif
    </div>
</div>
@endsection