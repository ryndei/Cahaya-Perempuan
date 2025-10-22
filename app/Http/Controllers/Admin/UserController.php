<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Hanya role ini yang bisa diatur oleh super-admin via modul ini.
     */
    private array $manageableRoles = ['user', 'admin'];

    public function index(Request $request): View
    {
        $q          = trim((string) $request->get('q', ''));
        $roleFilter = $request->get('role', 'all'); // default tampil admin

        $query = User::query()
            ->with('roles')
            // pastikan super-admin tidak ikut tampil di listing
            ->whereDoesntHave('roles', fn ($r) => $r->where('name', 'super-admin'));

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // filter role: user | admin | all
        if (in_array($roleFilter, $this->manageableRoles, true)) {
            $query->whereHas('roles', fn ($r) => $r->where('name', $roleFilter));
        } else {
            $query->whereHas('roles', fn ($r) => $r->whereIn('name', $this->manageableRoles));
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('dashboard.admin.users.index', compact('users', 'q', 'roleFilter'));
    }

    public function create(): View
    {
        $this->ensureRolesExist();
        $roles = $this->manageableRoles; // user & admin
        return view('dashboard.admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureRolesExist();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'  => ['required', Rule::in($this->manageableRoles)],
        ]);

        $plain = $this->generatePassword();

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($plain),
            'email_verified_at' => now(), // auto-verify agar langsung aktif
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index', ['role' => $data['role']])
            ->with('status', "User {$user->email} dibuat sebagai {$data['role']}. Password: {$plain}");
    }

    public function edit(User $user): View
    {
        // cegah modifikasi super-admin dari modul ini
        if ($user->hasRole('super-admin')) {
            abort(403);
        }

        $roles = $this->manageableRoles;
        return view('dashboard.admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('super-admin')) {
            abort(403);
        }

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', Rule::in($this->manageableRoles)],
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index', ['role' => $data['role']])
            ->with('status', 'User diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('status', 'Tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->hasRole('super-admin')) {
            return back()->with('status', 'Tidak bisa menghapus Super Admin.');
        }

        // opsional: pastikan yang dihapus memang user/admin
        if (! $user->hasAnyRole($this->manageableRoles)) {
            return back()->with('status', 'User ini tidak termasuk role yang dapat dihapus dari modul ini.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User dihapus.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        if ($user->hasRole('super-admin')) {
            abort(403);
        }

        $plain = $this->generatePassword();
        $user->update(['password' => Hash::make($plain)]);

        return back()->with('status', "Password baru untuk {$user->email}: {$plain}");
    }

    /**
     * Pastikan role dasar tersedia (jika belum, buat).
     */
    private function ensureRolesExist(): void
    {
        foreach (['user', 'admin', 'super-admin'] as $r) {
            Role::findOrCreate($r, 'web');
        }
    }

    /**
     * Generator password sederhana.
     * Gunakan Str::password jika tersedia; fallback ke Str::random.
     */
    private function generatePassword(int $length = 12): string
    {
        if (method_exists(Str::class, 'password')) {
            return Str::password($length);
        }
        return Str::random($length);
    }
}
