<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Complaint;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;

    /**
     * Lihat daftar pengaduan (index).
     * Semua user login boleh melihat daftar miliknya.
     */
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Lihat detail sebuah pengaduan.
     * Pemilik tiket ATAU admin/super-admin diperbolehkan.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin']);
        return $isAdmin || $user->id === $complaint->user_id;
    }

    /**
     * Buat pengaduan baru.
     * Semua user login (opsional: yang sudah verified) diizinkan.
     */
    public function create(User $user): bool
    {
        // Jika ingin wajib verified: return $user->hasVerifiedEmail();
        return $user !== null;
    }

    /**
     * Ubah data pengaduan (kalau ada fitur edit).
     * Khusus admin/super-admin.
     */
    public function update(User $user, Complaint $complaint): bool
    {
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin']);
        return $isAdmin;
    }

    /**
     * Hapus pengaduan (jika ada).
     * Khusus admin/super-admin.
     */
    public function delete(User $user, Complaint $complaint): bool
    {
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin']);
        return $isAdmin;
    }

    /**
     * Ability kustom: ubah status pengaduan (dipakai admin controller).
     */
    public function updateStatus(User $user, Complaint $complaint): bool
    {
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin']);
        return $isAdmin;
    }

    /**
     * Ability kustom: ekspor data.
     */
    public function export(User $user): bool
    {
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin']);
        return $isAdmin;
    }
}
