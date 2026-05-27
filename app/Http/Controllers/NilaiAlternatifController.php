<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
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

    public function index()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $kriterias = Kriteria::orderBy('id')->get();
        $alternatifs = Alternatif::orderBy('id')->get();
        $nilaiAlternatif = NilaiAlternatif::all()->keyBy(function ($item) {
            return $item->alternatif_id . '_' . $item->kriteria_id;
        });

        return view('pages.nilai_alternatif.index', compact('kriterias', 'alternatifs', 'nilaiAlternatif'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $kriterias = Kriteria::pluck('id')->toArray();
        $alternatifs = Alternatif::pluck('id')->toArray();
        $input = $request->input('nilai', []);

        if (!is_array($input)) {
            return back()->with('error', 'Format input nilai tidak valid.')->withInput();
        }

        foreach ($alternatifs as $altId) {
            if (!isset($input[$altId]) || !is_array($input[$altId])) {
                return back()->with('error', 'Silakan isi semua nilai alternatif untuk setiap kriteria.')->withInput();
            }

            foreach ($kriterias as $kritId) {
                if (!array_key_exists($kritId, $input[$altId]) || !in_array($input[$altId][$kritId], ['1', '3', '5'])) {
                    return back()->with('error', 'Semua nilai harus dipilih dari opsi: Baik (5), Sedang (3), atau Buruk (1).')->withInput();
                }

                NilaiAlternatif::updateOrCreate(
                    [
                        'alternatif_id' => $altId,
                        'kriteria_id' => $kritId,
                    ],
                    [
                        'nilai' => $input[$altId][$kritId],
                    ]
                );
            }
        }

        return redirect()->route('nilai-alternatif.index')->with('success', 'Semua nilai alternatif berhasil disimpan.');
    }
}
