@extends('layouts.admin')

@section('title', 'Edit Peminjaman')

@section('content')
<div class="alert alert-info">
    Fitur edit peminjaman minimal. Silakan update keperluan & tanggal.
</div>
<form method="POST" action="{{ route('peminjaman-aset.update', $peminjaman) }}">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="mb-2">
                <label class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" value="{{ $peminjaman->jumlah }}" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Keperluan</label>
                <textarea name="keperluan" class="form-control" rows="2" required>{{ $peminjaman->keperluan }}</textarea>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="form-control" value="{{ $peminjaman->tanggal_pinjam?->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rencana Kembali</label>
                    <input type="date" name="tanggal_rencana_kembali" class="form-control" value="{{ $peminjaman->tanggal_rencana_kembali?->format('Y-m-d') }}" required>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('peminjaman-aset.show', $peminjaman) }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </div>
</form>
@endsection