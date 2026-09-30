@extends('layouts.admin')

@section('title', 'Tambah Keluarga')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Tambah Keluarga (KK)</h5>
        <small class="text-muted">Buat data kartu keluarga baru</small>
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

<form method="POST" action="{{ route('keluarga.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No KK <span class="text-danger">*</span></label>
                    <input type="text" name="no_kk" class="form-control @error('no_kk') is-invalid @enderror"
                           value="{{ old('no_kk') }}" maxlength="16" pattern="[0-9]{16}" required>
                    <small class="text-muted">16 digit angka</small>
                    @error('no_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT <span class="text-danger">*</span></label>
                    <select name="rt_id" class="form-select @error('rt_id') is-invalid @enderror" required>
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                    @error('rt_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Kepala Keluarga <span class="text-danger">*</span></label>
                    <input type="text" name="kepala_keluarga_nama" class="form-control @error('kepala_keluarga_nama') is-invalid @enderror"
                           value="{{ old('kepala_keluarga_nama') }}" required>
                    <small class="text-muted">Nanti otomatis terupdate saat tambah anggota</small>
                    @error('kepala_keluarga_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Rumah</label>
                    <select name="status_rumah" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="milik_sendiri" {{ old('status_rumah') == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="sewa" {{ old('status_rumah') == 'sewa' ? 'selected' : '' }}>Sewa</option>
                        <option value="kontrak" {{ old('status_rumah') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="menumpang" {{ old('status_rumah') == 'menumpang' ? 'selected' : '' }}>Menumpang</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                              rows="2" required>{{ old('alamat') }}</textarea>
                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                    <select name="status_keluarga" class="form-select" required>
                        <option value="aktif" {{ old('status_keluarga') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="pindah" {{ old('status_keluarga') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                        <option value="nonaktif" {{ old('status_keluarga') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Daftar</label>
                    <input type="date" name="tgl_daftar" class="form-control"
                           value="{{ old('tgl_daftar', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keluarga.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Keluarga
            </button>
        </div>
    </div>
</form>

@endsection