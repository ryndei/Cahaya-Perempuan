<?php

use Illuminate\Support\Facades\Route;
use App\Models\News;

// Controllers
use App\Http\Controllers\ProfileController;

// USER controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ComplaintController as UserComplaintController;

// ADMIN controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NewsController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');

Route::view('/profil-lembaga', 'profil-lembaga')->name('profil-lembaga');
Route::view('/kontak-kami', 'kontak-kami')->name('kontak-kami');
Route::view('/cara-melapor-landing', 'cara-melapor-landing')->name('cara-melapor-landing');
Route::view('/syarat-layanan', 'syarat-layanan')->name('syarat-layanan');

/*
|--------------------------------------------------------------------------
| Static pages (requires login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::view('/cara-melapor', 'dashboard.user.cara-melapor')->name('cara-melapor');
    Route::view('/FAQ',          'dashboard.user.FAQ')->name('FAQ');
    Route::view('/kontak',       'dashboard.user.kontak')->name('kontak');
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
    ->middleware(['auth', 'verified', 'redirect.dashboard.byrole'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| USER – Pengaduan (binding by code)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pengaduan',                  [UserComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/pengaduan/buat',             [UserComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/pengaduan',                 [UserComplaintController::class, 'store'])
        ->middleware('throttle:complaints')->name('complaints.store');

    // Binding by code (aman dari tebakan ID numerik)
    Route::get('/pengaduan/{complaint:code}', [UserComplaintController::class, 'show'])->name('complaints.show');
});

/*
|--------------------------------------------------------------------------
| ADMIN – Dashboard (admin & super-admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin|super-admin'])->group(function () {
    // Pastikan AdminDashboardController bersifat __invoke
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');

    // Halaman statis admin
    Route::view('/admin/manajemen-pengaduan', 'dashboard.admin.manajemen-pengaduan')
        ->name('admin.manajemen-pengaduan');
});

/*
|--------------------------------------------------------------------------
| ADMIN – Pengaduan (admin & super-admin)  ★ Force binding by ID
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin|super-admin', 'permission:complaint.manage'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Export
        Route::get('/complaints/export/csv',  [AdminComplaintController::class, 'exportCsv'])->name('complaints.export.csv');
        Route::get('/complaints/export/xlsx', [AdminComplaintController::class, 'exportXlsx'])->name('complaints.export.xlsx');

        // Index
        Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');

        // Update status by ID
        Route::patch('/complaints/{complaint:id}/status', [AdminComplaintController::class, 'updateStatus'])
            ->whereNumber('complaint')
            ->name('complaints.updateStatus');

        // Show by ID
        Route::get('/complaints/{complaint:id}', [AdminComplaintController::class, 'show'])
            ->whereNumber('complaint')
            ->name('complaints.show');
    });

/*
|--------------------------------------------------------------------------
| ADMIN – Manajemen User (super-admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super-admin'])
    ->prefix('admin/users')
    ->as('admin.users.')
    ->group(function () {
        Route::get('/',                       [AdminUserController::class, 'index'])->name('index');
        Route::get('/create',                 [AdminUserController::class, 'create'])->name('create');
        Route::post('/',                      [AdminUserController::class, 'store'])->name('store');

        Route::get('/{user}/edit',            [AdminUserController::class, 'edit'])
            ->whereNumber('user')->name('edit');

        Route::patch('/{user}',               [AdminUserController::class, 'update'])
            ->whereNumber('user')->name('update');

        Route::delete('/{user}',              [AdminUserController::class, 'destroy'])
            ->whereNumber('user')->name('destroy');

        Route::post('/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
            ->whereNumber('user')->name('resetPassword');
    });

/*
|--------------------------------------------------------------------------
| ADMIN – News (admin & super-admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin|super-admin', 'permission:news.manage'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::resource('news', NewsController::class)
            ->except(['show'])
            ->names('news');

        // Redirect jika user akses /admin/news/{news} tanpa /edit
        Route::get('news/{news}', function (News $news) {
            return redirect()->route('admin.news.edit', $news);
        })->name('news.redirect');
    });

/*
|--------------------------------------------------------------------------
| Publik – Berita (scope published + slug)
|--------------------------------------------------------------------------
*/
Route::get('/berita', function () {
    $items = News::published()->latest('published_at')->paginate(9);
    return view('news.index', compact('items'));
})->name('news.index');

Route::get('/berita/{news:slug}', function (News $news) {
    abort_unless($news->status === 'published', 404);
    return view('news.show', compact('news'));
})->name('news.show');

/*
|--------------------------------------------------------------------------
| Auth routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| DEV ONLY – Test error
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(fn () => abort(404));
