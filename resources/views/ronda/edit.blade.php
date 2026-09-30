@extends('layouts.admin')

@section('title', 'Edit Jadwal Ronda')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Jadwal Ronda</h5>
    <a href="{{ route('ronda.show', $ronda) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('ronda.update', $ronda) }}">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">RT</label>
                    <input type="text" class="form-control" value="{{ $ronda->rt->nama_rt }}" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $ronda->tanggal->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Shift</label>
                    <select name="shift" class="form-select" required>
                        @foreach (['malam_1' => 'Malam 1', 'malam_2' => 'Malam 2', 'subuh' => 'Subuh'] as $v => $l)
                            <option value="{{ $v }}" {{ old('shift', $ronda->shift) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', $ronda->jam_mulai) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', $ronda->jam_selesai) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pos Ronda</label>
                    <input type="text" name="pos_ronda" class="form-control" value="{{ old('pos_ronda', $ronda->pos_ronda) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach (['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $ronda->status) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $ronda->catatan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('ronda.show', $ronda) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection