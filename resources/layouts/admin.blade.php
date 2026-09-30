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
            min-height: 100vh;
            background: #1e293b;
            color: #cbd5e1;
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
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
            <a href="#"><i class="bi bi-people"></i> Data Warga</a>
            @endcan

            @can('keluarga.view')
            <a href="#"><i class="bi bi-house-door"></i> Data Keluarga</a>
            @endcan

            @canany(['pengumuman.view', 'berita.view', 'kalender.view'])
            <div class="menu-label">Portal Informasi</div>
            @endcanany

            @can('pengumuman.view')
            <a href="#"><i class="bi bi-megaphone"></i> Pengumuman</a>
            @endcan

            @can('berita.view')
            <a href="#"><i class="bi bi-newspaper"></i> Berita RW</a>
            @endcan

            @can('kalender.view')
            <a href="#"><i class="bi bi-calendar-event"></i> Kalender Kegiatan</a>
            @endcan

            @canany(['surat.view', 'pengaduan.view'])
            <div class="menu-label">Layanan Warga</div>
            @endcanany

            @can('surat.view')
            <a href="#"><i class="bi bi-envelope-paper"></i> Surat Menyurat</a>
            @endcan

            @can('pengaduan.view')
            <a href="#"><i class="bi bi-chat-left-text"></i> Pengaduan</a>
            @endcan

            @can('keuangan.view')
            <div class="menu-label">Keuangan</div>
            <a href="#"><i class="bi bi-cash-coin"></i> Iuran & Kas</a>
            @endcan

            @canany(['ronda.view', 'tamu.view'])
            <div class="menu-label">Security</div>
            @endcanany

            @can('ronda.view')
            <a href="#"><i class="bi bi-shield-check"></i> Jadwal Ronda</a>
            @endcan

            @can('tamu.view')
            <a href="#"><i class="bi bi-person-badge"></i> Manajemen Tamu</a>
            @endcan

            @canany(['inventaris.view', 'umkm.view', 'lowongan.view', 'posyandu.view', 'bansos.view', 'peta.view', 'akta.view'])
            <div class="menu-label">Lainnya</div>
            @endcanany

            @can('inventaris.view')
            <a href="#"><i class="bi bi-box-seam"></i> Inventaris</a>
            @endcan

            @can('umkm.view')
            <a href="#"><i class="bi bi-shop"></i> UMKM Warga</a>
            @endcan

            @can('lowongan.view')
            <a href="#"><i class="bi bi-briefcase"></i> Lowongan Kerja</a>
            @endcan

            @can('posyandu.view')
            <a href="#"><i class="bi bi-heart-pulse"></i> Posyandu</a>
            @endcan

            @can('bansos.view')
            <a href="#"><i class="bi bi-gift"></i> Bantuan Sosial</a>
            @endcan

            @can('peta.view')
            <a href="#"><i class="bi bi-geo-alt"></i> Peta Digital</a>
            @endcan

            @can('akta.view')
            <a href="#"><i class="bi bi-file-earmark-text"></i> Akta Kelahiran/Kematian</a>
            @endcan

            @canany(['user.view', 'role.view'])
            <div class="menu-label">Administrasi</div>
            @endcanany

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