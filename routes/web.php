<?php

use Illuminate\Support\Facades\Route;

// Profile
use App\Http\Controllers\ProfileController;

// ================= USER controllers =================
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ComplaintController as UserComplaintController;

// ================= ADMIN controllers ================
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

Route::get('/profil-lembaga', fn () => view('profil-lembaga'))->name('profil-lembaga');
Route::get('/kontak-kami', fn () => view('kontak-kami'))->name('kontak-kami');
Route::get('/cara-melapor-landing', fn () => view('cara-melapor-landing'))->name('cara-melapor-landing');
Route::get('/syarat-layanan', fn () => view('syarat-layanan'))->name('syarat-layanan');

/*
|--------------------------------------------------------------------------
| Static pages (butuh login)
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
| USER Dashboard (auto-redirect by role)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth','verified','redirect.dashboard.byrole'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN Dashboard & Pages (admin & super-admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified','role:admin|super-admin'])->group(function () {
    // Admin home (invokable controller)
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');

    // (opsional) halaman statis lain
    Route::get('/admin.manajemen-pengaduan', fn () => view('dashboard.admin.manajemen-pengaduan'))
        ->name('admin.manajemen-pengaduan');
});

/*
|--------------------------------------------------------------------------
| USER – Pengaduan
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/pengaduan',                  [UserComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/pengaduan/buat',             [UserComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/pengaduan',                 [UserComplaintController::class, 'store'])->middleware('throttle:complaints')->name('complaints.store');
    Route::get('/pengaduan/{complaint:code}', [UserComplaintController::class, 'show'])->name('complaints.show');
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
        Route::get('/complaints',                      [AdminComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{complaint}',          [AdminComplaintController::class, 'show'])->name('complaints.show');
        Route::patch('/complaints/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');

        // Export CSV (mengikuti filter querystring yang sama)
        Route::get('/complaints/export/csv',           [AdminComplaintController::class, 'exportCsv'])
            ->name('complaints.export.csv');
    });

require __DIR__.'/auth.php';
