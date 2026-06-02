<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use App\Models\Periode;
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

        $currentPeriode = Periode::find(session('current_periode_id'));
        $alternatifs = Alternatif::orderBy('kode_alternatif')->get();

        return view('pages.alternatif.index', compact('alternatifs', 'currentPeriode'));
    }

    public function select()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $currentPeriode = Periode::find(session('current_periode_id'));
        $selectedAlternatifIds = $currentPeriode
            ? NilaiAlternatif::where('periode_id', $currentPeriode->id)->distinct()->pluck('alternatif_id')->toArray()
            : [];
        $alternatifs = Alternatif::orderBy('kode_alternatif')->get();

        return view('pages.alternatif.select', compact('alternatifs', 'currentPeriode', 'selectedAlternatifIds'));
    }

    public function toggleStatus(Request $request, Alternatif $alternatif)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $currentPeriode = Periode::find(session('current_periode_id'));
        if (!$currentPeriode) {
            return back()->with('error', 'Pilih periode terlebih dahulu sebelum mengubah status alternatif.');
        }

        $kriterias = Kriteria::orderBy('id')->get();
        if ($kriterias->isEmpty()) {
            return back()->with('error', 'Tambahkan minimal satu kriteria terlebih dahulu sebelum memilih alternatif.');
        }

        $existingValues = NilaiAlternatif::where('periode_id', $currentPeriode->id)
            ->where('alternatif_id', $alternatif->id)
            ->exists();

        if ($existingValues) {
            NilaiAlternatif::where('periode_id', $currentPeriode->id)
                ->where('alternatif_id', $alternatif->id)
                ->delete();

            return back()->with('success', 'Alternatif berhasil dinonaktifkan untuk periode aktif.');
        }

        foreach ($kriterias as $kriteria) {
            NilaiAlternatif::firstOrCreate([
                'alternatif_id' => $alternatif->id,
                'kriteria_id' => $kriteria->id,
                'periode_id' => $currentPeriode->id,
            ], [
                'nilai' => null,
            ]);
        }

        return back()->with('success', 'Alternatif berhasil dipilih untuk periode aktif.');
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
            'kode_alternatif' => ['required','string','max:50', Rule::unique('alternatifs', 'kode_alternatif')],
            'nama_pelanggan' => 'required|string|max:150',
        ], [
            'kode_alternatif.unique' => 'Kode Pelanggan yang diinput sudah terdaftar.',
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
        ], [
            'kode_alternatif.unique' => 'Kode Pelanggan yang diinput sudah terdaftar.',
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
