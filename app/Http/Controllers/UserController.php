<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Daftar semua user (dengan filter)
     */
    public function index(Request $request)
    {
        $query = User::with(['rt', 'roles']);

        // Filter by RT
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_akun', $request->status);
        }

        // Search nama / NIK / no_hp
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('no_hp', 'like', "%{$q}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'rts', 'roles'));
    }

    /**
     * Form buat user baru
     */
    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('users.create', compact('rts', 'roles', 'permissions'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Identitas
            'nik' => 'required|string|size:16|unique:users,nik',
            'name' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15|unique:users,no_hp',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8',

            // Kependudukan
            'rt_id' => 'nullable|exists:rt,id',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string|size:16',
            'status_kependudukan' => 'required|in:warga,pendatang,kontrak',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:20',
            'pekerjaan' => 'nullable|string|max:100',
            'status_kawin' => 'nullable|string|max:30',
            'warga_id' => 'nullable|exists:warga,id',
            'status_keluarga' => 'required|in:kepala,istri,anak,famili,lainnya',

            // Jabatan
            'jabatan' => 'nullable|string|max:100',
            'periode_jabatan_mulai' => 'nullable|date',
            'periode_jabatan_selesai' => 'nullable|date|after_or_equal:periode_jabatan_mulai',

            // Akun
            'status_akun' => 'required|in:pending,aktif,nonaktif',

            // Role & Permission
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Normalisasi no_hp
        $validated['no_hp'] = preg_replace('/[^0-9]/', '', $validated['no_hp']);

        // Buat user
        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'rt_id' => $validated['rt_id'] ?? null,
            'warga_id' => $validated['warga_id'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_kk' => $validated['no_kk'] ?? null,
            'status_kependudukan' => $validated['status_kependudukan'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tgl_lahir' => $validated['tgl_lahir'] ?? null,
            'agama' => $validated['agama'] ?? null,
            'pekerjaan' => $validated['pekerjaan'] ?? null,
            'status_kawin' => $validated['status_kawin'] ?? null,
            'status_keluarga' => $validated['status_keluarga'],
            'jabatan' => $validated['jabatan'] ?? null,
            'periode_jabatan_mulai' => $validated['periode_jabatan_mulai'] ?? null,
            'periode_jabatan_selesai' => $validated['periode_jabatan_selesai'] ?? null,
            'status_akun' => $validated['status_akun'],
            'must_change_password' => true,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'warga_id' => $validated['warga_id'] ?? null,
        ]);

        // Assign role
        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Assign permission override (langsung ke user)
        if (!empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        // Kalau dihubungkan ke warga, update warga->user_id
        if (!empty($validated['warga_id'])) {
            \App\Models\Warga::find($validated['warga_id'])?->update(['user_id' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', "Akun {$user->name} berhasil dibuat. Password awal: {$validated['password']}");
    }

    /**
     * Detail user
     */
    public function show(User $user)
    {
        $user->load(['rt', 'roles', 'permissions']);
        return view('users.show', compact('user'));
    }

    /**
     * Form edit user
     */
    public function edit(User $user)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->permissions->pluck('name')->toArray();

        return view('users.edit', compact('user', 'rts', 'roles', 'permissions', 'userRoles', 'userPermissions'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik,' . $user->id,
            'name' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15|unique:users,no_hp,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',

            'rt_id' => 'nullable|exists:rt,id',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string|size:16',
            'status_kependudukan' => 'required|in:warga,pendatang,kontrak',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:20',
            'pekerjaan' => 'nullable|string|max:100',
            'status_kawin' => 'nullable|string|max:30',
            'status_keluarga' => 'required|in:kepala,istri,anak,famili,lainnya',

            'jabatan' => 'nullable|string|max:100',
            'periode_jabatan_mulai' => 'nullable|date',
            'periode_jabatan_selesai' => 'nullable|date|after_or_equal:periode_jabatan_mulai',

            'status_akun' => 'required|in:pending,aktif,nonaktif',

            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $validated['no_hp'] = preg_replace('/[^0-9]/', '', $validated['no_hp']);

        // Update data
        $data = collect($validated)->except(['password', 'roles', 'permissions'])->toArray();

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
            $data['must_change_password'] = true;
            $data['last_password_change'] = now();
        }

        $user->update($data);

        // Sync role & permission
        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('users.index')
            ->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun {$nama} berhasil dihapus.");
    }

    /**
     * Reset password user (generate password baru)
     */
    public function resetPassword(User $user)
    {
        $newPassword = 'RW' . Str::random(6) . '!';

        $user->update([
            'password' => Hash::make($newPassword),
            'must_change_password' => true,
            'last_password_change' => now(),
        ]);

        return back()->with('success', "Password {$user->name} direset. Password baru: {$newPassword}");
    }
}