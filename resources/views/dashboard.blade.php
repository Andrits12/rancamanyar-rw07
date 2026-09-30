@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Welcome --}}
<div class="alert alert-primary border-0 shadow-sm mb-3">
    <h5 class="mb-1">🎉 Selamat Datang, {{ auth()->user()->name }}!</h5>
    <small>
        Login sebagai <strong>{{ auth()->user()->getRoleNames()->map(fn($r) => ucwords(str_replace('_', ' ', $r)))->implode(', ') }}</strong>
        @if (auth()->user()->rt)
            • {{ auth()->user()->rt->nama_rt }}
        @endif
    </small>
</div>

{{-- STATISTIK UTAMA --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Keluarga</div>
                        <h3 class="mb-0">{{ $statistik['total_keluarga'] }}</h3>
                    </div>
                    <i class="bi bi-house-door" style="font-size: 40px; color: #667eea; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Warga</div>
                        <h3 class="mb-0">{{ $statistik['total_warga'] }}</h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 40px; color: #10b981; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Laki-laki</div>
                        <h3 class="mb-0">{{ $statistik['total_laki'] }}</h3>
                    </div>
                    <i class="bi bi-gender-male" style="font-size: 40px; color: #3b82f6; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Perempuan</div>
                        <h3 class="mb-0">{{ $statistik['total_perempuan'] }}</h3>
                    </div>
                    <i class="bi bi-gender-female" style="font-size: 40px; color: #ec4899; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ROW 2: Kategori Umur + Warga per RT --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-bar-chart"></i> Warga per Kategori Umur</strong>
            </div>
            <div class="card-body">
                @php
                    $maxUmur = max($kategoriUmur) ?: 1;
                @endphp
                @foreach ($kategoriUmur as $label => $jumlah)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span>{{ $label }}</span>
                            <strong>{{ $jumlah }} orang</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary"
                                 style="width: {{ ($jumlah / $maxUmur) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-house"></i> Warga per RT</strong>
            </div>
            <div class="card-body" style="max-height: 320px; overflow-y: auto;">
                @php
                    $maxRt = $wargaPerRt->max('jumlah') ?: 1;
                @endphp
                @foreach ($wargaPerRt as $rt)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span>{{ $rt['nama'] }}</span>
                            <strong>{{ $rt['jumlah'] }} orang</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                 style="width: {{ ($rt['jumlah'] / $maxRt) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ROW 3: Warga Terbaru + Info Akun --}}
<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-person-plus"></i> Warga Terbaru</strong>
                <a href="{{ route('warga.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>RT</th>
                            <th>Umur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($wargaTerbaru as $w)
                        <tr>
                            <td>
                                <a href="{{ route('warga.show', $w) }}" class="text-decoration-none">
                                    <strong>{{ $w->nama }}</strong>
                                </a>
                            </td>
                            <td><small><code>{{ $w->nik }}</code></small></td>
                            <td>{{ $w->rt->nama_rt ?? '-' }}</td>
                            <td>{{ $w->umur }} th</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Belum ada data warga.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-info-circle"></i> Info Sistem</strong>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total RT</span>
                    <strong>{{ $wargaPerRt->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Akun Aktif</span>
                    <strong>{{ $totalUser }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Jumlah Role</span>
                    <strong>{{ \Spatie\Permission\Models\Role::count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Jumlah Permission</span>
                    <strong>{{ \Spatie\Permission\Models\Permission::count() }}</strong>
                </div>
                <hr>
                <div class="text-center text-muted small">
                    <i class="bi bi-house-heart-fill"></i><br>
                    RW 07 Perumahan Rancamanyar<br>
                    Desa Wancimekar
                </div>
            </div>
        </div>
    </div>
</div>

@endsection