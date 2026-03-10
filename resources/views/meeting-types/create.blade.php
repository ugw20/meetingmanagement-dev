<!-- resources/views/meeting-types/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Jenis Meeting')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('meeting-types.index') }}">Jenis Meeting</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Jenis Meeting</h3>
    </div>
    <form action="{{ route('meeting-types.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Jenis Meeting *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Field Wajib</label>
                <div>
                    @php
                        $allFields = [
                            'production_report' => 'Laporan Produksi',
                            'quality_issues' => 'Masalah Kualitas',
                            'maintenance_needs' => 'Kebutuhan Maintenance',
                            'safety_incidents' => 'Insiden Keselamatan',
                            'budget_review' => 'Review Budget',
                            'project_updates' => 'Update Project',
                            'performance_metrics' => 'Metrik Kinerja'
                        ];
                    @endphp
                    @foreach($allFields as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="required_fields[]" 
                               value="{{ $value }}" {{ in_array($value, old('required_fields', [])) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="custom-control-label">Aktif</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('meeting-types.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection