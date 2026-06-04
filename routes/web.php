<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\NilaiAlternatifController;
use App\Http\Controllers\PerhitunganController;
use App\Http\Controllers\PeriodeController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/login', function () {
    return view('pages.login');
});

Route::get('/register', function () {
    return view('pages.register');
});

Route::get('/forgot-password', function () {
    return view('pages.forgot-password');
});

Route::post('/proses-login', [AuthController::class, 'prosesLogin']);
Route::post('/proses-register', [AuthController::class, 'prosesRegister']);
Route::post('/proses-forgot-password', [AuthController::class, 'prosesForgotPassword']);

Route::get('/dashboard', function () {
    if (!session()->has('username')) {
        return redirect('/login')->with('error', 'Anda harus login terlebih dahulu untuk mengakses Dashboard!');
    }

    return view('pages.dashboard');
})->name('dashboard');

// Rute untuk proses logout
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/kriteria/bobot', [KriteriaController::class, 'bobotIndex'])->name('kriteria.bobot');
Route::post('/kriteria/bobot', [KriteriaController::class, 'bobotStore'])->name('kriteria.bobot.store');
Route::get('/kriteria/hasil-bobot', [KriteriaController::class, 'hasilBobot'])->name('kriteria.hasil-bobot');
Route::get('/nilai-alternatif', [NilaiAlternatifController::class, 'index'])->name('nilai-alternatif.index');
Route::post('/nilai-alternatif', [NilaiAlternatifController::class, 'store'])->name('nilai-alternatif.store');
Route::get('/alternatif/pilih', [AlternatifController::class, 'select'])->name('alternatif.select');
Route::post('/alternatif/{alternatif}/toggle-status', [AlternatifController::class, 'toggleStatus'])->name('alternatif.toggle-status');
Route::get('/perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');
Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
Route::post('/periode/set', [PeriodeController::class, 'setCurrent'])->name('periode.set');
Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');
Route::resource('kriteria', KriteriaController::class)->parameters(['kriteria' => 'kriteria']);
Route::resource('alternatif', AlternatifController::class)->parameters(['alternatif' => 'alternatif']);

