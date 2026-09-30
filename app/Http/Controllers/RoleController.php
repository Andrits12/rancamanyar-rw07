<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Daftar semua role
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Form buat role baru
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('roles.create', compact('permissions'));
    }

    /**
     * Simpan role baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'name.regex' => 'Nama role hanya boleh huruf kecil dan underscore (contoh: ketua_rt).',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role {$role->name} berhasil dibuat.");
    }

    /**
     * Detail role
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('roles.show', compact('role', 'permissions'));
    }

    /**
     * Form edit role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role (nama & permission)
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id . '|regex:/^[a-z_]+$/',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Cegah ubah nama role bawaan
        $protectedRoles = ['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris', 'bendahara', 'sesepuh', 'security', 'posyandu', 'warga'];
        if (in_array($role->name, $protectedRoles) && $role->name !== $validated['name']) {
            return back()->with('error', 'Nama role bawaan tidak dapat diubah.');
        }

        $role->update(['name' => $validated['name']]);

        // Sync permissions (super_admin selalu punya semua)
        if ($role->name === 'super_admin') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role {$role->name} berhasil diperbarui.");
    }

    /**
     * Hapus role
     */
    public function destroy(Role $role)
    {
        $protectedRoles = ['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris', 'bendahara', 'sesepuh', 'security', 'posyandu', 'warga'];

        if (in_array($role->name, $protectedRoles)) {
            return back()->with('error', 'Role bawaan tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role masih dipakai oleh user. Pindahkan user terlebih dahulu.');
        }

        $nama = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role {$nama} berhasil dihapus.");
    }
}