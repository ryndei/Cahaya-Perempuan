<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profil-lembaga', function () {
    return view('profil-lembaga');   // pastikan file ini ada
})->name('profil-lembaga');
Route::get('/kontak-kami', function () {
    return view('kontak-kami');   // pastikan file ini ada
})->name('kontak-kami');

Route::get('/dashboard', function () {
    return view('dashboard.user.home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/pengaduan', function () {
        return view('dashboard.user.pengaduan');
    })->name('pengaduan');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/cara-melapor', function () {
        return view('dashboard.user.cara-melapor');
    })->name('cara-melapor');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/FAQ', function () {
        return view('dashboard.user.FAQ');
    })->name('FAQ');
});
Route::middleware(['auth'])->group(function () {
    Route::get('.kontak', function () {
        return view('dashboard.user.kontak');
    })->name('kontak');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
