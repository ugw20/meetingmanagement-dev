@extends('layouts.app')

@section('title', 'Detail Tindak Lanjut - ' . $actionItem->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('action-items.index') }}">Tindak Lanjut</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-tasks mr-2 text-primary"></i>Detail Tindak Lanjut
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-white mb-3">
                            <span class="info-box-icon bg-primary"><i class="fas fa-heading text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Judul Tindak Lanjut</span>
                                <span class="info-box-number">{{ $actionItem->title }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-white mb-3">
                            <span class="info-box-icon bg-info"><i class="fas fa-calendar text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Batas Waktu</span>
                                <span class="info-box-number">
                                    <span class="badge badge-{{ $actionItem->isOverdue() ? 'danger' : 'secondary' }}">
                                        {{ $actionItem->due_date->format('d F Y') }}
                                    </span>
                                    @if($actionItem->isOverdue())
                                    <br>
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Terlambat {{ $actionItem->due_date->diffInDays(now()) }} hari
                                    </small>
                                    @elseif($actionItem->isCompletedLate())
                                    <br>
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Selesai Terlambat {{ $actionItem->due_date->startOfDay()->diffInDays($actionItem->completed_at->startOfDay()) }} hari
                                    </small>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-box bg-white mb-3">
                            <span class="info-box-icon bg-warning"><i class="fas fa-user-tie text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ditugaskan ke</span>
                                <span class="info-box-number">{{ $actionItem->assignedTo->name }}</span>
                                <small class="text-muted">{{ $actionItem->assignedTo->position }}</small>
                            </div>
                        </div>
                        
                        <div class="info-box bg-white mb-3">
                            <span class="info-box-icon bg-success"><i class="fas fa-building text-white"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Departemen</span>
                                <span class="info-box-number">{{ $actionItem->department->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="callout callout-{{ $actionItem->status === 'completed' ? 'success' : ($actionItem->status === 'needs_revision' ? 'danger' : ($actionItem->status === 'in_progress' ? 'info' : ($actionItem->status === 'waiting_review' ? 'warning' : 'secondary'))) }}">
                            <h6 class="mb-1"><i class="fas fa-flag mr-2"></i>Status</h6>
                            <span class="badge badge-{{ $actionItem->status_badge }}">
                                {{ $actionItem->status_label }}
                            </span>
                            @if($actionItem->completed_at && $actionItem->status === 'completed')
                            <br>
                            <small class="text-muted">
                                Diselesaikan: {{ $actionItem->completed_at->format('d M Y H:i') }}
                            </small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-{{ $actionItem->priority === 3 ? 'danger' : ($actionItem->priority === 2 ? 'warning' : 'success') }}">
                            <h6 class="mb-1"><i class="fas fa-exclamation-circle mr-2"></i>Prioritas</h6>
                            <span class="badge badge-{{ $actionItem->priority_badge }}">
                                {{ $actionItem->priority_label }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($actionItem->revision_notes && $actionItem->status == 'needs_revision')
                <div class="callout callout-danger shadow-sm mb-4">
                    <h6 class="font-weight-bold mb-2 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Tugas Dikembalikan (Perlu Revisi)</h6>
                    <p class="mb-0 text-sm">Penyelenggara menolak laporan Anda dengan catatan berikut:</p>
                    <hr class="border-danger my-2">
                    <div class="bg-white text-dark p-2 rounded text-sm">
                        {{ $actionItem->revision_notes }}
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <h6 class="text-primary mb-2"><i class="fas fa-align-left mr-2"></i>Deskripsi</h6>
                    <div class="border rounded p-3 bg-light">
                        {{ $actionItem->description }}
                    </div>
                </div>

                @if($actionItem->completion_notes)
                <div class="mb-3">
                    <h6 class="text-success mb-2"><i class="fas fa-check-circle mr-2"></i>Catatan Penyelesaian</h6>
                    <div class="border rounded p-3 bg-light">
                        {{ $actionItem->completion_notes }}
                    </div>
                </div>
                @endif

                @if($actionItem->meeting)
                <div class="mb-3">
                    <h6 class="text-primary mb-2"><i class="fas fa-users mr-2"></i>Meeting Asal</h6>
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $actionItem->meeting->title }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $actionItem->meeting->start_time->format('d F Y H:i') }} - 
                                    {{ $actionItem->meeting->end_time->format('H:i') }}
                                </small>
                            </div>
                            <a href="{{ route('meetings.show', $actionItem->meeting) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye mr-1"></i> Lihat Meeting
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Meeting asal telah dihapus.</strong> Tindak lanjut ini tetap tersedia untuk tracking.
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div>
                    <a href="{{ route('action-items.index') }}" class="btn btn-secondary btn-sm mr-2 mb-1">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                    
                    @if(auth()->user()->isAdmin() || (isset($actionItem->meeting) && $actionItem->meeting->organizer_id == auth()->id()))
                    <a href="{{ route('action-items.edit', $actionItem) }}" class="btn btn-primary btn-sm mr-2 mb-1">
                        <i class="fas fa-edit mr-1"></i> Edit Detail
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-file mr-2 text-primary"></i>File Lampiran
                    <span class="badge badge-info badge-pill ml-1">{{ $actionItem->files->count() }}</span>
                </h3>
                @if($actionItem->assigned_to == auth()->id() && in_array($actionItem->status, ['pending', 'in_progress', 'needs_revision']))
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadFileModal">
                        <i class="fas fa-upload mr-1"></i> Upload Bukti
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                @if($actionItem->files->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($actionItem->files as $file)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-file text-primary mr-2"></i>
                                    <strong class="text-truncate">{{ $file->file_name }}</strong>
                                </div>
                                <div class="ml-4">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-hdd mr-1"></i>{{ $file->file_size_formatted }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user mr-1"></i>{{ $file->uploader->name }}
                                    </small>
                                    @if($file->description)
                                    <small class="text-dark d-block mt-1 p-1 bg-light border-left border-primary" style="font-style: italic;">
                                        <i class="fas fa-comment-alt mr-1 text-primary"></i> "{{ $file->description }}"
                                    </small>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('action-items.preview-file', [$actionItem, $file]) }}" target="_blank" class="btn btn-info btn-sm mr-1" title="Lihat/Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('action-items.download-file', [$actionItem, $file]) }}" class="btn btn-success btn-sm" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($file->uploaded_by == auth()->id() && in_array($actionItem->status, ['pending', 'in_progress', 'needs_revision']))
                                <form action="{{ route('action-items.delete-file', [$actionItem, $file]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus file ini?')" title="Hapus">
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
                    <p class="text-muted mb-0">Belum ada file bukti yang diunggah.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-info-circle mr-2 text-primary"></i>Informasi
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">
                        <i class="fas fa-user-plus mr-2"></i>
                        <strong>Dibuat oleh:</strong> 
                        {{ $actionItem->meeting->organizer->name ?? 'Unknown' }}
                    </small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        <strong>Dibuat pada:</strong> 
                        {{ $actionItem->created_at->format('d M Y H:i') }}
                    </small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar-check mr-2"></i>
                        <strong>Diupdate pada:</strong> 
                        {{ $actionItem->updated_at->format('d M Y H:i') }}
                    </small>
                </div>
                
                @if($actionItem->assigned_to == auth()->id() || (isset($actionItem->meeting) && auth()->id() == $actionItem->meeting->organizer_id) || auth()->user()->isAdmin())
                <hr>
                <div class="mt-3">
                    <h6 class="text-primary mb-3"><i class="fas fa-tasks mr-1"></i> Aksi Tindak Lanjut</h6>

                    @if($actionItem->status == 'completed')
                        <div class="alert alert-success py-2 m-0"><i class="fas fa-check-circle mr-1"></i> Tugas telah diverifikasi & selesai.</div>
                    @elseif($actionItem->status == 'cancelled')
                        <div class="alert alert-danger py-2 m-0"><i class="fas fa-times-circle mr-1"></i> Tugas dibatalkan.</div>
                    @else
                        
                        @if(auth()->id() == $actionItem->assigned_to)
                            @if($actionItem->status == 'pending')
                                <form action="{{ route('action-items.update-status', $actionItem) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                                        <i class="fas fa-play mr-1"></i> Mulai Kerjakan
                                    </button>
                                </form>
                            @elseif($actionItem->status == 'in_progress' || $actionItem->status == 'needs_revision')
                                @if($actionItem->files->count() == 0)
                                    <div class="callout callout-warning py-2 text-sm m-0 mb-2">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Pekerjaan berlangsung.</strong><br>
                                        Wajib <a href="#uploadFileModal" data-toggle="modal" class="text-primary font-weight-bold">Upload File Bukti</a> di bawah sebelum melaporkan selesai.
                                    </div>
                                @else
                                    <form action="{{ route('action-items.update-status', $actionItem) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="waiting_review">
                                        <button type="submit" class="btn btn-warning btn-sm w-100 mb-2" onclick="return confirm('Kirim ke penyelenggara untuk direview?')">
                                            <i class="fas fa-paper-plane mr-1"></i> Lapor Selesai (Minta Review)
                                        </button>
                                    </form>
                                @endif
                            @elseif($actionItem->status == 'waiting_review')
                                <div class="callout callout-info py-2 text-sm m-0 mb-2">
                                    <i class="fas fa-hourglass-half fa-spin mr-1"></i> Menunggu direview Penyelenggara.
                                </div>
                            @endif
                        @endif

                        @if(auth()->user()->isAdmin() || (isset($actionItem->meeting) && auth()->id() == $actionItem->meeting->organizer_id))
                            @if($actionItem->status == 'waiting_review')
                                <div class="callout callout-info py-2 text-sm mb-2">
                                    <i class="fas fa-bell mr-1"></i> Penerima tugas telah melaporkan selesai. Silakan cek file bukti di bawah.
                                </div>

                                <form action="{{ route('action-items.update-status', $actionItem) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success btn-sm w-100 mb-2" onclick="return confirm('Tugas sudah sesuai? Tutup tugas ini.')">
                                        <i class="fas fa-check-double mr-1"></i> Verifikasi & Tutup Tugas
                                    </button>
                                </form>
                                <form action="{{ route('action-items.update-status', $actionItem) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="needs_revision">
                                    <div class="form-group mb-2">
                                        <textarea name="revision_notes" class="form-control form-control-sm" rows="3" required placeholder="Catatan revisi untuk penerima tugas..." style="display: none;" id="revision_notes_input"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="document.getElementById('revision_notes_input').style.display = 'block'; this.style.display = 'none'; document.getElementById('submit_revision_btn').style.display = 'block';">
                                        <i class="fas fa-undo mr-1"></i> Tolak (Minta Revisi)
                                    </button>
                                    <button type="submit" class="btn btn-danger btn-sm w-100 mb-2" id="submit_revision_btn" style="display: none;">
                                        <i class="fas fa-paper-plane mr-1"></i> Kirim Revisi
                                    </button>
                                </form>
                            @elseif(in_array($actionItem->status, ['pending', 'in_progress', 'needs_revision']))
                                <form action="{{ route('action-items.update-status', $actionItem) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Yakin membatalkan tugas ini secara paksa?')">
                                        <i class="fas fa-ban mr-1"></i> Batalkan Tugas
                                    </button>
                                </form>
                            @endif
                        @endif

                    @endif
                </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h3 class="card-title m-0">
                    <i class="fas fa-bolt mr-2 text-primary"></i>Aksi Cepat
                </h3>
            </div>
            <div class="card-body">
                <div>
                    <a href="{{ route('action-items.index') }}" class="btn btn-secondary btn-sm mb-2 w-100 text-left">
                        <i class="fas fa-list mr-2"></i> Semua Tindak Lanjut
                    </a>
                    
                    @if($actionItem->meeting)
                    <a href="{{ route('meetings.show', $actionItem->meeting) }}" class="btn btn-info btn-sm mb-2 w-100 text-left">
                        <i class="fas fa-users mr-2"></i> Ke Meeting Asal
                    </a>
                    @endif
                    
                    @if(auth()->user()->isAdmin() || (isset($actionItem->meeting) && $actionItem->meeting->organizer_id == auth()->id()))
                    <form action="{{ route('action-items.destroy', $actionItem) }}" method="POST" class="d-inline" onsubmit="return confirmDeleteActionItem('{{ addslashes($actionItem->title) }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100 text-left">
                            <i class="fas fa-trash mr-2"></i> Hapus Tindak Lanjut
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin() || (isset($actionItem->meeting) && auth()->id() == $actionItem->meeting->organizer_id))
<div class="modal fade" id="revisiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title m-0"><i class="fas fa-undo mr-2"></i>Tolak Laporan & Minta Revisi</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('action-items.update-status', $actionItem) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="needs_revision">
                <div class="modal-body">
                    <div class="alert alert-warning text-sm">
                        <i class="fas fa-info-circle mr-1"></i> Status tugas akan diubah menjadi <b>Perlu Revisi</b>.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Catatan Revisi untuk Penerima Tugas *</label>
                        <textarea name="revision_notes" class="form-control" rows="4" required placeholder="Contoh: Tolong perbaiki file laporan bulanannya, masih ada data yang kurang..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-paper-plane mr-1"></i>Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($actionItem->assigned_to == auth()->id())
<div class="modal fade" id="uploadFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title m-0"><i class="fas fa-upload mr-2"></i>Upload File Bukti</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('action-items.upload-file', $actionItem) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file" class="font-weight-bold small">File *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" required>
                            <label class="custom-file-label" for="file">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted"><i class="fas fa-info-circle mr-1"></i>Maksimal 10MB</small>
                    </div>
                    <div class="form-group">
                        <label for="description" class="font-weight-bold small">Deskripsi File</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="2" placeholder="Deskripsi singkat tentang file ini"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload mr-1"></i>Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
// Custom file input name display
document.querySelector('.custom-file-input')?.addEventListener('change', function(e) {
    var fileName = document.getElementById("file").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});

// Hapus Konfirmasi
function confirmDeleteActionItem(title) {
    return confirm(`Hapus tindak lanjut "${title}"?\n\nTindakan ini tidak dapat dibatalkan dan semua data terkait akan dihapus permanen!`);
}

// Loading state untuk hapus
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('form[action*="action-items"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button && button.innerHTML.includes('fa-trash')) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...';
                button.disabled = true;
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 5000);
            }
        });
    });
});
</script>

<style>
.btn-danger {
    transition: all 0.3s ease;
}
.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: scale(1.05);
}
.fa-spinner {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #e3e6f0;
}
</style>
@endsection