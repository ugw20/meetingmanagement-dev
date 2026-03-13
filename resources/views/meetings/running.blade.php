<!-- resources/views/meetings/running.blade.php -->
@extends('layouts.app')

@section('title', 'Meeting Berlangsung - ' . $meeting->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meeting</a></li>
    <li class="breadcrumb-item"><a href="{{ route('meetings.show', $meeting) }}">{{ $meeting->title }}</a></li>
    <li class="breadcrumb-item active">Berlangsung</li>
@endsection

@php
    // AMBIL DATA DEPARTMENTS LANGSUNG DI VIEW JIKA TIDAK ADA DI CONTROLLER
    if (!isset($departments)) {
        $departments = \App\Models\Department::active()->get();
    }
@endphp

@section('content')
<style>
    .minute-taker-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 12px;
        height: 12px;
        background-color: #ffc107;
        border: 2px solid #fff;
        border-radius: 50%;
    }
    .action-taker-badge {
        position: absolute;
        top: -5px;
        left: -5px;
        width: 12px;
        height: 12px;
        background-color: #28a745;
        border: 2px solid #fff;
        border-radius: 50%;
    }
    .auto-resize {
        min-height: 120px;
        resize: none;
    }
    .minutes-preview {
        max-height: 300px;
        overflow-y: auto;
    }
    .file-icon {
        font-size: 1.5rem;
    }
    .priority-high { background-color: #dc3545; color: white; }
    .priority-medium { background-color: #ffc107; color: black; }
    .priority-low { background-color: #17a2b8; color: white; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Meeting Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-play-circle mr-2"></i>Meeting Sedang Berlangsung
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="py-4">
                        <i class="fas fa-video fa-4x text-success mb-3"></i>
                        <h4 class="text-success">Meeting "{{ $meeting->title }}" Sedang Berjalan</h4>
                        <p class="text-muted">Mulai: {{ $meeting->started_at->format('d M Y H:i') }}</p>
                        
                        <!-- Info Minute Taker -->
                        @if($meeting->assignedMinuteTaker)
                        <div class="alert alert-info d-inline-flex align-items-center">
                            <i class="fas fa-user-edit mr-2"></i>
                            <strong>Penulis Notulensi:</strong> 
                            <span class="ml-2">{{ $meeting->assignedMinuteTaker->name }}</span>
                            @if($meeting->assigned_minute_taker_id == auth()->id())
                            <span class="badge badge-warning badge-pill ml-2">Anda</span>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Info Action Taker -->
                        @if($meeting->assignedActionTaker)
                        <div class="alert alert-success d-inline-flex align-items-center">
                            <i class="fas fa-user-plus mr-2"></i>
                            <strong>Penulis Tindak Lanjut:</strong> 
                            <span class="ml-2">{{ $meeting->assignedActionTaker->name }}</span>
                            @if($meeting->assigned_action_taker_id == auth()->id())
                            <span class="badge badge-success badge-pill ml-2">Anda</span>
                            @endif
                        </div>
                        @endif
                        
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="h2 text-secondary">{{ $meeting->actionItems ? $meeting->actionItems->count() : 0 }}</div>
                                <small class="text-muted">Tindak Lanjut</small>
                            </div>
                            <div class="col-md-3">
                                <div class="h2 text-info">{{ $meeting->files ? $meeting->files->count() : 0 }}</div>
                                <small class="text-muted">File</small>
                            </div>
                            <div class="col-md-3">
                                <div class="h2 text-primary">{{ $meeting->participants->count() }}</div>
                                <small class="text-muted">Peserta</small>
                            </div>
                            <div class="col-md-3">
                                <div class="h2 text-warning">{{ $meeting->minutes ? 1 : 0 }}</div>
                                <small class="text-muted">Notulensi</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <form action="{{ route('meetings.complete', $meeting) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg" 
                                    onclick="return confirm('Selesaikan meeting? Tindakan ini tidak dapat dibatalkan.')">
                                <i class="fas fa-stop mr-1"></i> Akhiri Meeting
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FORM NOTULENSI - HANYA UNTUK MINUTE TAKER YANG DITUNJUK -->
            @if($meeting->assigned_minute_taker_id == auth()->id() || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
            <div class="card shadow-sm mb-4" id="minuteTakerForm">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-edit mr-2"></i>Form Notulensi Meeting
                        @if($meeting->assigned_minute_taker_id == auth()->id())
                        <span class="badge badge-warning badge-pill ml-2">Anda adalah Penulis Notulensi</span>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($meeting->minutes && $meeting->minutes->is_finalized)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Notulensi telah difinalisasi</strong> pada 
                        {{ $meeting->minutes->finalized_at->format('d M Y H:i') }}
                    </div>
                    @endif

                    <form action="{{ $meeting->minutes ? route('meetings.minutes.update', [$meeting, $meeting->minutes]) : route('meetings.minutes.store', $meeting) }}" method="POST">
                        @csrf
                        @if($meeting->minutes)
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="content" class="font-weight-bold">
                                <i class="fas fa-align-left mr-1"></i>Isi Notulensi *
                            </label>
                            <textarea class="form-control auto-resize @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="8" 
                                      placeholder="Tuliskan rangkuman dan poin-poin penting dari meeting ini..."
                                      {{ $meeting->minutes && $meeting->minutes->is_finalized ? 'disabled' : '' }}>{{ old('content', $meeting->minutes->content ?? '') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Jelaskan secara detail apa yang dibahas dalam meeting, keputusan yang diambil, dan hal penting lainnya.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="decisions" class="font-weight-bold">
                                <i class="fas fa-gavel mr-1"></i>Keputusan Meeting
                            </label>
                            <textarea class="form-control auto-resize @error('decisions') is-invalid @enderror" 
                                      id="decisions" 
                                      name="decisions" 
                                      rows="4" 
                                      placeholder="Tuliskan setiap keputusan dalam baris terpisah..."
                                      {{ $meeting->minutes && $meeting->minutes->is_finalized ? 'disabled' : '' }}>{{ old('decisions', $meeting->minutes ? implode("\n", $meeting->minutes->decisions ?? []) : '') }}</textarea>
                            @error('decisions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Masukkan setiap keputusan dalam baris terpisah. Contoh:<br>
                                - Disetujui pembelian software baru<br>
                                - Deadline project diperpanjang 1 minggu<br>
                                - Budget tambahan disetujui sebesar Rp 50 juta
                            </small>
                        </div>

                        @if(!$meeting->minutes || !$meeting->minutes->is_finalized)
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_finalized" name="is_finalized" value="1"
                                       {{ old('is_finalized', $meeting->minutes->is_finalized ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="is_finalized">
                                    <i class="fas fa-lock mr-1"></i>Finalisasi Notulensi
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Jika dicentang, notulensi tidak dapat diubah lagi. Pastikan semua informasi sudah benar sebelum memfinalisasi.
                            </small>
                        </div>
                        @endif

                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($meeting->minutes)
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Terakhir diperbarui: {{ $meeting->minutes->updated_at->format('d M Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                                <div>
                                    @if($meeting->minutes && $meeting->minutes->is_finalized)
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fas fa-lock mr-1"></i>Terkunci
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>
                                            {{ $meeting->minutes ? 'Perbarui' : 'Simpan' }} Notulensi
                                        </button>
                                        
                                        @if($meeting->minutes)
                                        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary ml-2">
                                            <i class="fas fa-times mr-1"></i>Batal
                                        </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <!-- Info untuk user yang tidak ditunjuk -->
            @if($meeting->assignedMinuteTaker)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h3 class="card-title m-0 text-muted">
                        <i class="fas fa-user-edit mr-2"></i>Informasi Notulensi
                    </h3>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-user-edit fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Penulis Notulensi</h5>
                    <p class="lead">{{ $meeting->assignedMinuteTaker->name }}</p>
                    <small class="text-muted">
                        {{ $meeting->assignedMinuteTaker->position }} - {{ $meeting->assignedMinuteTaker->department->name }}
                    </small>
                    
                    @if($meeting->minutes)
                    <div class="mt-3">
                        <a href="#minutesPreview" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye mr-1"></i>Lihat Notulensi
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning mt-3 small">
                        <i class="fas fa-clock mr-1"></i>
                        Notulensi belum dibuat
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h3 class="card-title m-0 text-muted">
                        <i class="fas fa-clipboard mr-2"></i>Notulensi Meeting
                    </h3>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Penulis Notulensi</h5>
                    <p class="text-muted small">
                        Organizer meeting belum menunjuk penulis notulensi untuk meeting ini.
                    </p>
                </div>
            </div>
            @endif
            @endif

            <!-- Tindak Lanjut Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-tasks mr-2"></i>Tindak Lanjut
                        <span class="badge badge-light badge-pill ml-1">{{ $meeting->actionItems ? $meeting->actionItems->count() : 0 }}</span>
                        
                        <!-- Info Action Taker -->
                        @if($meeting->assignedActionTaker)
                        <small class="float-right">
                            <i class="fas fa-user-plus mr-1"></i>Penulis: {{ $meeting->assignedActionTaker->name }}
                            @if($meeting->assigned_action_taker_id == auth()->id())
                            <span class="badge badge-success badge-pill ml-1">Anda</span>
                            @endif
                        </small>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
@if($meeting->actionItems && $meeting->actionItems->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Tugas</th>
                    <th>Penanggung Jawab</th>
                    <th>Batas Waktu</th>
                    <th>Status</th>
                    <th>Prioritas</th>
                    <th style="width: 80px;">Aksi</th> <!-- TAMBAHKAN KOLOM INI -->
                </tr>
            </thead>
            <tbody>
                @foreach($meeting->actionItems as $actionItem)
                <tr>
                    <td>
                        <strong>{{ $actionItem->title }}</strong>
                        @if($actionItem->description)
                        <br><small class="text-muted">{{ Str::limit($actionItem->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $actionItem->assignedTo->name ?? 'Tidak ada' }}
                        <br><small class="text-muted">{{ $actionItem->department->name ?? 'Tidak ada' }}</small>
                    </td>
                    <td>
                        {{ $actionItem->due_date->format('d M Y') }}
                        <br>
                        <small class="text-{{ $actionItem->due_date->isPast() ? 'danger' : 'muted' }}">
                            {{ $actionItem->due_date->diffForHumans() }}
                        </small>
                    </td>
                    <td>
                        @php
                            $statusBadge = [
                                'pending' => 'secondary',
                                'in_progress' => 'warning', 
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $statusText = [
                                'pending' => 'Menunggu',
                                'in_progress' => 'Dalam Proses',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusBadge[$actionItem->status] ?? 'secondary' }}">
                            {{ $statusText[$actionItem->status] ?? $actionItem->status }}
                        </span>
                    </td>
                    <td>
                        @php
                            $priorityBadge = [
                                '1' => 'danger',   // Tinggi = merah
                                '2' => 'warning',  // Sedang = kuning  
                                '3' => 'info'      // Rendah = biru
                            ];
                            $priorityText = [
                                '1' => 'Tinggi',
                                '2' => 'Sedang',
                                '3' => 'Rendah'
                            ];
                            $priorityIcon = [
                                '1' => '🔴',
                                '2' => '🟡', 
                                '3' => '🟢'
                            ];
                        @endphp
                        <span class="badge badge-{{ $priorityBadge[$actionItem->priority] ?? 'secondary' }}">
                            {{ $priorityIcon[$actionItem->priority] ?? '' }} {{ $priorityText[$actionItem->priority] ?? $actionItem->priority }}
                        </span>
                    </td>
                    <td>
                        <!-- TAMBAHKAN TOMBOL HAPUS -->
                        <div class="d-flex align-items-center">
                            <a href="{{ route('action-items.show', $actionItem) }}" 
                               class="btn btn-info btn-sm mr-1" 
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if(auth()->user()->canManageMeetings() || 
                                $meeting->organizer_id == auth()->id() || 
                                $actionItem->created_by == auth()->id())
                            <form action="{{ route('action-items.destroy', $actionItem) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirmDeleteActionItem(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm" 
                                        title="Hapus Tindak Lanjut">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-3">
        <i class="fas fa-tasks fa-2x text-muted mb-2"></i>
        <p class="text-muted mb-0">Belum ada tindak lanjut</p>
    </div>
@endif
                    
                    <!-- Tombol Tambah Tindak Lanjut untuk organizer, admin, dan assigned action taker -->
                    @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id())
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addActionItemModal">
                            <i class="fas fa-plus mr-1"></i> Tambah Tindak Lanjut
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- File Terupload Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-folder mr-2"></i>File Terupload
                        <span class="badge badge-light badge-pill ml-1">{{ $meeting->files ? $meeting->files->count() : 0 }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($meeting->files && $meeting->files->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($meeting->files as $file)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $fileIcon = 'fa-file text-secondary';
                                                $fileColor = 'text-secondary';
                                                
                                                if (str_contains($file->file_type, 'pdf')) {
                                                    $fileIcon = 'fa-file-pdf text-danger';
                                                    $fileColor = 'text-danger';
                                                } elseif (str_contains($file->file_type, 'word') || str_contains($file->file_type, 'document')) {
                                                    $fileIcon = 'fa-file-word text-primary';
                                                    $fileColor = 'text-primary';
                                                } elseif (str_contains($file->file_type, 'excel') || str_contains($file->file_type, 'sheet')) {
                                                    $fileIcon = 'fa-file-excel text-success';
                                                    $fileColor = 'text-success';
                                                } elseif (str_contains($file->file_type, 'image')) {
                                                    $fileIcon = 'fa-file-image text-info';
                                                    $fileColor = 'text-info';
                                                } elseif (str_contains($file->file_type, 'powerpoint') || str_contains($file->file_type, 'presentation')) {
                                                    $fileIcon = 'fa-file-powerpoint text-warning';
                                                    $fileColor = 'text-warning';
                                                }
                                            @endphp
                                            <i class="fas {{ $fileIcon }} file-icon mr-3"></i>
                                            <div>
                                                <h6 class="mb-1 {{ $fileColor }}">{{ $file->file_name }}</h6>
                                                @if($file->description)
                                                <p class="text-muted small mb-1">{{ $file->description }}</p>
                                                @endif
                                                <small class="text-muted">
                                                    Diupload oleh: {{ $file->uploader->name ?? 'Unknown' }} • 
                                                    {{ $file->created_at->format('d M Y H:i') }} • 
                                                    {{ round($file->file_size / 1024) }} KB
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('meetings.files.preview', [$meeting, $file]) }}" target="_blank"
                                           class="btn btn-sm btn-outline-info" title="Lihat/Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('meetings.files.download', [$meeting, $file]) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        
                                        @if($file->uploaded_by == auth()->id() || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                                        <form action="{{ route('meetings.files.delete', [$meeting, $file]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Hapus file ini?')">
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
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Belum ada file yang diupload</p>
                        </div>
                    @endif
                    
                    @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadFileModal">
                            <i class="fas fa-upload mr-1"></i> Upload File
                        </button>
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
                        <!-- Link ke form notulensi hanya untuk minute taker -->
                        @if($meeting->assigned_minute_taker_id == auth()->id() || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                        <a href="#minuteTakerForm" class="btn btn-outline-primary btn-sm text-left">
                            <i class="fas fa-edit mr-2"></i>
                            @if($meeting->minutes)
                                Edit Notulensi
                            @else
                                Buat Notulensi
                            @endif
                        </a>
                        @else
                        <a href="#minutesPreview" class="btn btn-outline-secondary btn-sm text-left">
                            <i class="fas fa-eye mr-2"></i>Lihat Notulensi
                        </a>
                        @endif
                        
                        <!-- Tombol Assign Minute Taker hanya untuk organizer/admin -->
                        @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                        <button type="button" class="btn btn-outline-warning btn-sm text-left" data-toggle="modal" data-target="#assignMinuteTakerModal">
                            <i class="fas fa-user-edit mr-2"></i>@if($meeting->assignedMinuteTaker) Ganti @endif Tunjuk Penulis Notulensi
                        </button>
                        
                        <!-- Tombol Assign Action Taker hanya untuk organizer/admin -->
                        <button type="button" class="btn btn-outline-success btn-sm text-left" data-toggle="modal" data-target="#assignActionTakerModal">
                            <i class="fas fa-user-plus mr-2"></i>@if($meeting->assignedActionTaker) Ganti @endif Tunjuk Penulis Tindak Lanjut
                        </button>
                        @endif
                        
                        <!-- Tombol Tambah Tindak Lanjut untuk organizer, admin, dan assigned action taker -->
                        @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id() || $meeting->assigned_action_taker_id == auth()->id())
                        <button type="button" class="btn btn-outline-success btn-sm text-left" data-toggle="modal" data-target="#addActionItemModal">
                            <i class="fas fa-plus-circle mr-2"></i>Tambah Tindak Lanjut
                        </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-warning btn-sm text-left" data-toggle="modal" data-target="#uploadFileModal">
                            <i class="fas fa-upload mr-2"></i>Upload File
                        </button>
                        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary btn-sm text-left">
                            <i class="fas fa-eye mr-2"></i>Lihat Detail Meeting
                        </a>
                    </div>
                </div>
            </div>

            <!-- Preview Notulensi untuk Semua Peserta -->
            <div class="card shadow-sm mb-4" id="minutesPreview">
                <div class="card-header bg-secondary text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-clipboard mr-2"></i>Preview Notulensi
                        @if($meeting->assignedMinuteTaker)
                        <small class="float-right">
                            <i class="fas fa-user-edit mr-1"></i>{{ $meeting->assignedMinuteTaker->name }}
                        </small>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($meeting->minutes)
                        <div class="minutes-content mb-3 minutes-preview">
                            <h6>Isi Notulensi:</h6>
                            <div class="border rounded p-3 bg-light small">
                                {{ $meeting->minutes->content }}
                            </div>
                            
                            <!-- Handle decisions -->
                            @if($meeting->minutes->decisions && count($meeting->minutes->decisions) > 0)
                            <h6 class="mt-3">Keputusan:</h6>
                            <ul class="list-group small">
                                @foreach($meeting->minutes->decisions as $decision)
                                    @if(!empty(trim($decision)))
                                    <li class="list-group-item d-flex align-items-center py-2">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        {{ $decision }}
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                @if($meeting->minutes->is_finalized)
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i> Telah Difinalisasi
                                </span>
                                @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-pencil-alt mr-1"></i> Draft
                                </span>
                                @endif
                                
                                <small class="text-muted">
                                    Oleh: {{ $meeting->minutes->minuteTaker->name ?? 'Unknown' }}
                                </small>
                            </div>
                            @if($meeting->minutes->finalized_at)
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-calendar mr-1"></i>
                                Finalisasi: {{ $meeting->minutes->finalized_at->format('d M Y H:i') }}
                            </small>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-clipboard fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Belum ada notulensi</p>
                            
                            @if(!$meeting->assignedMinuteTaker)
                            <small class="text-muted">
                                Menunggu penunjukan penulis notulensi
                            </small>
                            @else
                            <small class="text-muted">
                                Menunggu {{ $meeting->assignedMinuteTaker->name }} membuat notulensi
                            </small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Participants -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-users mr-2"></i>Peserta Meeting
                        <span class="badge badge-light badge-pill ml-1">{{ $meeting->participants->count() }}</span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($meeting->participants as $participant)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="mr-3 position-relative">
                                    <i class="fas fa-user-circle fa-lg 
                                        {{ $participant->role === 'chairperson' ? 'text-success' : 'text-muted' }}"></i>
                                    @if($participant->user_id == $meeting->assigned_minute_taker_id)
                                    <span class="minute-taker-badge" title="Penulis Notulensi"></span>
                                    @endif
                                    @if($participant->user_id == $meeting->assigned_action_taker_id)
                                    <span class="action-taker-badge" title="Penulis Tindak Lanjut"></span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="d-block">{{ $participant->user->name }}</strong>
                                    <small class="text-muted">{{ $participant->user->position ?? '-' }}</small>
                                    <small class="text-muted d-block">
                                        {{ $participant->user->department->name ?? '-' }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    @if($participant->user_id == $meeting->assigned_minute_taker_id)
                                    <span class="badge badge-warning small mb-1" title="Penulis Notulensi">
                                        <i class="fas fa-user-edit"></i> Notulis
                                    </span>
                                    <br>
                                    @endif
                                    @if($participant->user_id == $meeting->assigned_action_taker_id)
                                    <span class="badge badge-success small mb-1" title="Penulis Tindak Lanjut">
                                        <i class="fas fa-tasks"></i> Action
                                    </span>
                                    <br>
                                    @endif
                                    <span class="badge badge-{{ $participant->role === 'chairperson' ? 'success' : 'secondary' }}">
                                        {{ $participant->role === 'chairperson' ? 'Ketua' : 'Peserta' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Meeting Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-info-circle mr-2"></i>Informasi Meeting
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong><i class="fas fa-calendar mr-2 text-primary"></i>Jadwal:</strong><br>
                        <span class="ml-4">{{ $meeting->start_time->format('d M Y H:i') }} - {{ $meeting->end_time->format('H:i') }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-map-marker-alt mr-2 text-primary"></i>Lokasi:</strong><br>
                        <span class="ml-4">
                            @if($meeting->is_online)
                                <i class="fas fa-video text-info mr-1"></i>Online
                                @if($meeting->meeting_link)
                                <br><a href="{{ $meeting->meeting_link }}" target="_blank" class="small">Join Meeting</a>
                                @endif
                            @else
                                <i class="fas fa-building text-secondary mr-1"></i>{{ $meeting->location }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-tag mr-2 text-primary"></i>Jenis Meeting:</strong><br>
                        <span class="ml-4">{{ $meeting->meetingType->name }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-building mr-2 text-primary"></i>Departemen:</strong><br>
                        <span class="ml-4">{{ $meeting->department->name }}</span>
                    </div>

                    <div>
                        <strong><i class="fas fa-user-tie mr-2 text-primary"></i>Organizer:</strong><br>
                        <span class="ml-4">{{ $meeting->organizer->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Di Modal Add Action Item - PERBAIKAN -->
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
            <form action="{{ route('meetings.action-items.store', $meeting) }}" method="POST" id="addActionItemForm">
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
                                    @if($departments->count() > 0)
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    @else
                                        <!-- Fallback jika departments tidak ada -->
                                        <option value="{{ $meeting->department_id }}">{{ $meeting->department->name }}</option>
                                    @endif
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
                                    <!-- PERBAIKAN: Urutan sesuai yang user lihat -->
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
                            <i class="fas fa-info-circle mr-1"></i>Maksimal 10MB. Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG
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
                            User yang ditunjuk akan memiliki akses untuk menambah tindak lanjut selama meeting berlangsung.
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirm meeting completion
    const meetingCompleteForm = document.querySelector('form[action*="meetings.complete"]');
    if (meetingCompleteForm) {
        meetingCompleteForm.addEventListener('submit', function(e) {
            if (!confirm('Akhiri meeting ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    }
    
    // Handle form submissions dengan feedback
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';
                button.disabled = true;
                
                // Re-enable button setelah 5 detik untuk menghindari stuck
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 5000);
            }
        });
    });

    // Initialize Select2 jika ada
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            placeholder: 'Pilih user',
            allowClear: true,
            width: '100%'
        });
    }

    // Auto-resize textarea
    const textareas = document.querySelectorAll('.auto-resize');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        // Trigger initial resize
        textarea.dispatchEvent(new Event('input'));
    });

    // Custom file input
    document.querySelector('.custom-file-input')?.addEventListener('change', function(e) {
        var fileName = document.getElementById("file").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });

    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Handle form tambah tindak lanjut - VALIDASI PRIORITAS
const addActionForm = document.getElementById('addActionItemForm');
if (addActionForm) {
    addActionForm.addEventListener('submit', function(e) {
        const priority = document.getElementById('priority').value;
        const dueDate = document.getElementById('due_date').value;
        const title = document.getElementById('title').value;
        
        // Validasi due date tidak boleh hari kemarin
        if (dueDate && new Date(dueDate) < new Date().setHours(0,0,0,0)) {
            e.preventDefault();
            alert('❌ Batas waktu tidak boleh hari kemarin atau sebelumnya');
            return false;
        }
        
        // Validasi title tidak boleh kosong
        if (!title.trim()) {
            e.preventDefault();
            alert('❌ Judul tindak lanjut harus diisi');
            return false;
        }
        
        // Konfirmasi berdasarkan prioritas - SESUAI DENGAN NILAI YANG BARU
        if (priority === '1') { // Tinggi
            if (!confirm('🚨 ANDA MEMILIH PRIORITAS TINGGI!\n\nTindak lanjut ini akan ditandai sebagai sangat penting dan mendesak.\nPastikan ini benar-benar tugas yang kritis.\n\nLanjutkan?')) {
                e.preventDefault();
                return false;
            }
        } else if (priority === '2') { // Sedang
            if (!confirm('🟡 Anda memilih prioritas SEDANG.\n\nTindak lanjut ini penting tapi tidak mendesak.\n\nLanjutkan?')) {
                e.preventDefault();
                return false;
            }
        } else if (priority === '3') { // Rendah
            if (!confirm('🟢 Anda memilih prioritas RENDAH.\n\nTindak lanjut ini bersifat biasa dan bisa dikerjakan belakangan.\n\nLanjutkan?')) {
                e.preventDefault();
                return false;
            }
        }
    });
}
    
    // Auto-set department berdasarkan user yang dipilih
    const assignedToSelect = document.getElementById('assigned_to');
    const departmentSelect = document.getElementById('department_id');
    
    if (assignedToSelect && departmentSelect) {
        // Buat mapping user ke department
        const userDepartments = {
            @foreach($participants as $participant)
            '{{ $participant->id }}': '{{ $participant->department_id }}',
            @endforeach
        };
        
        assignedToSelect.addEventListener('change', function() {
            const userId = this.value;
            const departmentId = userDepartments[userId];
            
            if (departmentId && departmentId !== '') {
                departmentSelect.value = departmentId;
                
                // Jika department tidak ada di options, tambahkan
                if (!departmentSelect.querySelector(`option[value="${departmentId}"]`)) {
                    const user = {!! $participants->firstWhere('id', '==', ' + userId + ') ? json_encode($participants->firstWhere('id', '==', userId)) : 'null' !!};
                    if (user && user.department) {
                        const option = new Option(user.department.name, user.department.id, true, true);
                        departmentSelect.appendChild(option);
                        departmentSelect.value = user.department.id;
                    }
                }
            }
        });
    }

    // Set minimum date untuk due_date ke hari ini
    const dueDateInput = document.getElementById('due_date');
    if (dueDateInput) {
        const today = new Date().toISOString().split('T')[0];
        dueDateInput.min = today;
        
        // Set default value ke 7 hari dari sekarang
        const nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        const nextWeekFormatted = nextWeek.toISOString().split('T')[0];
        dueDateInput.value = nextWeekFormatted;
    }
});

// Simple toast notification
function showToast(message, type = 'info') {
    // Hapus toast sebelumnya jika ada
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show custom-toast position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <strong>${type === 'success' ? '✅ Sukses!' : type === 'danger' ? '❌ Error!' : 'ℹ️ Info!'}</strong> ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    document.body.appendChild(toast);
    
    // Auto-hide setelah 3 detik
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

// Di section scripts - TAMBAHKAN FUNGSI KONFIRMASI HAPUS
function confirmDeleteActionItem(form) {
    const actionItemTitle = form.closest('tr').querySelector('td strong').textContent;
    
    if (!confirm(`Hapus tindak lanjut "${actionItemTitle.trim()}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        return false;
    }
    
    // Tampilkan loading
    const button = form.querySelector('button[type="submit"]');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    return true;
}

// Fungsi untuk hapus dengan AJAX (opsional)
function deleteActionItem(actionItemId, actionItemTitle) {
    if (!confirm(`Hapus tindak lanjut "${actionItemTitle}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        return;
    }
    
    // Tampilkan loading
    const button = event.target;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    // AJAX delete
    fetch(`/action-items/${actionItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (response.ok) {
            // Reload halaman setelah sukses
            window.location.reload();
        } else {
            throw new Error('Gagal menghapus tindak lanjut');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus tindak lanjut');
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

// Handle AJAX errors globally
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    showToast('Terjadi kesalahan sistem', 'danger');
});
</script>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast('{{ session('success') }}', 'success');
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast('{{ session('error') }}', 'danger');
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to hash if present in URL
    if (window.location.hash) {
        const targetElement = document.querySelector(window.location.hash);
        if (targetElement) {
            // Add a slight delay to ensure the page has completely rendered
            setTimeout(() => {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Optional: add a highlight effect to draw attention
                targetElement.classList.add('border-primary', 'shadow');
                setTimeout(() => {
                    targetElement.classList.remove('border-primary', 'shadow');
                }, 2000);
            }, 300);
        }
    }
});
</script>
@endsection