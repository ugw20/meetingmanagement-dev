<!-- resources/views/departments/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Departemen')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departemen</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Departemen</h3>
    </div>
    <form action="{{ route('departments.update', $department) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Departemen *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $department->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="custom-control-label">Aktif</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('departments.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection