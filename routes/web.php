<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComplaintController;

// Admin
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController; // <- untuk admin dashboard dinamis

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

Route::get('/profil-lembaga', fn () => view('profil-lembaga'))->name('profil-lembaga');
Route::get('/kontak-kami', fn () => view('kontak-kami'))->name('kontak-kami');

/*
|--------------------------------------------------------------------------
| Static pages (user) – perlu login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/cara-melapor', fn () => view('dashboard.user.cara-melapor'))->name('cara-melapor');
    Route::get('/FAQ',          fn () => view('dashboard.user.FAQ'))->name('FAQ');
    Route::get('/kontak',       fn () => view('dashboard.user.kontak'))->name('kontak');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Dashboard router (auto-redirect by role)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => view('dashboard.user.home'))
    ->middleware(['auth','verified','redirect.dashboard.byrole'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN Dashboard & Pages (admin & super-admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified','role:admin|super-admin'])->group(function () {
    // Admin home pakai controller supaya bisa hitung statistik
    Route::get('/admin', DashboardController::class)->name('admin.dashboard');

    // (opsional) halaman statis lain
    Route::get('/admin.manajemen-pengaduan', fn () => view('dashboard.admin.manajemen-pengaduan'))
        ->name('admin.manajemen-pengaduan');
});

/*
|--------------------------------------------------------------------------
| USER – Pengaduan (semua user terverifikasi)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/pengaduan',                 [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/pengaduan/buat',            [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/pengaduan',                [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/pengaduan/{complaint}',     [ComplaintController::class, 'show'])->name('complaints.show');
});

/*
|--------------------------------------------------------------------------
| ADMIN – Pengaduan (admin & super-admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified','role:admin|super-admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/complaints',                             [AdminComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{complaint}',                 [AdminComplaintController::class, 'show'])->name('complaints.show');
        Route::patch('/complaints/{complaint}/status',        [AdminComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');

        // Export CSV (menggunakan filter query string yang sama)
        Route::get('/complaints/export/csv',                  [AdminComplaintController::class, 'exportCsv'])
            ->name('complaints.export.csv');
    });

/*
|--------------------------------------------------------------------------
| ADMIN – Manajemen User (khusus super-admin)
|--------------------------------------------------------------------------
| Hanya super-admin yang boleh melihat & memanipulasi user.
*/
Route::middleware(['auth','verified','role:super-admin'])
    ->prefix('admin/users')
    ->as('admin.users.')
    ->group(function () {
        Route::get('/',                       [AdminUserController::class, 'index'])->name('index');
        Route::get('/create',                 [AdminUserController::class, 'create'])->name('create');
        Route::post('/',                      [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit',            [AdminUserController::class, 'edit'])->name('edit');
        Route::patch('/{user}',               [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}',              [AdminUserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('resetPassword');
    });

require __DIR__.'/auth.php';
