<!-- resources/views/meetings/partials/file-modals.blade.php -->

<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-upload mr-2"></i>Upload File Meeting
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.upload-file', $meeting) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file" class="font-weight-bold small">File *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" required>
                            <label class="custom-file-label" for="file">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Maksimal 10MB. Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="description" class="font-weight-bold small">Deskripsi File</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="2" 
                                  placeholder="Deskripsi singkat tentang file ini"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-info btn-sm">
                        <i class="fas fa-upload mr-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>