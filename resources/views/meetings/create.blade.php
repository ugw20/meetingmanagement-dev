<!-- resources/views/meetings/create.blade.php -->
@extends('layouts.app')

@section('title', 'Buat Meeting Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meeting</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="card-title m-0">
                        <i class="fas fa-plus-circle mr-2"></i>Buat Meeting Baru
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('meetings.store') }}" method="POST" id="meetingForm">
                        @csrf

                        <!-- Informasi Utama Meeting -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>Informasi Meeting
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="font-weight-bold">Judul Meeting *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="Masukkan judul meeting" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meeting_type_id" class="font-weight-bold">Jenis Meeting *</label>
                                    <select class="form-control @error('meeting_type_id') is-invalid @enderror" 
                                            id="meeting_type_id" name="meeting_type_id" required>
                                        <option value="">Pilih Jenis Meeting</option>
                                        @foreach($meetingTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('meeting_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('meeting_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="font-weight-bold">Deskripsi Meeting</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Deskripsi singkat tentang meeting ini">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Waktu dan Lokasi -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-calendar-alt mr-2"></i>Waktu & Lokasi
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time" class="font-weight-bold">Waktu Mulai *</label>
                                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time" class="font-weight-bold">Waktu Selesai *</label>
                                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id" class="font-weight-bold">Departemen *</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" 
                                            id="department_id" name="department_id" required>
                                        <option value="">Pilih Departemen</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tipe Meeting</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="is_online" name="is_online" value="1" {{ old('is_online') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_online">Meeting Online</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Lokasi untuk meeting offline -->
                            <div class="col-md-6" id="locationField">
                                <div class="form-group">
                                    <label for="location" class="font-weight-bold">Lokasi *</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                           id="location" name="location" value="{{ old('location') }}" 
                                           placeholder="Ruangan meeting atau alamat lengkap" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Platform Meeting Online -->
                        <div class="row mb-4" id="onlineMeetingSection" style="display: none;">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title m-0">
                                            <i class="fas fa-video mr-2"></i>Platform Meeting Online
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="meeting_platform" class="font-weight-bold">Platform Meeting *</label>
                                                    <select class="form-control @error('meeting_platform') is-invalid @enderror" 
                                                            id="meeting_platform" name="meeting_platform">
                                                        <option value="">Pilih Platform</option>
                                                        <option value="google_meet" {{ old('meeting_platform') == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                                                        <option value="zoom" {{ old('meeting_platform') == 'zoom' ? 'selected' : '' }}>Zoom</option>
                                                        <option value="microsoft_teams" {{ old('meeting_platform') == 'microsoft_teams' ? 'selected' : '' }}>Microsoft Teams</option>
                                                        <option value="webex" {{ old('meeting_platform') == 'webex' ? 'selected' : '' }}>Cisco Webex</option>
                                                        <option value="skype" {{ old('meeting_platform') == 'skype' ? 'selected' : '' }}>Skype</option>
                                                        <option value="other" {{ old('meeting_platform') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                                    </select>
                                                    @error('meeting_platform')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="meeting_link" class="font-weight-bold">Link Meeting *</label>
                                                    <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                                                           id="meeting_link" name="meeting_link" value="{{ old('meeting_link') }}" 
                                                           placeholder="https://meet.google.com/xxx-yyyy-zzz">
                                                    @error('meeting_link')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="meeting_id" class="font-weight-bold">Meeting ID / Kode</label>
                                                    <input type="text" class="form-control @error('meeting_id') is-invalid @enderror" 
                                                           id="meeting_id" name="meeting_id" value="{{ old('meeting_id') }}" 
                                                           placeholder="Meeting ID atau kode rahasia">
                                                    @error('meeting_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="meeting_password" class="font-weight-bold">Password Meeting</label>
                                                    <input type="text" class="form-control @error('meeting_password') is-invalid @enderror" 
                                                           id="meeting_password" name="meeting_password" value="{{ old('meeting_password') }}" 
                                                           placeholder="Password meeting (jika ada)">
                                                    @error('meeting_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Peserta Meeting -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-users mr-2"></i>Peserta Meeting
                                </h5>
                                
                                <div class="form-group">
                                    <label for="participants" class="font-weight-bold">Pilih Peserta *</label>
                                    <select class="form-control select2 @error('participants') is-invalid @enderror" 
                                            id="participants" name="participants[]" multiple="multiple" required 
                                            data-placeholder="Pilih peserta meeting">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ in_array($user->id, old('participants', [])) ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->department->name ?? '-' }} ({{ $user->position }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pilih peserta yang akan diundang ke meeting ini
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('meetings.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Simpan Meeting
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}
#onlineMeetingSection {
    transition: all 0.3s ease;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Document loaded, initializing meeting form...');

    // Toggle meeting online fields
    const isOnlineCheckbox = document.getElementById('is_online');
    const locationField = document.getElementById('locationField');
    const onlineMeetingSection = document.getElementById('onlineMeetingSection');
    
    function toggleMeetingType() {
        console.log('Toggle meeting type, checked:', isOnlineCheckbox.checked);
        if (isOnlineCheckbox.checked) {
            locationField.style.display = 'none';
            onlineMeetingSection.style.display = 'block';
            document.getElementById('meeting_platform').required = true;
            document.getElementById('meeting_link').required = true;
            document.getElementById('location').required = false;
        } else {
            locationField.style.display = 'block';
            onlineMeetingSection.style.display = 'none';
            document.getElementById('location').required = true;
            document.getElementById('meeting_platform').required = false;
            document.getElementById('meeting_link').required = false;
        }
    }
    
    isOnlineCheckbox.addEventListener('change', toggleMeetingType);
    toggleMeetingType();

    // Platform change handler
    const platformSelect = document.getElementById('meeting_platform');
    const meetingLinkInput = document.getElementById('meeting_link');
    
    if (platformSelect && meetingLinkInput) {
        platformSelect.addEventListener('change', function() {
            const platform = this.value;
            let placeholder = 'Masukkan link meeting';
            
            switch(platform) {
                case 'google_meet': placeholder = 'https://meet.google.com/xxx-xxxx-xxx'; break;
                case 'zoom': placeholder = 'https://zoom.us/j/xxxxxxxxx'; break;
                case 'microsoft_teams': placeholder = 'https://teams.microsoft.com/l/meetup-join/...'; break;
                case 'webex': placeholder = 'https://meeting.webex.com/meet/...'; break;
                case 'skype': placeholder = 'https://join.skype.com/...'; break;
                case 'other': placeholder = 'https://example.com/meeting-link'; break;
            }
            
            meetingLinkInput.placeholder = placeholder;
        });
    }

    // Initialize Select2
    if ($.fn.select2) {
        $('#participants').select2({
            placeholder: 'Pilih peserta meeting',
            allowClear: true,
            width: '100%'
        });
    }

    // Form validation
    const meetingForm = document.getElementById('meetingForm');
    if (meetingForm) {
        meetingForm.addEventListener('submit', function(e) {
            console.log('Form submitted, validating...');
            let isValid = true;
            
            // Validasi meeting online
            if (isOnlineCheckbox.checked) {
                const platform = document.getElementById('meeting_platform');
                const meetingLink = document.getElementById('meeting_link');
                
                if (platform && !platform.value) {
                    isValid = false;
                    platform.classList.add('is-invalid');
                    alert('Silakan pilih platform meeting untuk meeting online.');
                }
                
                if (meetingLink && !meetingLink.value) {
                    isValid = false;
                    meetingLink.classList.add('is-invalid');
                    alert('Silakan masukkan link meeting untuk meeting online.');
                }
            } else {
                const location = document.getElementById('location');
                if (location && !location.value.trim()) {
                    isValid = false;
                    location.classList.add('is-invalid');
                    alert('Silakan masukkan lokasi meeting.');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                console.log('Form validation failed');
            } else {
                console.log('Form validation passed');
            }
        });
    }
});
</script>
@endsection