<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\KriteriaBobotMatrix;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
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
        return view('pages.kriteria.index', compact('kriterias'));
    }

    public function create()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        return view('pages.kriteria.form', [
            'kriteria' => new Kriteria(),
            'action' => route('kriteria.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $validated = $request->validate([
            'kode_kriteria' => 'required|string|unique:kriterias,kode_kriteria|max:50',
            'nama_kriteria' => 'required|string|max:150',
            'jenis' => ['required', Rule::in(['benefit', 'cost'])],
        ]);

        Kriteria::create($validated);

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function edit(Kriteria $kriteria)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        return view('pages.kriteria.form', [
            'kriteria' => $kriteria,
            'action' => route('kriteria.update', ['kriteria' => $kriteria->id]),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $validated = $request->validate([
            'kode_kriteria' => ['required', 'string', 'max:50', Rule::unique('kriterias', 'kode_kriteria')->ignore($kriteria->id)],
            'nama_kriteria' => 'required|string|max:150',
            'jenis' => ['required', Rule::in(['benefit', 'cost'])],
        ]);

        $kriteria->update($validated);

        return redirect()->route('kriteria.index')->with('success', 'Data kriteria berhasil diperbarui.');
    }

    public function hasilBobot()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $kriteria = Kriteria::orderBy('id')->get();

        return view('pages.kriteria.hasil', compact('kriteria'));
    }

    public function destroy(Kriteria $kriteria)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        NilaiAlternatif::where('kriteria_id', $kriteria->id)->delete();
        $kriteria->delete();

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }

    public function bobotIndex()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $kriteria = Kriteria::orderBy('id')->get();

        if ($kriteria->count() < 2) {
            return redirect()->route('kriteria.index')->with('error', 'Minimal dua kriteria diperlukan untuk menghitung bobot AHP.');
        }

        $savedMatrix = session('kriteria_bobot_matrix', []);
        $lastSavedMatrix = KriteriaBobotMatrix::latest()->first();
        $latestMatrixAt = null;

        if ($lastSavedMatrix) {
            $latestMatrixAt = $lastSavedMatrix->created_at;

            if (empty($savedMatrix) && $lastSavedMatrix->kriteria_ids === $kriteria->pluck('id')->all()) {
                $savedMatrix = $lastSavedMatrix->matrix;
            }
        }

        return view('pages.kriteria.bobot', compact('kriteria', 'savedMatrix', 'latestMatrixAt'));
    }

    public function bobotStore(Request $request)
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $kriteria = Kriteria::orderBy('id')->get();
        $n = $kriteria->count();
        $matrixInputs = $request->input('matrix', []);

        if (!is_array($matrixInputs)) {
            return back()->withInput()->with('error', 'Format matriks perbandingan tidak valid.');
        }

        $matrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $matrix[$i][$j] = 1.0;
                    continue;
                }

                if ($i < $j) {
                    if (!isset($matrixInputs[$i][$j]) || !is_numeric($matrixInputs[$i][$j])) {
                        return back()->withInput()->with('error', 'Silakan isi semua nilai perbandingan dengan angka 1 sampai 9.');
                    }

                    $value = (float) $matrixInputs[$i][$j];
                    if ($value <= 0 || $value > 9) {
                        return back()->withInput()->with('error', 'Nilai perbandingan harus berada di antara 1 dan 9.');
                    }

                    $matrix[$i][$j] = $value;
                    $matrix[$j][$i] = round(1.0 / $value, 4);
                }
            }
        }

        $columnSums = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $columnSums[$j] += $matrix[$i][$j];
            }
        }

        $normalized = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalized[$i][$j] = $matrix[$i][$j] / $columnSums[$j];
            }
        }

        $priority = [];
        for ($i = 0; $i < $n; $i++) {
            $priority[$i] = array_sum($normalized[$i]) / $n;
        }

        $aw = [];
        for ($i = 0; $i < $n; $i++) {
            $aw[$i] = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $aw[$i] += $matrix[$i][$j] * $priority[$j];
            }
        }

        $lambdaSum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            if ($priority[$i] <= 0) {
                return back()->withInput()->with('error', 'Hasil bobot tidak valid. Mohon periksa kembali nilai input.');
            }
            $lambdaSum += $aw[$i] / $priority[$i];
        }

        $lambdaMax = $lambdaSum / $n;
        $ci = ($lambdaMax - $n) / ($n - 1);
        $riValues = [1 => 0.0, 2 => 0.0, 3 => 0.58, 4 => 0.9, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49];
        $ri = $riValues[$n] ?? 1.49;
        $cr = $ri > 0 ? $ci / $ri : 0.0;

        if ($cr >= 0.1) {
            return back()->withInput()->with('error', sprintf('Consistency Ratio terlalu tinggi (CR = %.4f). Mohon periksa kembali nilai perbandingan.', $cr));
        }

        foreach ($kriteria as $index => $item) {
            $item->update(['bobot' => round($priority[$index], 4)]);
        }

        KriteriaBobotMatrix::create([
            'matrix' => $matrixInputs,
            'kriteria_ids' => $kriteria->pluck('id')->all(),
        ]);

        session(['kriteria_bobot_matrix' => $matrixInputs]);

        return redirect()->route('kriteria.index')->with('success', sprintf('Bobot AHP berhasil disimpan. CI = %.4f, CR = %.4f.', $ci, $cr));
    }
}
