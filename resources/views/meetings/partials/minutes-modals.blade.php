<!-- resources/views/meetings/partials/minutes-modals.blade.php -->

<!-- Add Minutes Modal -->
<div class="modal fade" id="addMinutesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-plus-circle mr-2"></i>Buat Notulensi Meeting
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.minutes.store', $meeting) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="content" class="font-weight-bold small">Isi Notulensi *</label>
                        <textarea class="form-control" id="content" name="content" rows="8" 
                                  placeholder="Tuliskan rangkuman meeting, poin-poin penting, dan hal-hal yang dibahas..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="decisions" class="font-weight-bold small">Keputusan (opsional)</label>
                        <small class="form-text text-muted mb-2">Masukkan setiap keputusan pada baris baru</small>
                        <textarea class="form-control" id="decisions" name="decisions" rows="4" 
                                  placeholder="Keputusan 1&#10;Keputusan 2&#10;Keputusan 3"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_finalized" name="is_finalized">
                        <label class="form-check-label font-weight-bold small" for="is_finalized">
                            Finalisasi notulensi
                        </label>
                        <small class="form-text text-muted">
                            Notulensi yang sudah difinalisasi tidak dapat diubah lagi
                        </small>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Simpan Notulensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Minutes Modal -->
@if($meeting->minutes && !$meeting->minutes->is_finalized)
<div class="modal fade" id="editMinutesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-edit mr-2"></i>Edit Notulensi Meeting
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.minutes.update', [$meeting, $meeting->minutes]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_content" class="font-weight-bold small">Isi Notulensi *</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="8" required>{{ $meeting->minutes->content }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_decisions" class="font-weight-bold small">Keputusan (opsional)</label>
                        <small class="form-text text-muted mb-2">Masukkan setiap keputusan pada baris baru</small>
                        <textarea class="form-control" id="edit_decisions" name="decisions" rows="4">{{ $meeting->minutes->decisions ? implode("\n", $meeting->minutes->decisions) : '' }}</textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_finalized" name="is_finalized" {{ $meeting->minutes->is_finalized ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold small" for="edit_is_finalized">
                            Finalisasi notulensi
                        </label>
                        <small class="form-text text-muted">
                            Notulensi yang sudah difinalisasi tidak dapat diubah lagi
                        </small>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Update Notulensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif