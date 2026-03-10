<!-- resources/views/action-items/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Tindak Lanjut')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('action-items.index') }}">Tindak Lanjut</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Tindak Lanjut</h3>
    </div>
    <form action="{{ route('meetings.action-items.store', $meeting) }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="title">Judul Tindak Lanjut *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi *</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="assigned_to">Ditugaskan ke *</label>
                        <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                id="assigned_to" name="assigned_to" required>
                            <option value="">Pilih User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department_id">Departemen *</label>
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
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="due_date">Batas Waktu *</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                               id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                        @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="priority">Prioritas *</label>
                        <select class="form-control @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Rendah</option>
                            <option value="2" {{ old('priority', '2') == '2' ? 'selected' : '' }}>Sedang</option>
                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Tinggi</option>
                        </select>
                        @error('priority')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="completion_notes">Catatan Penyelesaian</label>
                <textarea class="form-control @error('completion_notes') is-invalid @enderror" 
                          id="completion_notes" name="completion_notes" rows="3">{{ old('completion_notes') }}</textarea>
                @error('completion_notes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection