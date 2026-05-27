<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaBobotMatrix;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;

class PerhitunganController extends Controller
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
        $nilaiAlternatif = NilaiAlternatif::all();
        $comparisonMatrix = [];
        $ahpNormalizedMatrix = [];
        $consistencyMeasures = [];
        $consistencyIndex = 0;
        $ratioIndex = 0;
        $consistencyRatio = 0;
        $isConsistent = false;
        $randomConsistencyIndices = [
            1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12,
            6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
        ];

        $savedMatrix = KriteriaBobotMatrix::latest()->first();
        $n = count($kriterias);
        $kriteria_ids = $kriterias->pluck('id')->toArray();
        
        // Cek apakah data matrix tersimpan
        if ($savedMatrix) {
            $matrixData = $savedMatrix->matrix ?? [];
            $savedKriteriaIds = $savedMatrix->kriteria_ids ?? [];
            
            // Jika kriteria_ids cocok, rekonstruksi matriks lengkap
            if (is_array($matrixData) && is_array($savedKriteriaIds) && $savedKriteriaIds === $kriteria_ids && $n > 0) {
                // Inisialisasi matriks lengkap dengan 0
                for ($i = 0; $i < $n; $i++) {
                    for ($j = 0; $j < $n; $j++) {
                        $comparisonMatrix[$i][$j] = 0.0;
                    }
                }
                
                // Isi diagonal dengan 1.0
                for ($i = 0; $i < $n; $i++) {
                    $comparisonMatrix[$i][$i] = 1.0;
                }
                
                // Isi upper triangle dari sparse data
                foreach ($matrixData as $i => $row) {
                    if (is_array($row)) {
                        foreach ($row as $j => $value) {
                            if ($i < $j) {
                                $comparisonMatrix[$i][$j] = (float) $value;
                            }
                        }
                    }
                }
                
                // Isi lower triangle sebagai reciprocal
                for ($i = 0; $i < $n; $i++) {
                    for ($j = 0; $j < $i; $j++) {
                        $upperValue = $comparisonMatrix[$j][$i];
                        $comparisonMatrix[$i][$j] = $upperValue > 0 ? round(1.0 / $upperValue, 4) : 1.0;
                    }
                }
            }
        }
        
        // Jika ada comparison matrix, hitung normalisasi
        if (!empty($comparisonMatrix) && $n > 0) {
            // Hitung column sums
            $columnSums = array_fill(0, $n, 0.0);
            for ($j = 0; $j < $n; $j++) {
                for ($i = 0; $i < $n; $i++) {
                    $columnSums[$j] += (float) ($comparisonMatrix[$i][$j] ?? 1.0);
                }
            }
            
            // Hitung normalized matrix
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $value = (float) ($comparisonMatrix[$i][$j] ?? 1.0);
                    $ahpNormalizedMatrix[$i][$j] = $columnSums[$j] > 0 ? $value / $columnSums[$j] : 0;
                }
            }
            
            // Hitung consistency measure untuk setiap elemen (lambda)
            for ($i = 0; $i < $n; $i++) {
                $priority = (float) ($kriterias[$i]->bobot ?? 0.25);
                $weightedSum = 0.0;
                for ($j = 0; $j < $n; $j++) {
                    $weightedSum += (float) ($comparisonMatrix[$i][$j] ?? 1.0) * (float) ($kriterias[$j]->bobot ?? 0.25);
                }
                $consistencyMeasures[$i] = $priority > 0 ? round($weightedSum / $priority, 4) : 0;
            }
            
            // Hitung Consistency Index (CI)
            $lambdaMax = count($consistencyMeasures) > 0 ? array_sum($consistencyMeasures) / count($consistencyMeasures) : 0;
            $consistencyIndex = $n > 1 ? round(($lambdaMax - $n) / ($n - 1), 4) : 0;
            
            // Random Consistency Index (RI) berdasarkan matrix size
            $randomConsistencyIndices = [
                1 => 0.00,
                2 => 0.00,
                3 => 0.58,
                4 => 0.90,
                5 => 1.12,
                6 => 1.24,
                7 => 1.32,
                8 => 1.41,
                9 => 1.45,
                10 => 1.49,
            ];
            
            $ratioIndex = $randomConsistencyIndices[$n] ?? 0;
            
            // Consistency Ratio (CR)
            $consistencyRatio = $ratioIndex > 0 ? round($consistencyIndex / $ratioIndex, 4) : 0;
            $isConsistent = $consistencyRatio <= 0.1;
        }

        $rawMatrix = [];
        $missingData = false;
        $criteriaWeightsIncomplete = false;

        // Initialize matrix
        foreach ($alternatifs as $alternatif) {
            foreach ($kriterias as $kriteria) {
                $rawMatrix[$alternatif->id][$kriteria->id] = null;
            }
        }

        // Fill matrix with existing values
        foreach ($nilaiAlternatif as $nilai) {
            if (array_key_exists($nilai->alternatif_id, $rawMatrix)
                && array_key_exists($nilai->kriteria_id, $rawMatrix[$nilai->alternatif_id])) {
                $rawMatrix[$nilai->alternatif_id][$nilai->kriteria_id] = $nilai->nilai;
            }
        }

        foreach ($kriterias as $kriteria) {
            if ($kriteria->bobot === null) {
                $criteriaWeightsIncomplete = true;
                break;
            }
        }

        $rankings = [];
        $normalizedMatrix = [];
        $weightedMatrix = [];
        $missingDataDetails = [];

        if ($kriterias->isNotEmpty() && $alternatifs->isNotEmpty() && !$criteriaWeightsIncomplete) {
            foreach ($alternatifs as $alternatif) {
                foreach ($kriterias as $kriteria) {
                    if ($rawMatrix[$alternatif->id][$kriteria->id] === null) {
                        $missingData = true;
                        $missingDataDetails[] = [
                            'alternatif' => $alternatif->nama_pelanggan,
                            'kriteria' => $kriteria->nama_kriteria
                        ];
                    }
                }
            }

            // Cek apakah ada kombinasi yang tidak memiliki record sama sekali (mungkin kriteria ditambah setelah alternatif dibuat)
            if (!$missingData && $kriterias->count() > 0 && $alternatifs->count() > 0) {
                $expectedCount = $kriterias->count() * $alternatifs->count();
                $actualCount = $nilaiAlternatif->count();
                
                if ($actualCount < $expectedCount) {
                    $missingData = true;
                    // Temukan kombinasi yang hilang
                    foreach ($alternatifs as $alternatif) {
                        foreach ($kriterias as $kriteria) {
                            $exists = $nilaiAlternatif->where('alternatif_id', $alternatif->id)
                                                     ->where('kriteria_id', $kriteria->id)
                                                     ->first();
                            if (!$exists) {
                                $missingDataDetails[] = [
                                    'alternatif' => $alternatif->nama_pelanggan,
                                    'kriteria' => $kriteria->nama_kriteria,
                                    'status' => 'tidak ada record'
                                ];
                            }
                        }
                    }
                }
            }

            if (!$missingData) {
                $criterionStats = [];
                foreach ($kriterias as $kriteria) {
                    $values = [];
                    foreach ($alternatifs as $alternatif) {
                        $values[] = $rawMatrix[$alternatif->id][$kriteria->id];
                    }

                    $criterionStats[$kriteria->id] = [
                        'max' => max($values),
                        'min' => min($values),
                    ];
                }

                foreach ($alternatifs as $alternatif) {
                    $score = 0.0;
                    foreach ($kriterias as $kriteria) {
                        $value = $rawMatrix[$alternatif->id][$kriteria->id];
                        $stats = $criterionStats[$kriteria->id];
                        $normalized = 0.0;

                        if ($kriteria->jenis === 'benefit') {
                            $normalized = $stats['max'] > 0 ? $value / $stats['max'] : 0.0;
                        } else {
                            $normalized = $value > 0 ? $stats['min'] / $value : 0.0;
                        }

                        $normalized = round($normalized, 4);
                        $weighted = round($normalized * $kriteria->bobot, 4);

                        $normalizedMatrix[$alternatif->id][$kriteria->id] = $normalized;
                        $weightedMatrix[$alternatif->id][$kriteria->id] = $weighted;
                        $score += $weighted;
                    }

                    $rankings[] = [
                        'alternatif' => $alternatif,
                        'score' => round($score, 4),
                    ];
                }

                usort($rankings, fn($a, $b) => $b['score'] <=> $a['score']);
            }
        }

        return view('pages.perhitungan.index', compact(
            'kriterias',
            'alternatifs',
            'rawMatrix',
            'normalizedMatrix',
            'weightedMatrix',
            'rankings',
            'missingData',
            'missingDataDetails',
            'criteriaWeightsIncomplete',
            'comparisonMatrix',
            'ahpNormalizedMatrix',
            'consistencyMeasures',
            'consistencyIndex',
            'ratioIndex',
            'consistencyRatio',
            'isConsistent',
            'randomConsistencyIndices',
            'n'
        ));
    }

    public function createMissingValues()
    {
        if ($redirect = $this->ensureLoggedIn()) {
            return $redirect;
        }

        $kriterias = Kriteria::orderBy('id')->get();
        $alternatifs = Alternatif::orderBy('id')->get();
        $nilaiAlternatif = NilaiAlternatif::all();
        $createdCount = 0;

        // Buat missing NilaiAlternatif records dengan nilai default 1
        foreach ($alternatifs as $alternatif) {
            foreach ($kriterias as $kriteria) {
                $exists = $nilaiAlternatif->where('alternatif_id', $alternatif->id)
                                          ->where('kriteria_id', $kriteria->id)
                                          ->first();
                if (!$exists) {
                    NilaiAlternatif::create([
                        'alternatif_id' => $alternatif->id,
                        'kriteria_id' => $kriteria->id,
                        'nilai' => 1, // Default value
                    ]);
                    $createdCount++;
                }
            }
        }

        return back()->with('success', "Berhasil membuat $createdCount record nilai alternatif yang hilang. Silakan edit nilai-nilai tersebut sesuai kebutuhan.");
    }
}

