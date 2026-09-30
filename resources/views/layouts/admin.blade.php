<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - RW Digital Rancamanyar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * { font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; margin: 0; }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: #1e293b;
            color: #cbd5e1;
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid #334155;
            text-align: center;
        }
        .sidebar-brand h5 { color: #fff; margin: 0; font-weight: 700; }
        .sidebar-brand small { color: #94a3b8; font-size: 12px; }
        .sidebar-menu { padding: 15px 0; }
        .sidebar-menu .menu-label {
            padding: 10px 20px 5px;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .sidebar-menu a {
            display: block;
            padding: 10px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #334155;
            color: #fff;
            border-left-color: #667eea;
        }
        .sidebar-menu a i { margin-right: 10px; width: 20px; display: inline-block; text-align: center; }

        .main-content { margin-left: 260px; min-height: 100vh; }

        .topbar {
            background: #fff;
            padding: 12px 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topbar .user-info { display: flex; align-items: center; gap: 10px; }
        .topbar .user-avatar {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .content-area { padding: 25px; }

        @media (max-width: 768px) {
            .sidebar { margin-left: -260px; }
            .sidebar.show { margin-left: 0; }
            .main-content { margin-left: 0; }
        }

        /* Scrollbar sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    
    </style>

    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-house-heart-fill" style="font-size: 32px; color: #667eea;"></i>
            <h5 class="mt-2">RW Digital</h5>
            <small>Rancamanyar RW 07</small>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Utama</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            @canany(['warga.view', 'keluarga.view'])
            <div class="menu-label">Master Data</div>
            @endcanany

            @can('warga.view')
            <a href="{{ route('warga.index') }}" class="{{ request()->routeIs('warga.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Warga
            </a>
            @endcan

            @can('keluarga.view')
            <a href="{{ route('keluarga.index') }}" class="{{ request()->routeIs('keluarga.*') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Data Keluarga
                        
            </a>
            @endcan

            @canany(['pengumuman.view', 'berita.view', 'kalender.view'])
            <div class="menu-label">Portal Informasi</div>
            @endcanany

            @can('pengumuman.view')
            <a href="{{ route('pengumuman.index', ['tipe' => 'pengumuman']) }}" class="{{ request()->routeIs('pengumuman.*') && request('tipe') != 'berita' ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i> Pengumuman
            </a>
            @endcan

            @can('berita.view')
            <a href="{{ route('pengumuman.index', ['tipe' => 'berita']) }}" class="{{ request()->routeIs('pengumuman.*') && request('tipe') == 'berita' ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> Berita RW
            </a>
            @endcan

            @can('kalender.view')
            <a href="#"><i class="bi bi-calendar-event"></i> Kalender Kegiatan</a>
            @endcan

            @canany(['surat.view', 'pengaduan.view'])
            <div class="menu-label">Layanan Warga</div>
            @endcanany

            @can('surat.view')
            <a href="{{ route('surat.index') }}" class="{{ request()->routeIs('surat.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper"></i> Surat Menyurat
            </a>
            @endcan
            
            @can('surat.edit')
            <a href="{{ route('jenis-surat.index') }}" class="{{ request()->routeIs('jenis-surat.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Jenis Surat
            </a>
            @endcan

            @can('pengaduan.view')
            <a href="{{ route('pengaduan.index') }}" class="{{ request()->routeIs('pengaduan.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text"></i> Pengaduan
            </a>
            @endcan

            @can('keuangan.view')
            <div class="menu-label">Keuangan</div>
            <a href="#"><i class="bi bi-cash-coin"></i> Iuran & Kas</a>
            @endcan

            @canany(['ronda.view', 'tamu.view'])
            <div class="menu-label">Security</div>
            @endcanany

            @can('ronda.view')
            <a href="{{ route('ronda.index') }}" class="{{ request()->routeIs('ronda.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Jadwal Ronda
            </a>
            @endcan

            @can('panic.view')
            <a href="{{ route('panic.index') }}" class="{{ request()->routeIs('panic.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Panic Button
                @php
                $panicBaru = \App\Models\PanicButton::where('status', 'baru')->count();
                @endphp
                @if ($panicBaru > 0)
                <span class="badge bg-danger float-end">{{ $panicBaru }}</span>
                @endif
            </a>
            @endcan

            @can('tamu.view')
            <a href="#"><i class="bi bi-person-badge"></i> Manajemen Tamu</a>
            @endcan

            @canany(['inventaris.view', 'umkm.view', 'lowongan.view', 'posyandu.view', 'bansos.view', 'peta.view', 'akta.view'])
            <div class="menu-label">Lainnya</div>
            @endcanany

            @can('inventaris.view')
            <a href="{{ route('inventaris.index') }}" class="{{ request()->routeIs('inventaris.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Inventaris
            </a>
            @endcan
            
            @can('inventaris.view')
            <a href="{{ route('peminjaman-aset.index') }}" class="{{ request()->routeIs('peminjaman-aset.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i> Peminjaman Aset
            </a>
            @endcan

            @can('umkm.view')
            <a href="#"><i class="bi bi-shop"></i> UMKM Warga</a>
            @endcan

            @can('lowongan.view')
            <a href="{{ route('lowongan.index') }}" class="{{ request()->routeIs('lowongan.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> Lowongan Kerja
            </a>
            @endcan
            
            @if (auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris']))
            <a href="{{ route('lamaran.index') }}" class="{{ request()->routeIs('lamaran.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-person"></i> Lamaran Masuk
                @php
                    $lamaranBaru = \App\Models\Lamaran::where('status', 'diajukan')
                        ->when(auth()->user()->hasRole('ketua_rt') && !auth()->user()->hasAnyRole(['super_admin','ketua_rw']), fn($q) => $q->where('rt_id', auth()->user()->rt_id))
                        ->count();
                @endphp
                
                @if ($lamaranBaru > 0)
                    <span class="badge bg-danger float-end">{{ $lamaranBaru }}</span>
                @endif
            </a>
            @endif
            
            {{-- Menu untuk Warga --}}
            @if (auth()->user()->hasRole('warga'))
            <a href="{{ route('lamaran.index') }}" class="{{ request()->routeIs('lamaran.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-person"></i> Lamaran Saya
            </a>
            @endif

            @can('posyandu.view')
            <a href="#"><i class="bi bi-heart-pulse"></i> Posyandu</a>
            @endcan

            @can('bansos.view')
            <a href="#"><i class="bi bi-gift"></i> Bantuan Sosial</a>
            @endcan

            @can('peta.view')
            <a href="{{ route('peta.index') }}" class="{{ request()->routeIs('peta.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Peta Digital
            </a>
            @endcan

            @can('akta.view')
            <a data-bs-toggle="collapse" href="#menuAkta" role="button" 
                class="{{ request()->routeIs('kelahiran.*') || request()->routeIs('kematian.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Akta Kelahiran/Kematian
                <i class="bi bi-chevron-down float-end" style="font-size: 12px;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('kelahiran.*') || request()->routeIs('kematian.*') ? 'show' : '' }}" id="menuAkta">
                <a href="{{ route('kelahiran.index') }}" class="ps-4 {{ request()->routeIs('kelahiran.*') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i> Kelahiran
                </a>
                <a href="{{ route('kematian.index') }}" class="ps-4 {{ request()->routeIs('kematian.*') ? 'active' : '' }}">
                    <i class="bi bi-person-dash"></i> Kematian
                </a>
            </div>
            @endcan

            @canany(['user.view', 'role.view'])
            <div class="menu-label">Administrasi</div>
            @endcanany

            <a href="{{ route('password.ganti') }}" class="{{ request()->routeIs('password.ganti') ? 'active' : '' }}">
                <i class="bi bi-key"></i> Ganti Password
            </a>

            @can('user.view')
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Manajemen Akun
            </a>
            @endcan

            @can('role.view')
            <a href="#" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> Role & Permission
            </a>
            @endcan
        
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        <div class="topbar">
            <div>
                <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <strong>@yield('title', 'Dashboard')</strong>
            </div>

            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="d-none d-md-block">
                    <div style="font-size: 14px; font-weight: 600;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 12px; color: #64748b;">
                        {{ auth()->user()->getRoleNames()->map(fn($r) => ucwords(str_replace('_', ' ', $r)))->implode(', ') }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="content-area">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>