<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\SuratPdfController;
use App\Http\Controllers\JadwalRondaController;
use App\Http\Controllers\PanicButtonController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\PeminjamanAsetController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\KelahiranController;
use App\Http\Controllers\KematianController;

// ============ GUEST ROUTES ============
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ============ AUTH ROUTES ============
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        // ... route yang sudah ada ...
    
        // Wajib ganti password (login pertama)
        Route::get('/password/wajib', [PasswordController::class, 'showWajibForm'])->name('password.wajib');
        Route::post('/password/wajib', [PasswordController::class, 'updateWajib'])->name('password.wajib.update');
    
        // Ganti password dari menu profil (butuh password lama)
        Route::get('/password/ganti', [PasswordController::class, 'showGantiForm'])->name('password.ganti');
        Route::post('/password/ganti', [PasswordController::class, 'updateGanti'])->name('password.ganti.update');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/file/preview/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            abort(404);
        }
    
        $mime = mime_content_type($fullPath);
    
        // Kalau PDF → embed via iframe
        if ($mime === 'application/pdf') {
            $pdf = base64_encode(file_get_contents($fullPath));
            return view('preview-pdf', compact('pdf', 'path'));
        }
    
        // Format lain → kirim langsung
        return response(file_get_contents($fullPath), 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"');
    })->where('path', '.*')->name('file.preview');

    // ============ USER MANAGEMENT ============
    // ⚠️ PENTING: urutan route harus: index → create → show → edit → update → destroy
    Route::prefix('users')->name('users.')->group(function () {

        // 1. Index (list)
        Route::middleware('permission:user.view')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
        });

        // 2. Create (HARUS sebelum {user})
        Route::middleware('permission:user.create')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        // 3. Show, Edit, Update, Reset Password
        Route::middleware('permission:user.view')->group(function () {
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
        });

        Route::middleware('permission:user.edit')->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });

        // 4. Delete
        Route::middleware('permission:user.delete')->group(function () {
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KELUARGA ============
    Route::prefix('keluarga')->name('keluarga.')->group(function () {
        Route::middleware('permission:keluarga.view')->group(function () {
            Route::get('/', [KeluargaController::class, 'index'])->name('index');
            Route::get('/{keluarga}', [KeluargaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:keluarga.create')->group(function () {
            Route::get('/create', [KeluargaController::class, 'create'])->name('create');
            Route::post('/', [KeluargaController::class, 'store'])->name('store');
        });
        Route::middleware('permission:keluarga.edit')->group(function () {
            Route::get('/{keluarga}/edit', [KeluargaController::class, 'edit'])->name('edit');
            Route::put('/{keluarga}', [KeluargaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:keluarga.delete')->group(function () {
            Route::delete('/{keluarga}', [KeluargaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ WARGA ============
    Route::prefix('warga')->name('warga.')->group(function () {
        Route::middleware('permission:warga.view')->group(function () {
            Route::get('/', [WargaController::class, 'index'])->name('index');
            Route::get('/{warga}', [WargaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:warga.create')->group(function () {
            Route::get('/create', [WargaController::class, 'create'])->name('create');
            Route::post('/', [WargaController::class, 'store'])->name('store');
        });
        Route::middleware('permission:warga.edit')->group(function () {
            Route::get('/{warga}/edit', [WargaController::class, 'edit'])->name('edit');
            Route::put('/{warga}', [WargaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:warga.delete')->group(function () {
            Route::delete('/{warga}', [WargaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ ROLE MANAGEMENT ============
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::middleware('permission:role.view')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        });

        Route::middleware('permission:role.create')->group(function () {
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
        });

        Route::middleware('permission:role.edit')->group(function () {
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        });

        Route::middleware('permission:role.delete')->group(function () {
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PENGUMUMAN & BERITA ============
    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::middleware('permission:pengumuman.view')->group(function () {
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
        });
        Route::middleware('permission:pengumuman.create')->group(function () {
            Route::get('/create', [PengumumanController::class, 'create'])->name('create');
            Route::post('/', [PengumumanController::class, 'store'])->name('store');
        });
        Route::middleware('permission:pengumuman.view')->group(function () {
            Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
        });
        Route::middleware('permission:pengumuman.edit')->group(function () {
            Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
            Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
        });
        Route::middleware('permission:pengumuman.delete')->group(function () {
            Route::delete('/{pengumuman}', [PengumumanController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PENGADUAN ============
    Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
        Route::middleware('permission:pengaduan.view')->group(function () {
            Route::get('/', [PengaduanController::class, 'index'])->name('index');
        });
        Route::middleware('permission:pengaduan.create')->group(function () {
            Route::get('/create', [PengaduanController::class, 'create'])->name('create');
            Route::post('/', [PengaduanController::class, 'store'])->name('store');
        });
        Route::middleware('permission:pengaduan.view')->group(function () {
            Route::get('/{pengaduan}', [PengaduanController::class, 'show'])->name('show');
        });
        Route::middleware('permission:pengaduan.edit')->group(function () {
            Route::get('/{pengaduan}/edit', [PengaduanController::class, 'edit'])->name('edit');
            Route::put('/{pengaduan}', [PengaduanController::class, 'update'])->name('update');
        });
        Route::middleware('permission:pengaduan.handle')->group(function () {
            Route::post('/{pengaduan}/tindak-lanjut', [PengaduanController::class, 'tindakLanjut'])->name('tindak-lanjut');
            Route::post('/{pengaduan}/eskalasi', [PengaduanController::class, 'eskalasi'])->name('eskalasi');
        });
        Route::middleware('permission:pengaduan.delete')->group(function () {
            Route::delete('/{pengaduan}', [PengaduanController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ SURAT MENYURAT ============
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/', [SuratController::class, 'index'])->name('index');
        });
        Route::middleware('permission:surat.create')->group(function () {
            Route::get('/create', [SuratController::class, 'create'])->name('create');
            Route::post('/', [SuratController::class, 'store'])->name('store');
        });
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/{surat}', [SuratController::class, 'show'])->name('show');
        });
        Route::middleware('permission:surat.edit')->group(function () {
            Route::get('/{surat}/edit', [SuratController::class, 'edit'])->name('edit');
            Route::put('/{surat}', [SuratController::class, 'update'])->name('update');
        });
        Route::middleware('permission:surat.approve')->group(function () {
            Route::post('/{surat}/approve-rt', [SuratController::class, 'approveRt'])->name('approve-rt');
            Route::post('/{surat}/tolak-rt', [SuratController::class, 'tolakRt'])->name('tolak-rt');
            Route::post('/{surat}/approve-rw', [SuratController::class, 'approveRw'])->name('approve-rw');
            Route::post('/{surat}/tolak-rw', [SuratController::class, 'tolakRw'])->name('tolak-rw');
        });
        Route::middleware('permission:surat.delete')->group(function () {
            Route::delete('/{surat}', [SuratController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ JENIS SURAT ============
    Route::prefix('jenis-surat')->name('jenis-surat.')->group(function () {
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/', [JenisSuratController::class, 'index'])->name('index');
            Route::get('/{jenisSurat}', [JenisSuratController::class, 'show'])->name('show');
        });
        Route::middleware('permission:surat.create')->group(function () {
            Route::get('/create', [JenisSuratController::class, 'create'])->name('create');
            Route::post('/', [JenisSuratController::class, 'store'])->name('store');
        });
        Route::middleware('permission:surat.edit')->group(function () {
            Route::get('/{jenisSurat}/edit', [JenisSuratController::class, 'edit'])->name('edit');
            Route::put('/{jenisSurat}', [JenisSuratController::class, 'update'])->name('update');
        });
        Route::middleware('permission:surat.delete')->group(function () {
            Route::delete('/{jenisSurat}', [JenisSuratController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ SURAT PDF ============
    Route::get('/surat/{surat}/pdf', [SuratPdfController::class, 'preview'])->name('surat.pdf.preview');
    Route::get('/surat/{surat}/view', [SuratPdfController::class, 'viewInline'])->name('surat.pdf.view');
    Route::get('/surat/{surat}/download', [SuratPdfController::class, 'download'])->name('surat.pdf.download');


    // Root redirect
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // ============ JADWAL RONDA ============
    Route::prefix('ronda')->name('ronda.')->group(function () {
        Route::middleware('permission:ronda.view')->group(function () {
            Route::get('/', [JadwalRondaController::class, 'index'])->name('index');
        });
        Route::middleware('permission:ronda.schedule')->group(function () {
            Route::get('/create', [JadwalRondaController::class, 'create'])->name('create');
            Route::post('/', [JadwalRondaController::class, 'store'])->name('store');
            Route::post('/users-by-rt', [JadwalRondaController::class, 'getUsersByRt'])->name('users-by-rt');
        });
        Route::middleware('permission:ronda.view')->group(function () {
            Route::get('/{ronda}', [JadwalRondaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:ronda.edit')->group(function () {
            Route::get('/{ronda}/edit', [JadwalRondaController::class, 'edit'])->name('edit');
            Route::put('/{ronda}', [JadwalRondaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:ronda.absen')->group(function () {
            Route::post('/{ronda}/absen', [JadwalRondaController::class, 'absen'])->name('absen');
        });
        Route::middleware('permission:ronda.lapor')->group(function () {
            Route::post('/{ronda}/lapor', [JadwalRondaController::class, 'lapor'])->name('lapor');
        });
        Route::middleware('permission:ronda.delete')->group(function () {
            Route::delete('/{ronda}', [JadwalRondaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PANIC BUTTON ============
    Route::prefix('panic')->name('panic.')->group(function () {
        Route::middleware('permission:panic.view')->group(function () {
            Route::get('/', [PanicButtonController::class, 'index'])->name('index');
        });
        Route::middleware('permission:panic.create')->group(function () {
            Route::get('/create', [PanicButtonController::class, 'create'])->name('create');
            Route::post('/', [PanicButtonController::class, 'store'])->name('store');
        });
        Route::middleware('permission:panic.view')->group(function () {
            Route::get('/{panic}', [PanicButtonController::class, 'show'])->name('show');
        });
        Route::middleware('permission:panic.handle')->group(function () {
            Route::get('/{panic}/edit', [PanicButtonController::class, 'edit'])->name('edit');
            Route::put('/{panic}', [PanicButtonController::class, 'update'])->name('update');
        });
        Route::middleware('permission:panic.handle')->group(function () {
            Route::delete('/{panic}', [PanicButtonController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/', [AsetController::class, 'index'])->name('index');
        });
        Route::middleware('permission:inventaris.create')->group(function () {
            Route::get('/create', [AsetController::class, 'create'])->name('create');
            Route::post('/', [AsetController::class, 'store'])->name('store');
        });
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/{inventaris}', [AsetController::class, 'show'])->name('show');
        });
        Route::middleware('permission:inventaris.edit')->group(function () {
            Route::get('/{inventaris}/edit', [AsetController::class, 'edit'])->name('edit');
            Route::put('/{inventaris}', [AsetController::class, 'update'])->name('update');
        });
        Route::middleware('permission:inventaris.delete')->group(function () {
            Route::delete('/{inventaris}', [AsetController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PEMINJAMAN ASET ============
    Route::prefix('peminjaman-aset')->name('peminjaman-aset.')->group(function () {
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/', [PeminjamanAsetController::class, 'index'])->name('index');
        });
        Route::middleware('permission:inventaris.pinjam')->group(function () {
            Route::get('/create', [PeminjamanAsetController::class, 'create'])->name('create');
            Route::post('/', [PeminjamanAsetController::class, 'store'])->name('store');
        });
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/{peminjamanAset}', [PeminjamanAsetController::class, 'show'])->name('show');
        });
        Route::middleware('permission:inventaris.pinjam')->group(function () {
            Route::get('/{peminjamanAset}/edit', [PeminjamanAsetController::class, 'edit'])->name('edit');
            Route::put('/{peminjamanAset}', [PeminjamanAsetController::class, 'update'])->name('update');
        });
        Route::middleware('permission:inventaris.approve')->group(function () {
            Route::post('/{peminjamanAset}/approve', [PeminjamanAsetController::class, 'approve'])->name('approve');
            Route::post('/{peminjamanAset}/tolak', [PeminjamanAsetController::class, 'tolak'])->name('tolak');
            Route::post('/{peminjamanAset}/tandai-dipinjam', [PeminjamanAsetController::class, 'tandaiDipinjam'])->name('tandai-dipinjam');
        });
        Route::middleware('permission:inventaris.kembalikan')->group(function () {
            Route::post('/{peminjamanAset}/kembalikan', [PeminjamanAsetController::class, 'kembalikan'])->name('kembalikan');
        });
        Route::middleware('permission:inventaris.delete')->group(function () {
            Route::delete('/{peminjamanAset}', [PeminjamanAsetController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ LOWONGAN KERJA ============
    Route::prefix('lowongan')->name('lowongan.')->group(function () {
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/', [LowonganController::class, 'index'])->name('index');
        });
        Route::middleware('permission:lowongan.create')->group(function () {
            Route::get('/create', [LowonganController::class, 'create'])->name('create');
            Route::post('/', [LowonganController::class, 'store'])->name('store');
        });
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/{lowongan}', [LowonganController::class, 'show'])->name('show');
        });
        Route::middleware('permission:lowongan.edit')->group(function () {
            Route::get('/{lowongan}/edit', [LowonganController::class, 'edit'])->name('edit');
            Route::put('/{lowongan}', [LowonganController::class, 'update'])->name('update');
        });
        Route::middleware('permission:lowongan.delete')->group(function () {
            Route::delete('/{lowongan}', [LowonganController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ LAMARAN ============
    Route::prefix('lamaran')->name('lamaran.')->group(function () {
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/', [LamaranController::class, 'index'])->name('index');
        });
        Route::middleware('permission:lowongan.lamar')->group(function () {
            Route::get('/create', [LamaranController::class, 'create'])->name('create');
            Route::post('/', [LamaranController::class, 'store'])->name('store');
        });
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/{lamaran}', [LamaranController::class, 'show'])->name('show');
        });
        Route::middleware('permission:lowongan.lamar')->group(function () {
            Route::get('/{lamaran}/edit', [LamaranController::class, 'edit'])->name('edit');
            Route::put('/{lamaran}', [LamaranController::class, 'update'])->name('update');
        });
        Route::middleware('permission:lowongan.delete')->group(function () {
            Route::delete('/{lamaran}', [LamaranController::class, 'destroy'])->name('destroy');
        });
        Route::middleware('permission:lowongan.verifikasi')->group(function () {
            Route::post('/{lamaran}/verifikasi', [LamaranController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/{lamaran}/status', [LamaranController::class, 'updateStatus'])->name('update-status');
        });
        Route::middleware('permission:lowongan.teruskan')->group(function () {
            Route::post('/{lamaran}/teruskan', [LamaranController::class, 'teruskan'])->name('teruskan');
        });
    });

    // ============ PETA DIGITAL ============
    Route::prefix('peta')->name('peta.')->group(function () {
        // 1. INDEX & API
        Route::middleware('permission:peta.view')->group(function () {
            Route::get('/', [PetaController::class, 'index'])->name('index');
            Route::get('/api/lokasi', [PetaController::class, 'apiLokasi'])->name('api-lokasi');
        });

        // 2. CREATE
        Route::middleware('permission:peta.create')->group(function () {
            Route::get('/create', [PetaController::class, 'create'])->name('create');
            Route::post('/', [PetaController::class, 'store'])->name('store');
        });

        // 3. SHOW
        Route::middleware('permission:peta.view')->group(function () {
            Route::get('/{lokasi}', [PetaController::class, 'show'])->name('show');
        });

        // 4. EDIT & UPDATE
        Route::middleware('permission:peta.edit')->group(function () {
            Route::get('/{lokasi}/edit', [PetaController::class, 'edit'])->name('edit');
            Route::put('/{lokasi}', [PetaController::class, 'update'])->name('update');
        });

        // 5. DELETE
        Route::middleware('permission:peta.delete')->group(function () {
            Route::delete('/{lokasi}', [PetaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KELAHIRAN ============
    Route::prefix('kelahiran')->name('kelahiran.')->group(function () {
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/', [KelahiranController::class, 'index'])->name('index');
        });
        Route::middleware('permission:akta.create')->group(function () {
            Route::get('/create', [KelahiranController::class, 'create'])->name('create');
            Route::post('/', [KelahiranController::class, 'store'])->name('store');
        });
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/{kelahiran}', [KelahiranController::class, 'show'])->name('show');
        });
        Route::middleware('permission:akta.edit')->group(function () {
            Route::get('/{kelahiran}/edit', [KelahiranController::class, 'edit'])->name('edit');
            Route::put('/{kelahiran}', [KelahiranController::class, 'update'])->name('update');
        });
        Route::middleware('permission:akta.delete')->group(function () {
            Route::delete('/{kelahiran}', [KelahiranController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KEMATIAN ============
    Route::prefix('kematian')->name('kematian.')->group(function () {
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/', [KematianController::class, 'index'])->name('index');
        });
        Route::middleware('permission:akta.create')->group(function () {
            Route::get('/create', [KematianController::class, 'create'])->name('create');
            Route::post('/', [KematianController::class, 'store'])->name('store');
        });
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/{kematian}', [KematianController::class, 'show'])->name('show');
        });
        Route::middleware('permission:akta.edit')->group(function () {
            Route::get('/{kematian}/edit', [KematianController::class, 'edit'])->name('edit');
            Route::put('/{kematian}', [KematianController::class, 'update'])->name('update');
        });
        Route::middleware('permission:akta.delete')->group(function () {
            Route::delete('/{kematian}', [KematianController::class, 'destroy'])->name('destroy');
        });
    });

// ============ VERIFIKASI QR (PUBLIK) ============
Route::get('/verifikasi/{qrCode}', [SuratPdfController::class, 'verifikasi'])->name('surat.verifikasi');

});

