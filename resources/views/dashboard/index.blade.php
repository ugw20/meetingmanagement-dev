<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-success rounded-lg shadow-sm">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $totalActions }}</h3>
                    <p class="mb-0">Total Tindak Lanjut</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="{{ route('action-items.index') }}" class="small-box-footer">
                    Lihat detail <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-info rounded-lg shadow-sm">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $totalMeetings }}</h3>
                    <p class="mb-0">Total Meeting</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('meetings.index') }}" class="small-box-footer">
                    Lihat detail <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-warning rounded-lg shadow-sm">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $scheduledMeetings }}</h3>
                    <p class="mb-0">Meeting Terjadwal</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('meetings.index') }}?status=scheduled" class="small-box-footer">
                    Lihat detail <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-danger rounded-lg shadow-sm">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $overdueActions }}</h3>
                    <p class="mb-0">Tugas Terlambat</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('action-items.index') }}" class="small-box-footer">
                    Lihat detail <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Diagram Garis Utama -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-chart-line mr-2 text-primary"></i>Trend Tindak Lanjut (30 Hari Terakhir)
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary active" data-chart-period="30" data-chart-type="action">
                                30 Hari
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="actionTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-chart-line mr-2 text-primary"></i>Trend Meeting (30 Hari Terakhir)
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary active" data-chart-period="30" data-chart-type="meeting">
                                30 Hari
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="meetingTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Tugas per User -->
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-users mr-2 text-primary"></i>Statistik Tugas per Penugasan
                </h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" data-display-type="chart">
                        <i class="fas fa-chart-line"></i> Grafik
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-display-type="table">
                        <i class="fas fa-table"></i> Tabel
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Diagram Garis -->
            <div id="user-chart-container">
                <div class="chart-container" style="height: 250px;">
                    <canvas id="userAssignmentChart"></canvas>
                </div>
            </div>
            
            <!-- Tabel -->
            <div id="user-table-container" class="d-none">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama User</th>
                                <th>Departemen</th>
                                <th>Total Tugas</th>
                                <th>Selesai</th>
                                <th>Sedang Dikerjakan</th>
                                <th>Belum Dikerjakan</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userAssignmentStats as $user)
                            @php
                                $progress = $user->total_assigned > 0 ? 
                                    ($user->completed_actions / $user->total_assigned) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-muted mr-2"></i>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $user->position }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->department->name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $user->total_assigned }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">{{ $user->completed_actions }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">{{ $user->in_progress_actions }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger">{{ $user->pending_actions }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $progress }}%">
                                            {{ number_format($progress, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistik Detail -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-chart-pie mr-2 text-primary"></i>Statistik Tindak Lanjut
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary active" data-display-type="chart">
                                <i class="fas fa-chart-bar"></i> Chart
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-display-type="numbers">
                                <i class="fas fa-table"></i> Angka
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Diagram Batang -->
                    <div id="action-chart-container">
                        <div class="chart-container" style="height: 200px;">
                            <canvas id="actionStatusChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Statistik Angka -->
                    <div id="action-numbers-container" class="d-none">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="info-box bg-success rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Selesai</span>
                                        <span class="info-box-number">{{ $completedActions }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $totalActions > 0 ? ($completedActions/$totalActions)*100 : 0 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $totalActions > 0 ? number_format(($completedActions/$totalActions)*100, 1) : 0 }}% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="info-box bg-warning rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Progress</span>
                                        <span class="info-box-number">{{ $inProgressActions }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $totalActions > 0 ? ($inProgressActions/$totalActions)*100 : 0 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $totalActions > 0 ? number_format(($inProgressActions/$totalActions)*100, 1) : 0 }}% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="info-box bg-danger rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending</span>
                                        <span class="info-box-number">{{ $pendingActions }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $totalActions > 0 ? ($pendingActions/$totalActions)*100 : 0 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $totalActions > 0 ? number_format(($pendingActions/$totalActions)*100, 1) : 0 }}% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-chart-bar mr-2 text-primary"></i>Statistik Meeting
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary active" data-display-type="chart">
                                <i class="fas fa-chart-bar"></i> Chart
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-display-type="numbers">
                                <i class="fas fa-table"></i> Angka
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Diagram Batang -->
                    <div id="meeting-chart-container">
                        <div class="chart-container" style="height: 200px;">
                            <canvas id="meetingStatusChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Statistik Angka -->
                    <div id="meeting-numbers-container" class="d-none">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="info-box bg-info rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total</span>
                                        <span class="info-box-number">{{ $totalMeetings }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-primary rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Terjadwal</span>
                                        <span class="info-box-number">{{ $scheduledMeetings }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-warning rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Berlangsung</span>
                                        <span class="info-box-number">{{ $ongoingMeetings }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-success rounded-lg">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Selesai</span>
                                        <span class="info-box-number">{{ $completedMeetings }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel dan Sidebar -->
    <div class="row">
        <!-- Tabel Tindak Lanjut Terbaru -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-tasks mr-2 text-primary"></i>Tindak Lanjut Terbaru
                        </h5>
                        <a href="{{ route('action-items.index') }}" class="btn btn-primary btn-sm rounded-pill">
                            <i class="fas fa-list mr-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">Judul</th>
                                    <th class="border-0">Meeting</th>
                                    <th class="border-0">Ditugaskan ke</th>
                                    <th class="border-0">Batas Waktu</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Prioritas</th>
                                    <th class="border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActionItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($item->title, 35) }}</strong>
                                        @if($item->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->meeting)
                                            <a href="{{ route('meetings.show', $item->meeting) }}" class="text-decoration-none">
                                                {{ Str::limit($item->meeting->title, 20) }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle text-muted mr-2"></i>
                                            <span>{{ $item->assignedTo->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->isOverdue() ? 'danger' : 'secondary' }} rounded-pill">
                                            {{ $item->due_date->format('d M Y') }}
                                        </span>
                                        @if($item->isOverdue())
                                        <br>
                                        <small class="text-danger">Terlambat</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->status_badge }} rounded-pill">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->priority_badge }} rounded-pill">
                                            {{ $item->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('action-items.show', $item) }}" class="btn btn-info btn-sm rounded-circle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-tasks fa-2x mb-3"></i>
                                        <p class="mb-0">Tidak ada tindak lanjut terbaru</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Kanan -->
        <div class="col-lg-4">
            <!-- Tugas Perlu Perhatian -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-warning text-white py-3 border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Tugas Perlu Perhatian
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($attentionActions as $action)
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <span class="text-dark">
                                            {{ Str::limit($action->title, 30) }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-user mr-1"></i>{{ $action->assignedTo->name }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $action->due_date->format('d M Y') }}
                                    </small>
                                    <div class="mt-1">
                                        <span class="badge badge-{{ $action->isOverdue() ? 'danger' : 'warning' }} badge-sm rounded-pill">
                                            {{ $action->isOverdue() ? 'TERLAMBAT' : 'PRIORITAS TINGGI' }}
                                        </span>
                                        <span class="badge badge-{{ $action->status_badge }} badge-sm rounded-pill">
                                            {{ $action->status_label }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('action-items.show', $action) }}" class="btn btn-info btn-sm rounded-circle ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4 border-0">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p class="mb-0">Tidak ada tugas yang perlu perhatian</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Meeting Mendatang -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-info text-white py-3 border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock mr-2"></i>Meeting Mendatang
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingMeetings as $meeting)
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        @if($meeting)
                                            <a href="{{ route('meetings.show', $meeting) }}" class="text-dark text-decoration-none">
                                                {{ Str::limit($meeting->title, 25) }}
                                            </a>
                                        @else
                                            <span class="text-dark">Meeting tidak tersedia</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $meeting->start_time->format('d M H:i') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-user-tie mr-1"></i>{{ $meeting->organizer->name }}
                                    </small>
                                    <div class="mt-1">
                                        <span class="badge badge-{{ $meeting->is_online ? 'info' : 'secondary' }} badge-sm rounded-pill">
                                            {{ $meeting->is_online ? 'Online' : Str::limit($meeting->location, 15) }}
                                        </span>
                                        <span class="badge badge-light badge-sm rounded-pill">
                                            {{ $meeting->meetingType->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4 border-0">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <p class="mb-0">Tidak ada meeting mendatang</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller
    const actionTrendData = @json($actionTrendData);
    const meetingTrendData = @json($meetingTrendData);
    const userData = @json($userAssignmentStats);

    // 1. Chart Trend Tindak Lanjut
    const actionTrendCtx = document.getElementById('actionTrendChart').getContext('2d');
    let actionTrendChart = new Chart(actionTrendCtx, {
        type: 'line',
        data: {
            labels: actionTrendData.labels || [],
            datasets: [
                {
                    label: 'Tugas Dibuat',
                    data: actionTrendData.created || [],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tugas Selesai',
                    data: actionTrendData.completed || [],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tugas Terlambat',
                    data: actionTrendData.overdue || [],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Trend Aktivitas Tindak Lanjut (30 Hari)',
                    font: { size: 16 }
                },
                tooltip: { 
                    mode: 'index', 
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} tugas`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Tugas' },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    title: { display: true, text: 'Tanggal' }
                }
            }
        }
    });

    // 2. Chart Trend Meeting
    const meetingTrendCtx = document.getElementById('meetingTrendChart').getContext('2d');
    let meetingTrendChart = new Chart(meetingTrendCtx, {
        type: 'line',
        data: {
            labels: meetingTrendData.labels || [],
            datasets: [
                {
                    label: 'Meeting Dibuat',
                    data: meetingTrendData.created || [],
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Meeting Selesai',
                    data: meetingTrendData.completed || [],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Meeting Terjadwal',
                    data: meetingTrendData.scheduled || [],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Trend Aktivitas Meeting (30 Hari)',
                    font: { size: 16 }
                },
                tooltip: { 
                    mode: 'index', 
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} meeting`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Meeting' },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    title: { display: true, text: 'Tanggal' }
                }
            }
        }
    });

    // 3. Chart Status Tindak Lanjut
    const actionStatusCtx = document.getElementById('actionStatusChart').getContext('2d');
    const actionStatusChart = new Chart(actionStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Selesai', 'Sedang Dikerjakan', 'Belum Dikerjakan'],
            datasets: [{
                label: 'Jumlah Tugas',
                data: [{{ $completedActions }}, {{ $inProgressActions }}, {{ $pendingActions }}],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderColor: ['#218838', '#e0a800', '#c82333'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Distribusi Status Tindak Lanjut',
                    font: { size: 14 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });

    // 4. Chart Status Meeting
    const meetingStatusCtx = document.getElementById('meetingStatusChart').getContext('2d');
    const meetingStatusChart = new Chart(meetingStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Total', 'Terjadwal', 'Berlangsung', 'Selesai'],
            datasets: [{
                label: 'Jumlah Meeting',
                data: [{{ $totalMeetings }}, {{ $scheduledMeetings }}, {{ $ongoingMeetings }}, {{ $completedMeetings }}],
                backgroundColor: ['#17a2b8', '#007bff', '#ffc107', '#28a745'],
                borderColor: ['#138496', '#0069d9', '#e0a800', '#218838'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Distribusi Status Meeting',
                    font: { size: 14 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });

    // 5. Chart Tugas per User
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    const userCtx = document.getElementById('userAssignmentChart').getContext('2d');
    const userAssignmentChart = new Chart(userCtx, {
        type: 'line',
        data: {
            labels: userData.map(user => user.name),
            datasets: [
                {
                    label: 'Total Tugas',
                    data: userData.map(user => user.total_assigned),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tugas Selesai',
                    data: userData.map(user => user.completed_actions),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Sedang Dikerjakan',
                    data: userData.map(user => user.in_progress_actions),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Belum Dikerjakan',
                    data: userData.map(user => user.pending_actions),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Tugas per User',
                    font: { size: 14 }
                },
                tooltip: { 
                    mode: 'index', 
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} tugas`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Tugas' },
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    }
                },
                x: {
                    title: { display: true, text: 'Nama User' }
                }
            }
        }
    });
    @endif

    // Fungsi untuk update chart berdasarkan periode
    function updateChartPeriod(chartType, period) {
        // Tampilkan loading state
        const chartElement = chartType === 'action' ? 
            document.getElementById('actionTrendChart') : 
            document.getElementById('meetingTrendChart');
        
        const canvas = chartElement.parentElement;
        canvas.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">Memuat data...</p></div>';
        
        // Simulasi AJAX request untuk mendapatkan data baru
        setTimeout(() => {
            // Dalam implementasi nyata, ini akan menjadi AJAX call ke server
            console.log(`Mengupdate ${chartType} chart untuk periode ${period} hari`);
            
            // Reload halaman dengan parameter periode (untuk demo)
            // window.location.href = `{{ route('dashboard') }}?${chartType}_period=${period}`;
            
            // Untuk demo, kita reload halaman
            location.reload();
        }, 1000);
    }

    // Fungsi toggle untuk semua section
    function setupToggleButtons() {
        document.querySelectorAll('[data-display-type]').forEach(button => {
            button.addEventListener('click', function() {
                const displayType = this.getAttribute('data-display-type');
                const card = this.closest('.card');
                
                // Update button states
                card.querySelectorAll('[data-display-type]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Toggle visibility
                if (card.querySelector('#action-chart-container')) {
                    document.getElementById('action-chart-container').classList.toggle('d-none', displayType !== 'chart');
                    document.getElementById('action-numbers-container').classList.toggle('d-none', displayType !== 'numbers');
                } else if (card.querySelector('#meeting-chart-container')) {
                    document.getElementById('meeting-chart-container').classList.toggle('d-none', displayType !== 'chart');
                    document.getElementById('meeting-numbers-container').classList.toggle('d-none', displayType !== 'numbers');
                } else if (card.querySelector('#user-chart-container')) {
                    document.getElementById('user-chart-container').classList.toggle('d-none', displayType !== 'chart');
                    document.getElementById('user-table-container').classList.toggle('d-none', displayType !== 'table');
                }
            });
        });

        // Toggle untuk trend charts period
        document.querySelectorAll('[data-chart-period]').forEach(button => {
            button.addEventListener('click', function() {
                const period = this.getAttribute('data-chart-period');
                const chartType = this.getAttribute('data-chart-type');
                
                // Update button states
                this.closest('.btn-group').querySelectorAll('[data-chart-period]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Update chart
                updateChartPeriod(chartType, period);
            });
        });
    }

    setupToggleButtons();
});
</script>

<style>
.small-box {
    transition: transform 0.2s ease-in-out;
}

.small-box:hover {
    transform: translateY(-2px);
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-1px);
}

.rounded-lg {
    border-radius: 12px !important;
}

.badge.rounded-pill {
    border-radius: 50rem !important;
}

.btn.rounded-circle {
    border-radius: 50% !important;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.border-0 {
    border: 0 !important;
}

.thead-light th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.list-group-item {
    border: none;
    padding: 1rem 1.25rem;
}

.list-group-item:not(:last-child) {
    border-bottom: 1px solid #e9ecef !important;
}

.chart-container {
    position: relative;
    width: 100%;
}

.btn-group .btn.active {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.info-box {
    margin-bottom: 0;
    padding: 1rem;
}
</style>
@endpush