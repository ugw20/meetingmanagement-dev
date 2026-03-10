<!-- resources/views/action-items/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Tindak Lanjut')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('action-items.index') }}">Tindak Lanjut</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Tindak Lanjut</h3>
    </div>
    <form action="{{ route('action-items.update', $actionItem) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="title">Judul Tindak Lanjut *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title', $actionItem->title) }}" required>
                @error('title')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi *</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4" required>{{ old('description', $actionItem->description) }}</textarea>
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
                                <option value="{{ $user->id }}" {{ old('assigned_to', $actionItem->assigned_to) == $user->id ? 'selected' : '' }}>
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
                                <option value="{{ $department->id }}" {{ old('department_id', $actionItem->department_id) == $department->id ? 'selected' : '' }}>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="due_date">Batas Waktu *</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                               id="due_date" name="due_date" value="{{ old('due_date', $actionItem->due_date->format('Y-m-d')) }}" required>
                        @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="priority">Prioritas *</label>
                        <select class="form-control @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="1" {{ old('priority', $actionItem->priority) == '1' ? 'selected' : '' }}>Rendah</option>
                            <option value="2" {{ old('priority', $actionItem->priority) == '2' ? 'selected' : '' }}>Sedang</option>
                            <option value="3" {{ old('priority', $actionItem->priority) == '3' ? 'selected' : '' }}>Tinggi</option>
                        </select>
                        @error('priority')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="pending" {{ old('status', $actionItem->status) == 'pending' ? 'selected' : '' }}>Belum Dikerjakan</option>
                            <option value="in_progress" {{ old('status', $actionItem->status) == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="completed" {{ old('status', $actionItem->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ old('status', $actionItem->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="completion_notes">Catatan Penyelesaian</label>
                <textarea class="form-control @error('completion_notes') is-invalid @enderror" 
                          id="completion_notes" name="completion_notes" rows="3">{{ old('completion_notes', $actionItem->completion_notes) }}</textarea>
                @error('completion_notes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('action-items.show', $actionItem) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection