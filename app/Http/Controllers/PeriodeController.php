<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodeController extends Controller
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

        $periodes = Periode::orderBy('tahun')
            ->orderByRaw("FIELD(nama_periode, 'Q1', 'Q2', 'Q3', 'Q4')")
            ->get();

        return view('pages.periode.index', compact('periodes'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $validated = $request->validate([
            'nama_periode' => [
                'required',
                'string',
                Rule::in(['Q1', 'Q2', 'Q3', 'Q4']),
                Rule::unique('periodes')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->input('tahun'));
                }),
            ],
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
        ]);

        // Create new periode as non-active by default (Selesai used as inactive label in DB)
        $periode = Periode::create([
            'nama_periode' => $validated['nama_periode'],
            'tahun' => $validated['tahun'],
            'status' => 'Selesai',
        ]);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil dibuat. Pilih periode untuk menjadikannya aktif.');
    }

    public function setCurrent(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $request->validate([
            'periode_id' => 'required|integer|exists:periodes,id',
        ]);

        $periodeId = $request->input('periode_id');

        // Set selected periode as Aktif and others as Selesai (inactive)
        Periode::where('id', '!=', $periodeId)->update(['status' => 'Selesai']);
        Periode::where('id', $periodeId)->update(['status' => 'Aktif']);

        session(['current_periode_id' => $periodeId]);

        return redirect()->back()->with('success', 'Periode terpilih telah diperbarui.');
    }

    public function destroy(Periode $periode)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $periodeId = $periode->id;
        $periode->delete();

        if (session('current_periode_id') === $periodeId) {
            session()->forget('current_periode_id');
        }

        return redirect()->back()->with('success', 'Periode berhasil dihapus.');
    }
}
