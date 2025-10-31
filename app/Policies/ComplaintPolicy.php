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
     * Semua user login boleh (nanti filter "milik sendiri" ada di controller/query).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Lihat detail sebuah pengaduan.
     * Diizinkan jika:
     * - punya permission 'complaint.manage' (admin/super-admin), ATAU
     * - pemilik tiket (user yang buat).
     */
    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->can('complaint.manage')) {
            return true;
        }
        return $user->id === $complaint->user_id;
    }

    /**
     * Buat pengaduan baru.
     * Diizinkan jika punya 'complaint.create'.
     * (Kalau butuh verified: && $user->hasVerifiedEmail())
     */
    public function create(User $user): bool
    {
        return $user->can('complaint.create');
    }

    /**
     * Ubah data pengaduan (jika ada fitur edit).
     * Admin/super-admin saja (punya 'complaint.manage').
     */
    public function update(User $user, Complaint $complaint): bool
    {
        return $user->can('complaint.manage');
    }

    /**
     * Hapus pengaduan (jika ada).
     * Admin/super-admin saja.
     */
    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->can('complaint.manage');
    }

    /**
     * Ability kustom: ubah status pengaduan (dipakai di Admin controller).
     */
    public function updateStatus(User $user, Complaint $complaint): bool
    {
        return $user->can('complaint.manage');
    }

    /**
     * Ability kustom: ekspor data pengaduan (Admin).
     * Kalau nanti mau granular, buat permission 'complaint.export' dan map ke role terkait.
     */
    public function export(User $user): bool
    {
        return $user->can('complaint.manage');
    }
}
