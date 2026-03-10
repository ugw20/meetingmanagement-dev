<!-- resources/views/meetings/partials/action-item-modals.blade.php -->

<!-- Add Action Item Modal -->
<div class="modal fade" id="addActionItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title m-0">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Tindak Lanjut
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('meetings.action-items.store', $meeting) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" class="font-weight-bold small">Judul Tindak Lanjut *</label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" 
                                       placeholder="Masukkan judul tindak lanjut" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="font-weight-bold small">Deskripsi *</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3" 
                                  placeholder="Jelaskan detail tindak lanjut yang harus dilakukan" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assigned_to" class="font-weight-bold small">Ditugaskan ke *</label>
                                <select class="form-control form-control-sm select2" id="assigned_to" name="assigned_to" required>
                                    <option value="">Pilih User</option>
                                    @php
                                        // Handle jika $users tidak tersedia
                                        $users = $users ?? \App\Models\User::active()->get();
                                        $departments = $departments ?? \App\Models\Department::active()->get();
                                    @endphp
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} - {{ $user->department->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id" class="font-weight-bold small">Departemen *</label>
                                <select class="form-control form-control-sm" id="department_id" name="department_id" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date" class="font-weight-bold small">Batas Waktu *</label>
                                <input type="date" class="form-control form-control-sm" id="due_date" name="due_date" 
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority" class="font-weight-bold small">Prioritas *</label>
                                <select class="form-control form-control-sm" id="priority" name="priority" required>
                                    <option value="1">🟢 Rendah</option>
                                    <option value="2" selected>🟡 Sedang</option>
                                    <option value="3">🔴 Tinggi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>