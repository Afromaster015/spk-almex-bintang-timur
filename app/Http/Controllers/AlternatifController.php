<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlternatifController extends Controller
{
    private function ensureLoggedIn()
    {
        if (!session()->has('username')) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $alternatifs = Alternatif::orderBy('id')->get();
        return view('pages.alternatif.index', compact('alternatifs'));
    }

    public function create()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        return view('pages.alternatif.form', [
            'alternatif' => new Alternatif(),
            'action' => route('alternatif.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $validated = $request->validate([
            'kode_alternatif' => 'required|string|unique:alternatifs,kode_alternatif|max:50',
            'nama_pelanggan' => 'required|string|max:150',
        ]);

        Alternatif::create([
            'kode_alternatif' => $validated['kode_alternatif'],
            'nama_pelanggan' => $validated['nama_pelanggan'],
        ]);

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan.');
    }

    public function edit(Alternatif $alternatif)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        return view('pages.alternatif.form', [
            'alternatif' => $alternatif,
            'action' => route('alternatif.update', $alternatif),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $validated = $request->validate([
            'kode_alternatif' => ['required', 'string', 'max:50', Rule::unique('alternatifs', 'kode_alternatif')->ignore($alternatif->id)],
            'nama_pelanggan' => 'required|string|max:150',
        ]);

        $alternatif->update([
            'kode_alternatif' => $validated['kode_alternatif'],
            'nama_pelanggan' => $validated['nama_pelanggan'],
        ]);

        return redirect()->route('alternatif.index')->with('success', 'Data alternatif berhasil diperbarui.');
    }

    public function destroy(Alternatif $alternatif)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $alternatif->delete();

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus.');
    }
}
