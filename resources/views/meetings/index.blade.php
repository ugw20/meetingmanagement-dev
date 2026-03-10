<!-- resources/views/meetings/index.blade.php -->
@extends('layouts.app')

@section('title', 'Daftar Meeting')

@section('breadcrumb')
    <li class="breadcrumb-item active">Meeting</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Meeting</h3>
        @if(auth()->user()->canManageMeetings())
        <div class="card-tools">
            <a href="{{ route('meetings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Meeting Baru
            </a>
        </div>
        @endif
    </div>
    
    <!-- Tabs untuk Admin/Manager -->
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="meetingTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $filters['type'] === 'all' ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['type' => 'all']) }}">
                    Semua Meeting
                    <span class="badge badge-primary ml-1">{{ $stats['all'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $filters['type'] === 'created' ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['type' => 'created']) }}">
                    Yang Saya Buat
                    <span class="badge badge-info ml-1">{{ $stats['created'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $filters['type'] === 'participating' ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['type' => 'participating']) }}">
                    Yang Saya Ikuti
                    <span class="badge badge-success ml-1">{{ $stats['participating'] ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>
    @endif
    
    <!-- Filter Section -->
    <div class="card-body border-bottom">
        <form action="{{ route('meetings.index') }}" method="GET" id="filterForm">
            <!-- Tambahkan hidden input untuk type -->
            <input type="hidden" name="type" value="{{ $filters['type'] }}">
            
            <div class="row">
                <!-- Filter Status -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status Meeting</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>
                
                <!-- Filter Departemen -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="department_id">Departemen</label>
                        <select name="department_id" id="department_id" class="form-control">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" 
                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Filter Jenis Meeting -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="meeting_type_id">Jenis Meeting</label>
                        <select name="meeting_type_id" id="meeting_type_id" class="form-control">
                            <option value="">Semua Jenis</option>
                            @foreach($meetingTypes as $type)
                                <option value="{{ $type->id }}" 
                                    {{ request('meeting_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filter Sorting -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sort">Urutkan Berdasarkan</label>
                        <select name="sort" id="sort" class="form-control">
                            <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru - Lama</option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Lama - Terbaru</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Tombol Filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
                
                <!-- Tombol Reset -->
                <div class="col-md-3">
                    <div class="form-group">
                        <a href="{{ route('meetings.index', ['type' => $filters['type']]) }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-redo"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Info Filter Aktif -->
        @if(request()->anyFilled(['status', 'department_id', 'meeting_type_id', 'sort']))
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info py-2">
                    <small>
                        <strong>Filter Aktif:</strong>
                        @if(request('status'))
                            <span class="badge badge-info mr-1">Status: {{ request('status') == 'scheduled' ? 'Terjadwal' : (request('status') == 'ongoing' ? 'Berlangsung' : 'Selesai') }}</span>
                        @endif
                        @if(request('department_id'))
                            <span class="badge badge-info mr-1">Departemen: {{ $departments->where('id', request('department_id'))->first()->name ?? '' }}</span>
                        @endif
                        @if(request('meeting_type_id'))
                            <span class="badge badge-info mr-1">Jenis: {{ $meetingTypes->where('id', request('meeting_type_id'))->first()->name ?? '' }}</span>
                        @endif
                        @if(request('sort'))
                            <span class="badge badge-info mr-1">Urutan: {{ request('sort') == 'desc' ? 'Terbaru - Lama' : 'Lama - Terbaru' }}</span>
                        @endif
                        <a href="{{ route('meetings.index', ['type' => $filters['type']]) }}" class="badge badge-danger ml-2">Hapus Semua Filter</a>
                    </small>
                </div>
            </div>
        </div>
        @endif

        <!-- Info Tab Aktif -->
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <div class="row mt-2">
            <div class="col-12">
                <div class="alert alert-light py-2">
                    <small>
                        <i class="fas fa-info-circle mr-1"></i>
                        @if($filters['type'] === 'created')
                            Menampilkan <strong>meeting yang Anda buat</strong> sebagai organizer.
                        @elseif($filters['type'] === 'participating')
                            Menampilkan <strong>meeting yang Anda ikuti sebagai peserta</strong> (bukan sebagai organizer).
                        @else
                            Menampilkan <strong>semua meeting</strong> dalam sistem.
                        @endif
                    </small>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Meeting</span>
                        <span class="info-box-number">{{ $meetings->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Terjadwal</span>
                        <span class="info-box-number">{{ $statusCounts['scheduled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-running"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Berlangsung</span>
                        <span class="info-box-number">{{ $statusCounts['ongoing'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Selesai</span>
                        <span class="info-box-number">{{ $statusCounts['completed'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Urutan -->
        <div class="alert alert-light mb-3 py-2">
            <small>
                <i class="fas fa-info-circle mr-1"></i>
                Data diurutkan berdasarkan <strong>tanggal pembuatan</strong> 
                ({{ request('sort', 'desc') == 'asc' ? 'Lama - Terbaru' : 'Terbaru - Lama' }})
            </small>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Organizer</th>
                        <th>Departemen</th>
                        <th>Waktu Meeting</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>
                            <div class="d-flex align-items-center">
                                <span>Dibuat</span>
                                @if(request('sort', 'desc') == 'asc')
                                    <i class="fas fa-sort-up ml-1 text-primary" title="Urutan: Lama - Terbaru"></i>
                                @else
                                    <i class="fas fa-sort-down ml-1 text-primary" title="Urutan: Terbaru - Lama"></i>
                                @endif
                            </div>
                        </th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                    @php
                        $isOrganizer = $meeting->organizer_id == auth()->id();
                        $isParticipant = $meeting->participants->contains('user_id', auth()->id());
                    @endphp
                    <tr class="{{ $isOrganizer ? 'table-info' : ($isParticipant ? 'table-warning' : '') }}">
                        <td>
                            <div>
                                <strong>{{ $meeting->title }}</strong>
                                @if($isOrganizer)
                                <span class="badge badge-info badge-pill ml-1 small">Anda Organizer</span>
                                @elseif($isParticipant)
                                <span class="badge badge-warning badge-pill ml-1 small">Anda Peserta</span>
                                @endif
                            </div>
                            @if($meeting->description)
                            <br><small class="text-muted">{{ Str::limit($meeting->description, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $meeting->meetingType->name }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle mr-2 {{ $isOrganizer ? 'text-info' : 'text-muted' }}"></i>
                                <div>
                                    <strong class="{{ $isOrganizer ? 'text-info' : '' }}">{{ $meeting->organizer->name }}</strong>
                                    @if($isOrganizer)
                                    <br><small class="text-info">(Anda)</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $meeting->department->name }}</td>
                        <td>
                            <small>
                                <strong>{{ $meeting->start_time->format('d M Y') }}</strong><br>
                                {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                            </small>
                        </td>
                        <td>
                            @if($meeting->is_online)
                                <i class="fas fa-video text-info"></i> Online
                                @if($meeting->meeting_link)
                                <br><small class="text-muted">{{ Str::limit($meeting->meeting_link, 25) }}</small>
                                @endif
                            @else
                                <i class="fas fa-building text-secondary"></i> 
                                <small>{{ Str::limit($meeting->location, 30) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $meeting->status === 'scheduled' ? 'primary' : ($meeting->status === 'ongoing' ? 'warning' : 'success') }}">
                                {{ $meeting->status === 'scheduled' ? 'Terjadwal' : ($meeting->status === 'ongoing' ? 'Berlangsung' : 'Selesai') }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $meeting->created_at->format('d M Y') }}</small>
                            <br><small class="text-muted">{{ $meeting->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Hanya tampilkan edit/hapus jika user adalah pembuat meeting -->
                                @if($meeting->organizer_id == auth()->id())
                                    <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Hapus meeting ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Admin masih bisa hapus semua meeting -->
                                @if(auth()->user()->isAdmin() && $meeting->organizer_id != auth()->id())
                                    <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Hapus meeting ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada meeting yang ditemukan</p>
                            @if(request()->anyFilled(['status', 'department_id', 'meeting_type_id']))
                            <small class="text-muted">Coba ubah filter pencarian Anda</small>
                            @endif
                            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            @if($filters['type'] === 'created')
                            <div class="mt-2">
                                <a href="{{ route('meetings.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Buat Meeting Pertama
                                </a>
                            </div>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted mb-2 mb-md-0">
                Menampilkan {{ $meetings->firstItem() ?? 0 }} - {{ $meetings->lastItem() ?? 0 }} 
                dari {{ $meetings->total() }} data
            </div>
            
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($meetings->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $meetings->previousPageUrl() }}">
                                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                            </a>
                        </li>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $current = $meetings->currentPage();
                        $last = $meetings->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    {{-- First Page --}}
                    @if ($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $meetings->url(1) }}">1</a>
                        </li>
                        @if ($start > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    {{-- Page Numbers --}}
                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $meetings->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $meetings->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Last Page --}}
                    @if ($end < $last)
                        @if ($end < $last - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $meetings->url($last) }}">{{ $last }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($meetings->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $meetings->nextPageUrl() }}">
                                Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.info-box {
    min-height: 70px;
    margin-bottom: 0;
    border-radius: 8px;
}
.info-box .info-box-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    border-radius: 8px 0 0 8px;
}
.info-box .info-box-content {
    padding: 10px;
}
.info-box .info-box-text {
    font-size: 14px;
    font-weight: 600;
}
.info-box .info-box-number {
    font-size: 18px;
    font-weight: 700;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.badge {
    font-size: 11px;
    padding: 5px 8px;
}
.btn-group-sm > .btn {
    padding: 4px 8px;
    font-size: 12px;
}

/* Tab Styles */
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 12px 20px;
}
.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
    color: #007bff;
    background-color: transparent;
}
.nav-tabs .nav-link:hover {
    border-bottom-color: #dee2e6;
    color: #495057;
}

/* Table row colors */
.table-info {
    background-color: #d1ecf1 !important;
}
.table-warning {
    background-color: #fff3cd !important;
}

/* Enhanced Pagination Styles */
.pagination {
    margin-bottom: 0;
    font-size: 0.9rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}
.pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    margin: 0 2px;
    border-radius: 0.375rem;
    color: #495057;
    background-color: #fff;
    transition: all 0.2s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 38px;
}
.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
    font-weight: 600;
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}
.pagination .page-item:not(.disabled):not(.active) .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #0056b3;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Mobile responsive */
@media (max-width: 768px) {
    .pagination {
        font-size: 0.8rem;
    }
    .pagination .page-link {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
        min-width: 35px;
        height: 35px;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll ke atas saat pindah halaman
    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.closest('.page-item').classList.contains('disabled') && 
                !this.closest('.page-item').classList.contains('active')) {
                setTimeout(() => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 100);
            }
        });
    });

    // Update hidden type input ketika tab diklik
    document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            const url = new URL(this.href);
            const type = url.searchParams.get('type') || 'all';
            document.querySelector('input[name="type"]').value = type;
        });
    });
});
</script>
@endsection