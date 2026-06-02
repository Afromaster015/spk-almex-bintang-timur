<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use App\Models\Periode;
use Illuminate\Http\Request;

class NilaiAlternatifController extends Controller
{
    private function ensureLoggedIn()
    {
        if (!session()->has('username')) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
        }

        return null;
    }

    private function getCurrentPeriode()
    {
        $periodeId = session('current_periode_id');
        $periode = $periodeId ? Periode::find($periodeId) : null;

        if (!$periode) {
            // Do not depend on status; pick the latest periode if none selected
            $periode = Periode::orderBy('tahun', 'desc')
                ->orderByRaw("FIELD(nama_periode, 'Q1', 'Q2', 'Q3', 'Q4')")
                ->first();

            if ($periode) {
                session(['current_periode_id' => $periode->id]);
            }
        }

        return $periode;
    }

    public function index()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $currentPeriode = $this->getCurrentPeriode();
        $kriterias = Kriteria::orderBy('id')->get();

        $nilaiAlternatif = $currentPeriode
            ? NilaiAlternatif::where('periode_id', $currentPeriode->id)->get()
            : collect();

        $alternatifIds = $nilaiAlternatif->pluck('alternatif_id')->unique()->toArray();
        $alternatifs = $currentPeriode ? Alternatif::whereIn('id', $alternatifIds)->orderBy('kode_alternatif')->get() : collect();

        $nilaiAlternatif = $nilaiAlternatif->keyBy(function ($item) {
            return $item->alternatif_id . '_' . $item->kriteria_id;
        });

        return view('pages.nilai_alternatif.index', compact('kriterias', 'alternatifs', 'nilaiAlternatif', 'currentPeriode'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $currentPeriode = $this->getCurrentPeriode();

        if (!$currentPeriode) {
            return back()->with('error', 'Tidak ada periode terpilih. Silakan buat atau pilih periode terlebih dahulu.');
        }

        // No longer enforce 'Aktif' status; periods are editable anytime

        $kriterias = Kriteria::orderBy('id')->get();
        $kriteriasMap = $kriterias->keyBy('id');
        $input = $request->input('nilai', []);
        $alternatifIds = array_keys($input);
        $validAlternatifs = Alternatif::whereIn('id', $alternatifIds)->pluck('id')->toArray();

        if (empty($validAlternatifs)) {
            return back()->with('error', 'Tidak ada alternatif yang valid untuk disimpan.')->withInput();
        }

        if (!is_array($input)) {
            return back()->with('error', 'Format input nilai tidak valid.')->withInput();
        }

        foreach ($validAlternatifs as $altId) {
            if (!isset($input[$altId]) || !is_array($input[$altId])) {
                return back()->with('error', 'Silakan isi semua nilai alternatif untuk setiap kriteria.')->withInput();
            }

            foreach ($kriteriasMap as $kritId => $kriteria) {
                if (!array_key_exists($kritId, $input[$altId]) || !in_array($input[$altId][$kritId], ['1', '3', '5'])) {
                    return back()->with('error', 'Semua nilai harus dipilih dari opsi: Baik (5), Sedang (3), atau Buruk (1).')->withInput();
                }

                $nilaiInput = (int) $input[$altId][$kritId];
                
                // Untuk kriteria 'cost', balik pemetaan nilai agar Baik tetap berarti baik
                // 5 (Baik) → 1, 3 (Sedang) → 3, 1 (Buruk) → 5
                if ($kriteria->jenis === 'cost') {
                    $nilaiInput = $nilaiInput === 5 ? 1 : ($nilaiInput === 1 ? 5 : 3);
                }

                NilaiAlternatif::updateOrCreate(
                    [
                        'alternatif_id' => $altId,
                        'kriteria_id' => $kritId,
                        'periode_id' => $currentPeriode->id,
                    ],
                    [
                        'nilai' => $nilaiInput,
                    ]
                );
            }
        }

        return redirect()->route('nilai-alternatif.index')->with('success', 'Semua nilai alternatif berhasil disimpan.');
    }
}
