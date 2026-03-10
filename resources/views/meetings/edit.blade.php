<!-- resources/views/meetings/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Meeting')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meeting</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Meeting</h3>
    </div>
    <form action="{{ route('meetings.update', $meeting) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Judul Meeting *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $meeting->title) }}" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="meeting_type_id">Jenis Meeting *</label>
                        <select class="form-control @error('meeting_type_id') is-invalid @enderror" 
                                id="meeting_type_id" name="meeting_type_id" required>
                            <option value="">Pilih Jenis Meeting</option>
                            @foreach($meetingTypes as $type)
                                <option value="{{ $type->id }}" {{ old('meeting_type_id', $meeting->meeting_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('meeting_type_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Meeting</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $meeting->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department_id">Departemen *</label>
                        <select class="form-control @error('department_id') is-invalid @enderror" 
                                id="department_id" name="department_id" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $meeting->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location">Lokasi *</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location', $meeting->location) }}" required>
                        @error('location')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_time">Waktu Mulai *</label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                               id="start_time" name="start_time" 
                               value="{{ old('start_time', $meeting->start_time->format('Y-m-d\TH:i')) }}" required>
                        @error('start_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_time">Waktu Selesai *</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                               id="end_time" name="end_time" 
                               value="{{ old('end_time', $meeting->end_time->format('Y-m-d\TH:i')) }}" required>
                        @error('end_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="is_online" name="is_online" value="1" 
                           {{ old('is_online', $meeting->is_online) ? 'checked' : '' }}>
                    <label for="is_online" class="custom-control-label">Meeting Online</label>
                </div>
            </div>

            <div class="form-group" id="meeting_link_group" style="{{ $meeting->is_online ? '' : 'display: none;' }}">
                <label for="meeting_link">Link Meeting Online</label>
                <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                       id="meeting_link" name="meeting_link" 
                       value="{{ old('meeting_link', $meeting->meeting_link) }}" placeholder="https://">
                @error('meeting_link')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="participants">Peserta Meeting *</label>
                <select class="form-control select2 @error('participants') is-invalid @enderror" 
                        id="participants" name="participants[]" multiple="multiple" required style="width: 100%;">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, old('participants', $currentParticipants)) ? 'selected' : '' }}>
                            {{ $user->name }} - {{ $user->department->name }} ({{ $user->position }})
                        </option>
                    @endforeach
                </select>
                @error('participants')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        </div> <!-- Tutup card-body -->

        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Meeting
                    </button>
                    <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
                
                @if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Hapus Meeting
                    </button>
                @endif
            </div>
        </div>
    </form> <!-- Tutup form update -->
</div> <!-- Tutup card -->

<!-- Modal Konfirmasi Hapus -->
@if(auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus meeting ini?</p>
                <p><strong>{{ $meeting->title }}</strong></p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Meeting</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
    </form>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#participants').select2({
        placeholder: 'Pilih peserta meeting',
        allowClear: true
    });

    // Toggle meeting link field
    $('#is_online').change(function() {
        if ($(this).is(':checked')) {
            $('#meeting_link_group').show();
        } else {
            $('#meeting_link_group').hide();
        }
    });
});
</script>
@endsection