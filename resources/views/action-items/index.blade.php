<!-- resources/views/action-items/index.blade.php -->
@extends('layouts.app')

@section('title', 'Tindak Lanjut')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tindak Lanjut</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Tindak Lanjut</h3>
        <div class="card-tools">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Filter Status
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">Semua</a>
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Belum Dikerjakan</a>
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'in_progress']) }}">Sedang Dikerjakan</a>
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}">Selesai</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs berdasarkan role -->
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="actionItemTabs" role="tablist">
            @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link {{ $type === 'all' ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['type' => 'all']) }}">
                    <i class="fas fa-list mr-1"></i>
                    Semua Tindak Lanjut
                    <span class="badge badge-primary ml-1">{{ $stats['all'] ?? 0 }}</span>
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ $type === 'created' ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['type' => 'created']) }}">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Yang Saya Buat
                    <span class="badge badge-info ml-1">{{ $stats['created'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type === 'assigned' ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['type' => 'assigned']) }}">
                    <i class="fas fa-user-check mr-1"></i>
                    Tugas Saya
                    <span class="badge badge-success ml-1">{{ $stats['assigned'] ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>
    @endif

    <div class="card-body">
        <!-- Info Tab Aktif -->
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                @if($type === 'all')
                    Menampilkan <strong>semua tindak lanjut</strong> dalam sistem.
                @elseif($type === 'created')
                    Menampilkan <strong>tindak lanjut yang Anda buat</strong> dalam berbagai meeting.
                @else
                    Menampilkan <strong>tindak lanjut yang ditugaskan kepada Anda</strong>.
                @endif
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @else
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                Menampilkan <strong>tindak lanjut yang ditugaskan kepada Anda</strong>.
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Meeting</th>
                        <th>Ditugaskan ke</th>
                        <th>Departemen</th>
                        <th>Batas Waktu</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actionItems as $item)
                    @php
                        $isAssignedToMe = $item->assigned_to == auth()->id();
                        $isCreatedByMe = $item->meeting && $item->meeting->organizer_id == auth()->id();
                        $meetingDeleted = !$item->meeting;
                        $canEditOrDelete = auth()->user()->canManageMeetings() || 
                                         ($item->meeting && $item->meeting->organizer_id == auth()->id()) || 
                                         $item->created_by == auth()->id();
                    @endphp
                    
                    <tr class="{{ $isAssignedToMe ? 'table-warning' : '' }} {{ $isCreatedByMe ? 'table-info' : '' }} {{ $meetingDeleted ? 'table-danger' : '' }}">
                        <td>
                            <div>
                                <strong>{{ $item->title }}</strong>
                                @if($isAssignedToMe)
                                <span class="badge badge-warning badge-pill ml-1 small">Tugas Anda</span>
                                @endif
                                @if($isCreatedByMe)
                                <span class="badge badge-info badge-pill ml-1 small">Anda yang Buat</span>
                                @endif
                                @if($meetingDeleted)
                                <span class="badge badge-danger badge-pill ml-1 small">Meeting Dihapus</span>
                                @endif
                            </div>
                            @if($item->description)
                            <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($item->meeting)
                                <a href="{{ route('meetings.show', $item->meeting) }}" class="text-decoration-none">
                                    {{ Str::limit($item->meeting->title, 30) }}
                                </a>
                            @else
                                <span class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Meeting telah dihapus
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle mr-2 
                                    {{ $isAssignedToMe ? 'text-warning' : 'text-muted' }}"></i>
                                <div>
                                    <strong class="{{ $isAssignedToMe ? 'text-warning' : '' }}">
                                        {{ $item->assignedTo->name }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">{{ $item->assignedTo->position }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $item->department->name }}</td>
                        <td>
                            <span class="badge badge-{{ $item->isOverdue() ? 'danger' : 'secondary' }}">
                                {{ $item->due_date->format('d M Y') }}
                            </span>
                            @if($item->isOverdue())
                            <br><small class="text-danger">Terlambat {{ $item->due_date->diffInDays(now()) }} hari</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $item->status_badge }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $item->priority_badge }}">
                                {{ $item->priority_label }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('action-items.show', $item) }}" 
                                   class="btn btn-info" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($canEditOrDelete)
                                <a href="{{ route('action-items.edit', $item) }}" 
                                   class="btn btn-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('action-items.destroy', $item) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirmDeleteActionItem('{{ addslashes($item->title) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-tasks fa-3x mb-3"></i>
                                <h5>Tidak ada tindak lanjut</h5>
                                <p>
                                    @if(auth()->user()->isAdmin())
                                        @if($type === 'all')
                                            Tidak ada tindak lanjut dalam sistem.
                                        @elseif($type === 'created')
                                            Anda belum membuat tindak lanjut apapun.
                                        @else
                                            Tidak ada tindak lanjut yang ditugaskan kepada Anda.
                                        @endif
                                    @elseif(auth()->user()->isManager())
                                        @if($type === 'created')
                                            Anda belum membuat tindak lanjut apapun.
                                        @else
                                            Tidak ada tindak lanjut yang ditugaskan kepada Anda.
                                        @endif
                                    @else
                                        Tidak ada tindak lanjut yang ditugaskan kepada Anda.
                                    @endif
                                </p>
                                @if((auth()->user()->isAdmin() || auth()->user()->isManager()) && $type === 'created')
                                <a href="{{ route('meetings.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Buat Meeting Baru
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $actionItems->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Fungsi konfirmasi hapus dengan detail
function confirmDeleteActionItem(title) {
    return confirm(`Hapus tindak lanjut "${title}"?\n\nTindakan ini tidak dapat dibatalkan dan semua data terkait akan dihapus permanen!`);
}

// Handle loading state untuk semua form hapus
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('form[action*="action-items"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button && button.innerHTML.includes('fa-trash')) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                button.title = 'Menghapus...';
                
                // Re-enable button setelah 5 detik untuk menghindari stuck
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    button.title = 'Hapus';
                }, 5000);
            }
        });
    });

    // Auto-hide alert setelah 5 detik
    const autoHideAlert = document.querySelector('.alert-info');
    if (autoHideAlert) {
        setTimeout(() => {
            if (autoHideAlert) {
                autoHideAlert.remove();
            }
        }, 5000);
    }
});

// Toast notification untuk feedback
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

<style>
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 12px 20px;
    transition: all 0.3s ease;
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

.table-warning {
    background-color: #fff3cd !important;
}

.table-info {
    background-color: #d1ecf1 !important;
}

.table-danger {
    background-color: #f8d7da !important;
}

.btn-group-sm > .btn {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 0.25rem;
    transition: all 0.3s ease;
}

.btn-group-sm > .btn:not(:last-child) {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group-sm > .btn:not(:first-child) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    margin-left: -1px;
}

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

.text-decoration-none:hover {
    text-decoration: underline !important;
}

.badge-pill {
    font-size: 0.7rem;
    padding: 0.25em 0.6em;
}

/* Hover effects untuk rows */
.table-hover tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Responsive design */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm > .btn {
        padding: 2px 4px;
        font-size: 10px;
    }
    
    .nav-tabs .nav-link {
        padding: 8px 12px;
        font-size: 0.875rem;
    }
    
    .card-header .card-title {
        font-size: 1.1rem;
    }
    
    .table td, .table th {
        padding: 0.5rem;
    }
}

/* Empty state styling */
.text-center.py-4 {
    background: #f8f9fa;
    border-radius: 8px;
}

.text-center.py-4 .fa-tasks {
    opacity: 0.5;
}

/* Badge styling */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.badge-primary { background-color: #007bff; }
.badge-info { background-color: #17a2b8; }
.badge-success { background-color: #28a745; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; }
.badge-secondary { background-color: #6c757d; }

/* Alert styling */
.alert {
    border: none;
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
    background-color: #f8f9fa;
}

/* Card header styling */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    color: #495057;
    font-weight: 600;
}
</style>
@endsection