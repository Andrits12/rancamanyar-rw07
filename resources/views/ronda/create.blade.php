@extends('layouts.admin')

@section('title', 'Buat Jadwal Ronda')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Buat Jadwal Ronda</h5>
        <small class="text-muted">Pilih RT, tanggal, shift, dan anggota</small>
    </div>
    <a href="{{ route('ronda.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Ada kesalahan:</strong>
    <ul class="mb-0 mt-2">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('ronda.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">RT <span class="text-danger">*</span></label>
                    <select name="rt_id" id="rt_id" class="form-select" required onchange="loadUsers()">
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', auth()->user()->rt_id) == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                    <select name="shift" class="form-select" required>
                        <option value="malam_1" {{ old('shift') == 'malam_1' ? 'selected' : '' }}>Malam 1 (22:00-00:00)</option>
                        <option value="malam_2" {{ old('shift') == 'malam_2' ? 'selected' : '' }}>Malam 2 (00:00-02:00)</option>
                        <option value="subuh" {{ old('shift') == 'subuh' ? 'selected' : '' }}>Subuh (02:00-04:00)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '22:00') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', '00:00') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pos Ronda</label>
                    <input type="text" name="pos_ronda" class="form-control" value="{{ old('pos_ronda') }}"
                           placeholder="Contoh: Pos Depan Blok C">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Koordinator Ronda</label>
                    <input type="text" name="koordinator_nama" class="form-control"
                    value="{{ old('koordinator_nama') }}"
                    placeholder="Contoh: Budi Santoso / Pak RT 03">
                </div>
                <div class="col-12">
                    <label class="form-label">Anggota Ronda <span class="text-danger">*</span></label>
                    <small class="text-muted d-block mb-2">
                    </small>
                <div class="input-group mb-2">
                    <input type="text" id="input-anggota" class="form-control"
                    placeholder="Ketik nama anggota ronda...">
                    <button type="button" class="btn btn-primary" onclick="tambahAnggota()">
                        <i class="bi bi-plus-circle"></i> Tambah Anggota
                    </button>
                </div>
                <div id="anggota-container" class="border rounded p-3" style="min-height: 80px;">
                    <p class="text-muted mb-0" id="anggota-empty">Belum ada anggota.</p>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('ronda.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Jadwal
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
let anggotaCount = 0;

function tambahAnggota() {
    const input = document.getElementById('input-anggota');
    const nama = input.value.trim();
    
    if (!nama) {
        alert('Ketik nama anggota dulu.');
        return;
    }

    const container = document.getElementById('anggota-container');
    const emptyMsg = document.getElementById('anggota-empty');
    if (emptyMsg) emptyMsg.remove();

    const row = document.createElement('div');
    row.className = 'd-flex align-items-center mb-2 anggota-row';
    row.innerHTML = `
        <i class="bi bi-person-circle text-primary me-2"></i>
        <input type="text" name="anggota_nama[]" value="${nama.replace(/"/g, '&quot;')}"
               class="form-control form-control-sm me-2" readonly>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusAnggota(this)">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(row);

    input.value = '';
    input.focus();
    anggotaCount++;
}

function hapusAnggota(btn) {
    btn.closest('.anggota-row').remove();
    anggotaCount--;

    if (anggotaCount === 0) {
        const container = document.getElementById('anggota-container');
        container.innerHTML = '<p class="text-muted mb-0" id="anggota-empty">Belum ada anggota.</p>';
    }
}

// Enter untuk tambah anggota
document.getElementById('input-anggota')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        tambahAnggota();
    }
});

</script>
@endpush

@endsection