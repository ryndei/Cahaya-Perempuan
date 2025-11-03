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
use RealRashid\SweetAlert\Facades\Alert;
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
        $roleFilter = $request->get('role', 'all');

        $query = User::query()
            ->with('roles')
            // sembunyikan super-admin dari listing
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

        // SweetAlert sukses + info password (sekali tampil)
        Alert::success('Sukses', "User {$user->email} dibuat sebagai {$data['role']}.")
            ->autoClose(5000);
        Alert::info('Password Sementara', "Password: {$plain}")
            ->autoClose(8000);

        return redirect()->route('admin.users.index', ['role' => $data['role']]);
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

        Alert::success('Sukses', 'User berhasil diperbarui.')->autoClose(4000);

        return redirect()->route('admin.users.index', ['role' => $data['role']]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            Alert::warning('Gagal', 'Tidak bisa menghapus akun Anda sendiri.')->autoClose(5000);
            return back();
        }

        if ($user->hasRole('super-admin')) {
            Alert::error('Ditolak', 'Tidak bisa menghapus Super Admin.')->autoClose(5000);
            return back();
        }

        // opsional: pastikan yang dihapus memang user/admin
        if (! $user->hasAnyRole($this->manageableRoles)) {
            Alert::warning('Ditolak', 'User ini tidak termasuk role yang dapat dihapus dari modul ini.')->autoClose(5000);
            return back();
        }

        $email = $user->email;
        $user->delete();

        Alert::success('Sukses', "User {$email} berhasil dihapus.")->autoClose(4000);

        return redirect()->route('admin.users.index');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        if ($user->hasRole('super-admin')) {
            Alert::error('Ditolak', 'Tidak bisa reset password untuk Super Admin.')->autoClose(5000);
            abort(403);
        }

        $plain = $this->generatePassword();
        $user->update(['password' => Hash::make($plain)]);

        Alert::success('Password Direset', "Password baru untuk {$user->email}: {$plain}")
            ->autoClose(8000);

        return back();
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
