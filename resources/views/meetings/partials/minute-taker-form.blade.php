<!-- resources/views/meetings/partials/minute-taker-form.blade.php -->
@if($meeting->assigned_minute_taker_id == auth()->id() || auth()->user()->canManageMeetings() || $meeting->organizer_id == auth()->id())
<div class="card shadow-sm mb-4">
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
                <textarea class="form-control @error('content') is-invalid @enderror" 
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
                <textarea class="form-control @error('decisions') is-invalid @enderror" 
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