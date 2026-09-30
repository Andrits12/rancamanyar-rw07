@extends('layouts.admin')

@section('title', 'Detail Akun: ' . $user->name)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Akun</h5>
        <small class="text-muted">Informasi lengkap akun warga</small>
    </div>
    <div>
        @can('user.edit')
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 32px; font-weight: 700;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->nik }}</p>
                @foreach ($user->roles as $role)
                    <span class="badge bg-info text-dark">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <strong>Informasi Akun</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th width="200">NIK</th>
                        <td>{{ $user->nik }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>No HP</th>
                        <td>{{ $user->no_hp }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>RT</th>
                        <td>{{ $user->rt->nama_rt ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $user->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No KK</th>
                        <td>{{ $user->no_kk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Kependudukan</th>
                        <td>{{ ucfirst($user->status_kependudukan) }}</td>
                    </tr>
                    <tr>
                        <th>Status Keluarga</th>
                        <td>{{ ucfirst($user->status_keluarga) }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : ($user->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td>{{ $user->tgl_lahir?->format('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Agama</th>
                        <td>{{ $user->agama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Pekerjaan</th>
                        <td>{{ $user->pekerjaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Perkawinan</th>
                        <td>{{ $user->status_kawin ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if ($user->jabatan)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <strong>Jabatan</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th width="200">Jabatan</th>
                        <td>{{ $user->jabatan }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>
                            {{ $user->periode_jabatan_mulai?->format('d M Y') ?? '-' }}
                            s/d
                            {{ $user->periode_jabatan_selesai?->format('d M Y') ?? 'Sekarang' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        @if ($user->permissions->count() > 0)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <strong>Hak Akses Tambahan (Override)</strong>
            </div>
            <div class="card-body">
                @foreach ($user->permissions as $perm)
                    <span class="badge bg-secondary mb-1">{{ $perm->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <strong>Informasi Sistem</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th width="200">Status Akun</th>
                        <td>
                            @if ($user->status_akun === 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @elseif ($user->status_akun === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Login Terakhir</th>
                        <td>{{ $user->last_login_at?->format('d M Y H:i') ?? 'Belum pernah' }}</td>
                    </tr>
                    <tr>
                        <th>Terdaftar</th>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection