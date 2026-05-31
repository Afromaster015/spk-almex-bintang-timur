<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\NilaiAlternatifController;
use App\Http\Controllers\PerhitunganController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/login', function () {
    return view('pages.login');
});

Route::post('/proses-login', [AuthController::class, 'prosesLogin']);

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
Route::get('/perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');
Route::post('/perhitungan/create-missing-values', [PerhitunganController::class, 'createMissingValues'])->name('perhitungan.create-missing-values');
Route::resource('kriteria', KriteriaController::class)->parameters(['kriteria' => 'kriteria']);
Route::resource('alternatif', AlternatifController::class)->parameters(['alternatif' => 'alternatif']);

