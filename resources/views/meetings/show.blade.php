<!-- resources/views/meetings/show.blade.php -->
@extends('layouts.app')

@section('title', $meeting->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meeting</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
@if($meeting->status === 'ongoing' && ($meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id()))
<div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Meeting sedang berlangsung!</strong> 
    @if($meeting->assigned_action_taker_id == auth()->id())
    Anda ditunjuk sebagai <strong>Penulis Tindak Lanjut</strong>. 
    @endif
    <a href="{{ route('meetings.running', $meeting) }}" class="alert-link font-weight-bold">
        Klik di sini untuk masuk ke halaman meeting dan input tindak lanjut
    </a>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<style>
    /* Custom CSS untuk perbaikan tata letak */
    .info-box {
        min-height: 70px;
        margin-bottom: 12px;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
    }
    .info-box .info-box-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        border-radius: 8px 0 0 8px;
    }
    .info-box .info-box-content {
        padding: 8px 12px;
    }
    .info-box .info-box-text {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 2px;
    }
    .info-box .info-box-number {
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }
    .card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #4e73df;
        margin-bottom: 0;
    }
    .table th {
        font-size: 13px;
        font-weight: 700;
        background-color: #f8f9fc;
        border-top: 1px solid #e3e6f0;
    }
    .table td {
        font-size: 13px;
        vertical-align: middle;
    }
    .badge {
        font-size: 11px;
        padding: 4px 8px;
        font-weight: 600;
    }
    .user-avatar {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .callout {
        border-left: 4px solid;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 4px;
        background: #f8f9fc;
    }
    .list-group-item {
        padding: 12px 16px;
        border: 1px solid #e3e6f0;
    }
    .btn-group-sm > .btn {
        padding: 4px 8px;
        font-size: 12px;
    }
    /* Perbaikan spacing */
    .card-body {
        padding: 16px;
    }
    .modal-header {
        padding: 12px 16px;
    }
    .modal-body {
        padding: 16px;
    }
    /* Responsive improvements */
    @media (max-width: 768px) {
        .info-box .info-box-icon {
            width: 50px;
            height: 50px;
            font-size: 18px;
        }
        .info-box .info-box-content {
            padding: 6px 10px;
        }
        .card-body {
            padding: 12px;
        }
    }
    /* Style untuk indicator tugas */
    .assigned-to-me {
        background-color: #f0f8ff !important;
        border-left: 3px solid #4e73df !important;
    }
    /* Style untuk participant badges */
    .participants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }
    .participant-item {
        position: relative;
        transition: all 0.3s ease;
    }
    .participant-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }
    .participant-avatar {
        position: relative;
        display: inline-block;
    }
    .role-badge {
        font-size: 0.6rem;
    }
</style>

<div class="row">
    <div class="col-md-8">
        <!-- Meeting Details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-info-circle mr-2 text-primary"></i>Detail Meeting
                    @if($meeting->status === 'ongoing')
                    <span class="badge badge-warning ml-2">
                        <i class="fas fa-running mr-1"></i> Sedang Berlangsung
                    </span>
                    @endif
                </h3>
                <div class="card-tools">
                    <!-- Tombol utama untuk input tindak lanjut -->
                    @if($meeting->status === 'ongoing' && ($meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id()))
                    <a href="{{ route('meetings.running', $meeting) }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-tasks mr-1"></i> Input Tindak Lanjut
                    </a>
                    @endif
                    
                    <!-- Tombol kontrol meeting untuk organizer -->
                    @if(auth()->user()->isAdmin() || $meeting->organizer_id == auth()->id())
                        @if($meeting->status === 'scheduled')
                            <form action="{{ route('meetings.start', $meeting) }}" method="POST" class="d-inline mr-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-play mr-1"></i> Mulai Meeting
                                </button>
                            </form>
                        @endif
                        
                        @if($meeting->status === 'ongoing')
                            <a href="{{ route('meetings.running', $meeting) }}" class="btn btn-warning btn-sm mr-2">
                                <i class="fas fa-running mr-1"></i> Kelola Meeting
                            </a>
                        @endif
                        
                        <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-primary"><i class="fas fa-heading text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Judul Meeting</span>
                                <span class="info-box-number">{{ $meeting->title }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-info"><i class="fas fa-list text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Jenis Meeting</span>
                                <span class="info-box-number">{{ $meeting->meetingType->name }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-success"><i class="fas fa-user-tie text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Organizer</span>
                                <span class="info-box-number">{{ $meeting->organizer->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-warning"><i class="fas fa-building text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Departemen</span>
                                <span class="info-box-number">{{ $meeting->department->name }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-map-marker-alt text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Lokasi</span>
                                <span class="info-box-number">
                                    @if($meeting->is_online)
                                        <i class="fas fa-video text-info mr-1"></i> Online
                                    @else
                                        <i class="fas fa-building text-secondary mr-1"></i> {{ $meeting->location }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-white">
                            <span class="info-box-icon bg-{{ $meeting->status === 'scheduled' ? 'primary' : ($meeting->status === 'ongoing' ? 'warning' : 'success') }}">
                                <i class="fas fa-{{ $meeting->status === 'scheduled' ? 'clock' : ($meeting->status === 'ongoing' ? 'running' : 'check') }} text-white"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status</span>
                                <span class="info-box-number">
                                    <span class="badge badge-{{ $meeting->status === 'scheduled' ? 'primary' : ($meeting->status === 'ongoing' ? 'warning' : 'success') }}">
                                        {{ $meeting->status === 'scheduled' ? 'Terjadwal' : ($meeting->status === 'ongoing' ? 'Berlangsung' : 'Selesai') }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waktu Meeting -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-info border-left-info">
                            <h6 class="mb-1"><i class="fas fa-play-circle mr-2 text-info"></i>Waktu Mulai</h6>
                            <p class="mb-0 text-dark font-weight-semibold">{{ $meeting->start_time->format('l, d F Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-info border-left-info">
                            <h6 class="mb-1"><i class="fas fa-stop-circle mr-2 text-info"></i>Waktu Selesai</h6>
                            <p class="mb-0 text-dark font-weight-semibold">{{ $meeting->end_time->format('l, d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($meeting->description)
                <div class="mt-3">
                    <h6 class="text-primary mb-2"><i class="fas fa-align-left mr-2"></i>Deskripsi Meeting</h6>
                    <div class="border rounded p-3 bg-light">
                        {{ $meeting->description }}
                    </div>
                </div>
                @endif

                @if($meeting->is_online && $meeting->meeting_link)
                <div class="mt-3">
                    <h6 class="text-primary mb-2"><i class="fas fa-link mr-2"></i>Link Meeting Online</h6>
                    <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Link Meeting
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Items -->
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-tasks mr-2 text-primary"></i>Tindak Lanjut
                    @if($meeting->actionItems->count() > 0)
                    <span class="badge badge-primary badge-pill ml-1">{{ $meeting->actionItems->count() }}</span>
                    @endif
                </h3>
                <div class="card-tools">
                    <!-- Tombol untuk masuk ke halaman running meeting (jika meeting ongoing) -->
                    @if($meeting->status === 'ongoing' && ($meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id()))
                    <a href="{{ route('meetings.running', $meeting) }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-plus-circle mr-1"></i> Input Tindak Lanjut
                    </a>
                    @endif
                    
                    <!-- Tombol tambah tindak lanjut (jika meeting completed) -->
                    @if($meeting->status === 'completed' && (auth()->user()->isAdmin() || $meeting->organizer_id == auth()->id()))
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addActionItemModal">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($meeting->actionItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Judul</th>
                                <th style="width: 150px">Ditugaskan ke</th>
                                <th style="width: 100px">Batas Waktu</th>
                                <th style="width: 90px">Status</th>
                                <th style="width: 90px">Prioritas</th>
                                <th style="width: 80px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meeting->actionItems as $actionItem)
                            @php
                                $isAssignedToMe = $actionItem->assigned_to == auth()->id();
                                $canView = $isAssignedToMe || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id();
                            @endphp
                            
                            @if($canView)
                            <tr class="{{ $isAssignedToMe ? 'assigned-to-me' : '' }}">
                                <td>
                                    <div>
                                        <strong class="text-dark">
                                            <a href="{{ route('action-items.show', $actionItem) }}" class="text-dark text-decoration-none">
                                                {{ Str::limit($actionItem->title, 40) }}
                                                @if($isAssignedToMe)
                                                <span class="badge badge-info badge-pill ml-1 small">Tugas Anda</span>
                                                @endif
                                            </a>
                                        </strong>
                                        @if($actionItem->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($actionItem->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-2">
                                            <i class="fas fa-user-circle text-muted"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block small">{{ $actionItem->assignedTo->name }}</strong>
                                            <small class="text-muted">{{ Str::limit($actionItem->assignedTo->position, 20) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $actionItem->isOverdue() ? 'danger' : 'secondary' }}">
                                        {{ $actionItem->due_date->format('d M Y') }}
                                    </span>
                                    @if($actionItem->isOverdue())
                                    <br>
                                    <small class="text-danger small">
                                        <i class="fas fa-exclamation-triangle"></i> Terlambat
                                    </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $actionItem->status_badge }}">
                                        {{ $actionItem->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $actionItem->priority_badge }}">
                                        {{ $actionItem->priority_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Semua yang punya akses bisa melihat detail -->
                                        <a href="{{ route('action-items.show', $actionItem) }}" class="btn btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Hanya bisa edit jika: -->
                                        <!-- 1. Admin/Manager yang bisa manage meetings -->
                                        <!-- 2. Organizer meeting -->
                                        <!-- TIDAK termasuk user yang ditugaskan -->
                                        @if($meeting->organizer_id == auth()->id() && !$isAssignedToMe)
                                        <a href="{{ route('action-items.edit', $actionItem) }}" class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    
                    @if(auth()->user()->isAdmin() || $meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id())
                    <h6 class="text-muted">Belum ada tindak lanjut</h6>
                    <p class="text-muted small">
                        @if($meeting->status === 'ongoing')
                        Klik tombol "Input Tindak Lanjut" untuk menambahkan tindak lanjut pertama
                        @elseif($meeting->status === 'completed')
                        Tindak lanjut akan muncul setelah meeting selesai
                        @endif
                    </p>
                    @if($meeting->status === 'ongoing' || $meeting->status === 'completed')
                    @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id())
                    <button type="button" class="btn btn-success btn-sm mt-2" data-toggle="modal" data-target="#addActionItemModal">
                        <i class="fas fa-plus-circle mr-1"></i> Input Tindak Lanjut
                    </button>
                    @endif
                    @endif
                    @else
                    <h6 class="text-muted">Tidak ada tugas untuk Anda</h6>
                    <p class="text-muted small">Anda tidak memiliki tugas dari meeting ini</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-bolt mr-2"></i>Aksi Cepat
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <!-- Tombol Assign Minute Taker hanya untuk organizer/admin -->
                    @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                    <button type="button" class="btn btn-outline-warning btn-sm text-left d-block w-100 mb-2" data-toggle="modal" data-target="#assignMinuteTakerModal">
                        <i class="fas fa-user-edit mr-2"></i>@if($meeting->assignedMinuteTaker) Ganti @else Tunjuk @endif Penulis Notulensi
                    </button>
                    
                    <!-- Tombol Assign Action Taker hanya untuk organizer/admin -->
                    <button type="button" class="btn btn-outline-success btn-sm text-left d-block w-100 mb-2" data-toggle="modal" data-target="#assignActionTakerModal">
                        <i class="fas fa-user-plus mr-2"></i>@if($meeting->assignedActionTaker) Ganti @else Tunjuk @endif Penulis Tindak Lanjut
                    </button>
                    @endif
                    
                    <!-- Tombol Tambah Tindak Lanjut untuk organizer, admin, dan assigned action taker -->
                    @if((auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id()) && in_array($meeting->status, ['scheduled', 'ongoing', 'completed']))
                        <button type="button" class="btn btn-outline-success btn-sm text-left d-block w-100 mb-2" data-toggle="modal" data-target="#addActionItemModal">
                            <i class="fas fa-plus-circle mr-2"></i>Tambah Tindak Lanjut
                        </button>
                    @endif
                    
                    <!-- Tombol Buat/Edit Notulensi -->
                    @if(in_array($meeting->status, ['scheduled', 'ongoing', 'completed']) && (!$meeting->minutes || !$meeting->minutes->is_finalized))
                        @if($meeting->assigned_minute_taker_id == auth()->id() || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                        <a href="{{ route('meetings.running', $meeting) }}#minuteTakerForm" class="btn btn-outline-primary btn-sm text-left d-block w-100 mb-2">
                            <i class="fas fa-edit mr-2"></i>{{ $meeting->minutes ? 'Edit Notulensi' : 'Buat Notulensi' }}
                        </a>
                        @endif
                    @endif
                    
                    <!-- Tombol Upload File -->
                    @if(in_array($meeting->status, ['scheduled', 'ongoing', 'completed']) && ($meeting->organizer_id == auth()->id() || auth()->user()->canManageMeetings()))
                    <button type="button" class="btn btn-outline-primary btn-sm text-left d-block w-100" data-toggle="modal" data-target="#uploadFileModal">
                        <i class="fas fa-upload mr-2"></i>Upload File Baru
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Meeting Minutes -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-clipboard mr-2 text-primary"></i>Notulensi Meeting
                    @if($meeting->assignedMinuteTaker)
                    <small class="float-right text-muted">
                        <i class="fas fa-user-edit mr-1"></i>Penulis: {{ $meeting->assignedMinuteTaker->name }}
                    </small>
                    @endif
                </h3>
            </div>
            <div class="card-body">
                <!-- Info Action Taker -->
                @if($meeting->assignedActionTaker)
                <div class="alert alert-success alert-dismissible fade show mb-3 py-2" role="alert">
                    <i class="fas fa-user-plus mr-2"></i>
                    <strong>Penulis Tindak Lanjut:</strong> {{ $meeting->assignedActionTaker->name }}
                    @if($meeting->assigned_action_taker_id == auth()->id())
                    <span class="badge badge-success badge-pill ml-2">Anda</span>
                    @endif
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif
                
                @if($meeting->minutes)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-file-alt mr-1"></i>Konten Notulensi
                        </h6>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($meeting->minutes->content)) !!}
                        </div>
                    </div>
                    
                    @if($meeting->minutes->decisions && count($meeting->minutes->decisions) > 0)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-gavel mr-1"></i>Keputusan
                        </h6>
                        <ul class="list-group">
                            @foreach($meeting->minutes->decisions as $decision)
                                @if(!empty(trim($decision)))
                                <li class="list-group-item d-flex align-items-center py-2">
                                    <i class="fas fa-check text-success mr-2"></i>
                                    {{ $decision }}
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <i class="fas fa-user-edit mr-1"></i>
                                    <strong>Dicatat oleh:</strong> {{ $meeting->minutes->minuteTaker->name }}
                                </small>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <strong>Dibuat:</strong> {{ $meeting->minutes->created_at->format('d M Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                @if($meeting->minutes->is_finalized)
                                <small class="text-muted d-block">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <strong>Difinalisasi:</strong> {{ $meeting->minutes->finalized_at->format('d M Y H:i') }}
                                </small>
                                <span class="badge badge-success mt-1">
                                    <i class="fas fa-lock mr-1"></i> Terkunci
                                </span>
                                @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-pencil-alt mr-1"></i> Draft
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                        
                        @if($meeting->assignedMinuteTaker)
                            @if($meeting->assigned_minute_taker_id == auth()->id())
                            <h6 class="text-muted">Anda adalah Penulis Notulensi</h6>
                            <p class="text-muted mb-3">Belum ada notulensi untuk meeting ini.</p>
                            @if(in_array($meeting->status, ['scheduled', 'ongoing']))
                            <a href="{{ route('meetings.running', $meeting) }}#minuteTakerForm" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit mr-1"></i> Buat Notulensi
                            </a>
                            @elseif($meeting->status === 'completed')
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Meeting sudah selesai. Notulensi belum dibuat.
                            </div>
                            @endif
                            @else
                            <h6 class="text-muted">Penulis Notulensi: {{ $meeting->assignedMinuteTaker->name }}</h6>
                            <p class="text-muted mb-0">Belum ada notulensi untuk meeting ini.</p>
                            <small class="text-muted">
                                Menunggu {{ $meeting->assignedMinuteTaker->name }} membuat notulensi
                            </small>
                            @endif
                        @else
                        <h6 class="text-muted">Belum ada notulensi</h6>
                        <p class="text-muted mb-0">Belum ada penulis notulensi yang ditunjuk untuk meeting ini.</p>
                        @endif
                    </div>
                @endif
                
                <!-- Tombol Edit untuk Minute Taker yang Ditunjuk -->
                @if($meeting->minutes && !$meeting->minutes->is_finalized)
                    @if($meeting->assigned_minute_taker_id == auth()->id() || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                    <div class="mt-3">
                        <a href="{{ route('meetings.running', $meeting) }}#minuteTakerForm" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i> Edit Notulensi
                        </a>
                    </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Files -->
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-file mr-2 text-primary"></i>File Meeting
                    <span class="badge badge-info badge-pill ml-1">{{ $meeting->files->count() }}</span>
                </h3>
                @if(auth()->user()->isAdmin() || $meeting->organizer_id == auth()->id())
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadFileModal">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                @if($meeting->files->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($meeting->files as $file)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-file text-primary mr-2"></i>
                                    <strong class="text-truncate small">{{ $file->file_name }}</strong>
                                </div>
                                <div class="ml-4">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-hdd mr-1"></i>{{ $file->file_size_formatted }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user mr-1"></i>{{ $file->uploader->name }}
                                    </small>
                                    @if($file->description)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-align-left mr-1"></i>{{ Str::limit($file->description, 40) }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-2" style="white-space: nowrap;">
                                <a href="{{ route('meetings.files.preview', [$meeting, $file]) }}" target="_blank"
                                   class="btn btn-info btn-sm mr-1" title="Lihat/Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('meetings.files.download', [$meeting, $file]) }}" 
                                   class="btn btn-success btn-sm mr-1" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                
                                @if(auth()->user()->isAdmin() || $meeting->organizer_id == auth()->id())
                                <form action="{{ route('meetings.files.delete', [$meeting, $file]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Hapus file ini?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-file fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0 small">Belum ada file untuk meeting ini.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Participants - Grid Layout -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-users mr-2 text-primary"></i>Peserta
                    <span class="badge badge-primary badge-pill ml-1">{{ $meeting->participants->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="participants-grid">
                    @foreach($meeting->participants as $participant)
                    <div class="participant-item text-center p-2 border rounded">
                        <div class="participant-avatar mb-2 position-relative">
                            <i class="fas fa-user-circle fa-2x 
                                {{ $participant->role === 'chairperson' ? 'text-success' : 'text-muted' }}
                                {{ $participant->user_id == $meeting->assigned_minute_taker_id ? 'text-warning' : '' }}
                                {{ $participant->user_id == $meeting->assigned_action_taker_id ? 'text-success' : '' }}">
                            </i>
                            
                            <!-- Badge Minute Taker -->
                            @if($participant->user_id == $meeting->assigned_minute_taker_id)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle" title="Penulis Notulensi">
                                <i class="fas fa-user-edit text-white" style="font-size: 0.5rem;"></i>
                            </span>
                            @endif
                            
                            <!-- Badge Action Taker -->
                            @if($participant->user_id == $meeting->assigned_action_taker_id)
                            <span class="position-absolute top-0 start-0 translate-middle p-1 bg-success border border-light rounded-circle" title="Penulis Tindak Lanjut">
                                <i class="fas fa-tasks text-white" style="font-size: 0.5rem;"></i>
                            </span>
                            @endif
                        </div>
                        
                        <div class="participant-name small font-weight-medium">
                            {{ Str::limit($participant->user->name, 15) }}
                            @if($participant->user_id == auth()->id())
                            <span class="badge badge-info badge-pill ml-1" style="font-size: 0.6rem;">Anda</span>
                            @endif
                        </div>
                        
                        <div class="participant-role">
                            @if($participant->user_id == $meeting->assigned_minute_taker_id)
                            <span class="badge badge-warning mt-1 role-badge">
                                <i class="fas fa-user-edit"></i> Notulis
                            </span>
                            @elseif($participant->user_id == $meeting->assigned_action_taker_id)
                            <span class="badge badge-success mt-1 role-badge">
                                <i class="fas fa-tasks"></i> Action
                            </span>
                            @else
                            <span class="badge badge-{{ $participant->role === 'chairperson' ? 'success' : 'secondary' }} mt-1 role-badge">
                                {{ $participant->role === 'chairperson' ? 'Ketua' : 'Peserta' }}
                            </span>
                            @endif
                        </div>

                        {{-- Tampilkan score jika sudah dinilai --}}
                        @if($participant->score)
                        <div class="participant-score mt-2 mb-1">
                            <span class="badge badge-success px-2 py-1" style="font-size:0.7rem;">
                                Nilai: {{ $participant->score }}<small class="text-white-50">/100</small>
                            </span>
                            @if($participant->score_note)
                            <div class="text-muted mt-1" style="font-size:0.58rem; line-height:1.2;" title="{{ $participant->score_note }}">
                                "{{ Str::limit($participant->score_note, 30) }}"
                            </div>
                            @endif
                        </div>
                        @else
                        @if($meeting->organizer_id == auth()->id() && $meeting->status === 'completed')
                        <div class="mt-1"><small class="text-muted" style="font-size:0.6rem;">Belum dinilai</small></div>
                        @endif
                        @endif

                        {{-- Tombol Beri Nilai: hanya untuk organizer, meeting completed --}}
                        @if($meeting->organizer_id == auth()->id() && $meeting->status === 'completed')
                        <button type="button"
                            class="btn btn-xs btn-outline-warning mt-1 py-0 px-1"
                            style="font-size: 0.65rem;"
                            data-toggle="modal"
                            data-target="#rateModal-{{ $participant->id }}">
                            <i class="fas fa-star"></i> {{ $participant->score ? 'Edit Nilai' : 'Beri Nilai' }}
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Rating Modals (satu per peserta, hanya untuk organizer) --}}
        @if($meeting->organizer_id == auth()->id() && $meeting->status === 'completed')
            @foreach($meeting->participants as $participant)
            <div class="modal fade" id="rateModal-{{ $participant->id }}" tabindex="-1" role="dialog" aria-labelledby="rateModalLabel-{{ $participant->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
                    <div class="modal-content border-0 shadow-lg rounded-lg">
                        <div class="modal-header border-0 pb-0 justify-content-end">
                            <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('meetings.participants.rate', [$meeting, $participant]) }}" method="POST">
                            @csrf
                            <div class="modal-body pt-0 px-4 pb-4">
                                <!-- User Info Section -->
                                <div class="text-center mb-4">
                                    <div class="d-inline-block position-relative mb-3">
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm" style="width: 70px; height: 70px; margin: 0 auto;">
                                            <i class="fas fa-user text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">{{ $participant->user->name }}</h5>
                                    <span class="badge badge-light text-muted px-3 py-1 font-weight-normal border">
                                        {{ $participant->role_label }}
                                    </span>
                                </div>

                                <!-- Rating Score Section -->
                                <div class="form-group text-center mb-4 bg-light rounded pt-3 pb-3 px-3 border border-light">
                                    <label class="d-block mb-2 text-dark font-weight-semibold">Beri Penilaian (1-100)</label>
                                    
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <div class="position-relative" style="width: 100px;">
                                            <input type="number" name="score" id="score-{{ $participant->id }}" 
                                                   class="form-control text-center font-weight-bold score-number-input" 
                                                   value="{{ $participant->score ?? '' }}" 
                                                   min="1" max="100" required placeholder="0"
                                                   style="font-size: 1.8rem; height: 60px; border-radius: 12px; color: #4e73df; border: 2px solid #e3e6f0; background-color: #fff;"
                                                   data-slider-target="slider-{{ $participant->id }}">
                                        </div>
                                    </div>
                                    
                                    <div class="px-3">
                                        <input type="range" class="custom-range score-slider" 
                                               id="slider-{{ $participant->id }}" 
                                               min="1" max="100" 
                                               value="{{ $participant->score ?? 1 }}"
                                               data-input-target="score-{{ $participant->id }}">
                                        <div class="d-flex justify-content-between text-muted mt-1 small">
                                            <span>1</span>
                                            <span>100</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                <div class="form-group mb-4">
                                    <label class="text-dark font-weight-semibold mb-2">Evaluasi Singkat <span class="text-muted font-weight-normal small">(Opsional)</span></label>
                                    <textarea name="score_note" class="form-control bg-light border-0" rows="3" maxlength="500" placeholder="Tuliskan umpan balik untuk peserta ini... (mis: Sangat proaktif dalam diskusi)" style="resize: none;">{{ $participant->score_note }}</textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light px-4 font-weight-semibold text-muted" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4 font-weight-semibold shadow-sm">
                                        <i class="fas fa-paper-plane mr-2"></i>Kirim Penilaian
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
        <!-- Info Hak Akses untuk Partisipan -->
        @if($meeting->participants->contains('user_id', auth()->id()) && !auth()->user()->canManageMeetings() && $meeting->organizer_id != auth()->id())
        <div class="card shadow-sm mt-3 border-info">
            <div class="card-header bg-info text-white py-2">
                <h6 class="card-title m-0 small">
                    <i class="fas fa-info-circle mr-1"></i>Info Hak Akses Anda
                </h6>
            </div>
            <div class="card-body py-2">
                <small class="text-muted">
                    <i class="fas fa-check text-success mr-1"></i> Anda dapat melihat semua detail meeting<br>
                    <i class="fas fa-check text-success mr-1"></i> Anda dapat melihat semua tindak lanjut<br>
                    <i class="fas fa-times text-danger mr-1"></i> Anda <strong>tidak dapat</strong> mengubah tindak lanjut yang ditugaskan kepada Anda<br>
                    <i class="fas fa-times text-danger mr-1"></i> Anda tidak dapat mengubah meeting<br>
                    <i class="fas fa-times text-danger mr-1"></i> Anda tidak dapat menambah/menghapus file
                </small>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
@if((auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id()) && in_array($meeting->status, ['scheduled', 'ongoing', 'completed']))
<!-- Add Action Item Modal -->
<div class="modal fade" id="addActionItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Tindak Lanjut
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.action-items.store', $meeting) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" class="font-weight-bold small">Judul Tindak Lanjut *</label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" 
                                       placeholder="Masukkan judul tindak lanjut" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="font-weight-bold small">Deskripsi *</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3" 
                                  placeholder="Jelaskan detail tindak lanjut yang harus dilakukan" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assigned_to" class="font-weight-bold small">Ditugaskan ke *</label>
                                <select class="form-control form-control-sm select2" id="assigned_to" name="assigned_to" required>
                                    <option value="">Pilih User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} - {{ $user->position ?? 'No Position' }} ({{ $user->department->name ?? 'No Department' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id" class="font-weight-bold small">Departemen *</label>
                                <select class="form-control form-control-sm" id="department_id" name="department_id" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date" class="font-weight-bold small">Batas Waktu *</label>
                                <input type="date" class="form-control form-control-sm" id="due_date" name="due_date" 
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority" class="font-weight-bold small">Prioritas *</label>
                                <select class="form-control form-control-sm" id="priority" name="priority" required>
                                    <!-- PERBAIKAN: Urutan berdasarkan prioritas -->
                                    <option value="1">🔴 Tinggi</option>
                                    <option value="2" selected>🟡 Sedang</option>
                                    <option value="3">🟢 Rendah</option>
                                </select>
                                <small class="form-text text-muted">
                                    🔴 Tinggi = Sangat penting & mendesak<br>
                                    🟡 Sedang = Penting tapi tidak mendesak<br>
                                    🟢 Rendah = Biasa, bisa dikerjakan belakangan
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->isAdmin() || $meeting->organizer_id == auth()->id())
<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-upload mr-2"></i>Upload File Meeting
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.files.upload', $meeting) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file" class="font-weight-bold small">File *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" required>
                            <label class="custom-file-label" for="file">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Maksimal 10MB. Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="description" class="font-weight-bold small">Deskripsi File</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="2" 
                                  placeholder="Deskripsi singkat tentang file ini"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-info btn-sm">
                        <i class="fas fa-upload mr-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Minute Taker Modal -->
<div class="modal fade" id="assignMinuteTakerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-user-edit mr-2"></i>Tunjuk Penulis Notulensi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.assign-minute-taker', $meeting) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="minute_taker_id" class="font-weight-bold small">Pilih Penulis Notulensi *</label>
                        <select class="form-control form-control-sm select2" id="minute_taker_id" name="minute_taker_id" required>
                            <option value="">Pilih User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                    {{ $meeting->assigned_minute_taker_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->position ?? 'No Position' }} ({{ $user->department->name ?? 'No Department' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Penulis notulensi yang ditunjuk akan memiliki akses untuk membuat dan mengedit notulensi meeting ini.
                        </small>
                    </div>
                    
                    @if($meeting->assignedMinuteTaker)
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Saat ini: <strong>{{ $meeting->assignedMinuteTaker->name }}</strong>
                    </div>
                    @endif
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Action Taker Modal -->
<div class="modal fade" id="assignActionTakerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-user-plus mr-2"></i>Tunjuk Penulis Tindak Lanjut
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.assign-action-taker', $meeting) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="action_taker_id" class="font-weight-bold small">Pilih Penulis Tindak Lanjut *</label>
                        <select class="form-control form-control-sm select2" id="action_taker_id" name="action_taker_id" required>
                            <option value="">Pilih User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                    {{ $meeting->assigned_action_taker_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->position ?? 'No Position' }} ({{ $user->department->name ?? 'No Department' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            User yang ditunjuk akan memiliki akses untuk menambah tindak lanjut.
                        </small>
                    </div>
                    
                    @if($meeting->assignedActionTaker)
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Saat ini: <strong>{{ $meeting->assignedActionTaker->name }}</strong>
                    </div>
                    @endif
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Custom file input
document.querySelector('.custom-file-input')?.addEventListener('change', function(e) {
    var fileName = document.getElementById("file").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});

// Initialize Select2 if available
if (typeof $.fn.select2 !== 'undefined') {
    $('.select2').select2({
        placeholder: 'Pilih user',
        allowClear: true,
        width: '100%'
    });
}

// ── Score Rating Interactive ─────────────────────────────────
document.querySelectorAll('.score-slider').forEach(function(slider) {
    slider.addEventListener('input', function() {
        var inputId = this.dataset.inputTarget;
        var numberInput = document.getElementById(inputId);
        if (numberInput) {
            numberInput.value = this.value;
        }
    });
});

document.querySelectorAll('.score-number-input').forEach(function(input) {
    input.addEventListener('input', function() {
        var sliderId = this.dataset.sliderTarget;
        var slider = document.getElementById(sliderId);
        
        // Enforce min/max
        var val = parseInt(this.value);
        if (isNaN(val)) val = 0;
        if (val < 1 && this.value !== "") val = 1;
        if (val > 100) val = 100;
        
        // Only update value if it's out of bounds, let user type empty string temporarily
        if (this.value !== "" && parseInt(this.value) !== val) {
            this.value = val;
        }

        if (slider && val >= 1 && val <= 100) {
            slider.value = val;
        }
    });
    
    // Ensure value is set to at least 1 when leaving focus if empty
    input.addEventListener('blur', function() {
        if (!this.value || parseInt(this.value) < 1) {
            this.value = 1;
            var sliderId = this.dataset.sliderTarget;
            var slider = document.getElementById(sliderId);
            if (slider) slider.value = 1;
        }
    });
});
</script>
@endpush