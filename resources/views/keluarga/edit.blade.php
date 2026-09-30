@extends('layouts.admin')

@section('title', 'Edit Keluarga: ' . $keluarga->kepala_keluarga_nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit Keluarga</h5>
        <small class="text-muted">{{ $keluarga->no_kk }}</small>
    </div>
    <a href="{{ route('keluarga.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Ada kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('keluarga.update', $keluarga) }}">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No KK <span class="text-danger">*</span></label>
                    <input type="text" name="no_kk" class="form-control @error('no_kk') is-invalid @enderror"
                           value="{{ old('no_kk', $keluarga->no_kk) }}" maxlength="16" pattern="[0-9]{16}" required>
                    @error('no_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT <span class="text-danger">*</span></label>
                    <select name="rt_id" class="form-select" required>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', $keluarga->rt_id) == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Kepala Keluarga <span class="text-danger">*</span></label>
                    <input type="text" name="kepala_keluarga_nama" class="form-control"
                           value="{{ old('kepala_keluarga_nama', $keluarga->kepala_keluarga_nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Rumah</label>
                    <select name="status_rumah" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach (['milik_sendiri' => 'Milik Sendiri', 'sewa' => 'Sewa', 'kontrak' => 'Kontrak', 'menumpang' => 'Menumpang'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status_rumah', $keluarga->status_rumah) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $keluarga->alamat) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                    <select name="status_keluarga" class="form-select" required>
                        @foreach (['aktif' => 'Aktif', 'pindah' => 'Pindah', 'nonaktif' => 'Nonaktif'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status_keluarga', $keluarga->status_keluarga) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Daftar</label>
                    <input type="date" name="tgl_daftar" class="form-control"
                           value="{{ old('tgl_daftar', $keluarga->tgl_daftar?->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $keluarga->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keluarga.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update Keluarga
            </button>
        </div>
    </div>
</form>

@endsection