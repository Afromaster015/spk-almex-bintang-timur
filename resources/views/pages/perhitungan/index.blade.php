@extends('layouts.app')

@section('title', 'Perhitungan SAW - SPK')

@section('content')
    <div class="space-y-8">
        <div class="rounded-3xl bg-white p-8 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-4xl font-semibold">Perhitungan & Perankingan</h1>
                    <p class="text-gray-600 mt-2">Lihat hasil perhitungan AHP dan perankingan SAW dengan detail lengkap.</p>
                </div>
            </div>

            @if($kriterias->isEmpty() || $alternatifs->isEmpty())
                <div class="mt-8 rounded-3xl bg-yellow-50 border border-yellow-200 p-6 text-yellow-800">
                    <p class="font-semibold">Data belum lengkap.</p>
                    <p class="mt-2">Tambahkan minimal satu kriteria dan satu alternatif sebelum menjalankan perhitungan.</p>
                </div>
            @elseif($criteriaWeightsIncomplete)
                <div class="mt-8 rounded-3xl bg-red-50 border border-red-200 p-6 text-red-800">
                    <p class="font-semibold">Bobot AHP belum tersedia.</p>
                    <p class="mt-2">Hitung bobot kriteria terlebih dahulu di modul Nilai Bobot Kriteria.</p>
                </div>
            @elseif($missingData)
                <div class="mt-8 rounded-3xl bg-orange-50 border border-orange-200 p-6 text-orange-800">
                    <p class="font-semibold">Data nilai alternatif tidak lengkap.</p>
                    <p class="mt-2 mb-4">Isi semua nilai pelanggan untuk setiap kriteria di menu Nilai Bobot Alternatif.</p>
                    
                    @if(!empty($missingDataDetails))
                        <div class="mt-4 rounded-lg bg-white text-gray-800 p-4">
                            <p class="font-semibold text-sm mb-3">Data yang masih kosong:</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-orange-200 text-sm">
                                    <thead class="bg-orange-100">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium">Pelanggan (Alternatif)</th>
                                            <th class="px-3 py-2 text-left font-medium">Kriteria</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-orange-200">
                                        @foreach($missingDataDetails as $missing)
                                            <tr class="bg-orange-50">
                                                <td class="px-3 py-2">{{ $missing['alternatif'] }}</td>
                                                <td class="px-3 py-2">{{ $missing['kriteria'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('nilai-alternatif.index') }}" class="inline-flex items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">
                                    Isi di Menu Nilai Bobot Alternatif
                                </a>
                                <form action="{{ route('perhitungan.create-missing-values') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                        Buat Record Otomatis (Nilai Default 1)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-8 space-y-8">
                    <!-- Container 1: Menentukan Bobot dengan AHP -->
                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-2xl font-semibold mb-6">Menentukan Bobot dengan AHP</h2>
                        
                        <!-- Buttons Container -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <button onclick="showAhpTab('matriks-perbandingan')" 
                                    id="btn-matriks-perbandingan"
                                    class="px-4 py-2 rounded-xl bg-merah-gelap text-white font-medium hover:bg-opacity-90 transition ahp-btn active-tab">
                                Matriks Perbandingan Kriteria
                            </button>
                            <button onclick="showAhpTab('matriks-bobot')" 
                                    id="btn-matriks-bobot"
                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition ahp-btn">
                                Matriks Bobot Prioritas Kriteria
                            </button>
                            <button onclick="showAhpTab('konsistensi')" 
                                    id="btn-konsistensi"
                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition ahp-btn">
                                Konsistensi Kriteria
                            </button>
                        </div>

                        <!-- Table Container -->
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <!-- Matriks Perbandingan Kriteria Tab -->
                            <div id="matriks-perbandingan" class="ahp-tab active">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Kode</th>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Nama Kriteria</th>
                                                @foreach($kriterias as $kriteria)
                                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">{{ $kriteria->kode_kriteria }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($kriterias as $rowIndex => $kriteria)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">{{ $kriteria->kode_kriteria }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $kriteria->nama_kriteria }}</td>
                                                    @foreach($kriterias as $colIndex => $k)
                                                        @php
                                                            $value = null;
                                                            if ($rowIndex === $colIndex) {
                                                                $value = 1.0;
                                                            } elseif ($rowIndex < $colIndex) {
                                                                $value = $comparisonMatrix[$rowIndex][$colIndex] ?? null;
                                                            } else {
                                                                $upperValue = $comparisonMatrix[$colIndex][$rowIndex] ?? null;
                                                                $value = $upperValue ? round(1 / (float) $upperValue, 4) : null;
                                                            }
                                                        @endphp
                                                        <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                                            {{ $value !== null ? number_format((float) $value, 4) : '–' }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            @php
                                                $colTotals = [];
                                                for ($j = 0; $j < count($kriterias); $j++) {
                                                    $colTotals[$j] = 0;
                                                }
                                                foreach ($kriterias as $rowIndex => $kriteria) {
                                                    foreach ($kriterias as $colIndex => $k) {
                                                        if ($rowIndex === $colIndex) {
                                                            $colTotals[$colIndex] += 1.0;
                                                        } elseif ($rowIndex < $colIndex) {
                                                            $value = $comparisonMatrix[$rowIndex][$colIndex] ?? 0;
                                                            $colTotals[$colIndex] += (float) $value;
                                                        } else {
                                                            $upperValue = $comparisonMatrix[$colIndex][$rowIndex] ?? 0;
                                                            $colTotals[$colIndex] += $upperValue ? round(1 / (float) $upperValue, 4) : 0;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-semibold text-merah-gelap text-center" colspan="2">Total</td>
                                                @foreach($colTotals as $total)
                                                    <td class="px-4 py-3 text-sm font-semibold text-merah-gelap text-center">{{ number_format($total, 4) }}</td>
                                                @endforeach
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <p class="text-sm text-gray-500 mt-3">Matriks perbandingan berpasangan untuk menentukan prioritas kriteria menggunakan metode AHP.</p>
                            </div>

                            <!-- Matriks Bobot Prioritas Kriteria Tab -->
                            <div id="matriks-bobot" class="ahp-tab hidden">
                                @if(empty($ahpNormalizedMatrix))
                                    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4 mb-4">
                                        <p class="text-sm text-yellow-800 font-semibold">⚠ Data matriks perbandingan belum tersimpan.</p>
                                        <p class="text-sm text-yellow-700 mt-1">Lakukan perhitungan bobot terlebih dahulu di menu <strong>Matriks Perbandingan Kriteria</strong> dengan klik "Proses Bobot".</p>
                                    </div>
                                @endif
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700 text-center">Kode</th>
                                                @foreach($kriterias as $kriteria)
                                                    <th class="px-4 py-3 text-sm font-medium text-gray-700 text-center">{{ $kriteria->kode_kriteria }}</th>
                                                @endforeach
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700 text-center">Prioritas/<br>Bobot</th>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700 text-center">Consistency<br>Measure</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($kriterias as $rowIndex => $kriteria)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">{{ $kriteria->kode_kriteria }}</td>
                                                    @foreach($kriterias as $colIndex => $k)
                                                        <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                                            {{ isset($ahpNormalizedMatrix[$rowIndex][$colIndex]) ? number_format($ahpNormalizedMatrix[$rowIndex][$colIndex], 4) : '–' }}
                                                        </td>
                                                    @endforeach
                                                    <td class="px-4 py-3 text-sm text-merah-gelap font-semibold text-center">{{ number_format($kriteria->bobot, 4) }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                                        {{ isset($consistencyMeasures[$rowIndex]) ? number_format($consistencyMeasures[$rowIndex], 4) : '–' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-sm text-gray-500 mt-3">Matriks normalisasi prioritas kriteria hasil perhitungan AHP. Setiap kolom dijumlahkan menjadi 1.0 dengan consistency measure untuk validasi.</p>
                            </div>

                            <!-- Konsistensi Kriteria Tab -->
                            <div id="konsistensi" class="ahp-tab hidden">
                                <!-- Tabel Random Consistency Index -->
                                <div class="mb-6 overflow-x-auto">
                                    <h3 class="text-lg font-semibold mb-3">Tabel Random Consistency Index (RI)</h3>
                                    <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700 text-center">Matrix Size</th>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700 text-center">Random Consistency Index (RI)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @for($i = 1; $i <= 10; $i++)
                                                <tr @if($i === $n) class="bg-merah-gelap text-white font-bold" @endif>
                                                    <td class="px-4 py-3 text-sm text-center">{{ $i }}</td>
                                                    <td class="px-4 py-3 text-sm text-center">{{ number_format($randomConsistencyIndices[$i] ?? 0, 2) }}</td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Hasil Perhitungan -->
                                <div class="rounded-lg bg-white p-4 mb-4 border border-gray-200">
                                    <h3 class="text-lg font-semibold mb-4">Hasil Perhitungan Konsistensi</h3>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4">
                                            <p class="text-sm text-emerald-700 font-medium">Consistency Index (CI)</p>
                                            <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ number_format($consistencyIndex, 4) }}</p>
                                        </div>
                                        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                                            <p class="text-sm text-blue-700 font-medium">Ratio Index (RI)</p>
                                            <p class="mt-2 text-2xl font-semibold text-blue-900">{{ number_format($ratioIndex, 2) }}</p>
                                        </div>
                                        <div class="rounded-lg @if($isConsistent) bg-emerald-50 border border-emerald-200 @else bg-red-50 border border-red-200 @endif p-4">
                                            <p class="text-sm @if($isConsistent) text-emerald-700 @else text-red-700 @endif font-medium">Consistency Ratio (CR)</p>
                                            <p class="mt-2 text-2xl font-semibold @if($isConsistent) text-emerald-900 @else text-red-900 @endif">{{ number_format($consistencyRatio, 4) }}</p>
                                            <p class="mt-3 text-xs font-semibold @if($isConsistent) text-emerald-700 bg-emerald-100 @else text-red-700 bg-red-100 @endif rounded px-2 py-1 inline-block">
                                                @if($isConsistent) ✓ Konsisten @else ✗ Tidak Konsisten @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-lg bg-gray-100 p-4 text-sm text-gray-700">
                                    <p><strong>Keterangan:</strong> Konsistensi rasio yang baik adalah CR ≤ 0.1. Jika CR > 0.1, maka penilaian perlu ditinjau kembali.</p>
                                </div>
                                <p class="text-sm text-gray-500 mt-3">Tingkat konsistensi dari matriks perbandingan AHP berdasarkan perhitungan Consistency Index dibagi Ratio Index.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Container 2: Menentukan Ranking dengan SAW -->
                    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-2xl font-semibold mb-6">Menentukan Ranking dengan SAW</h2>
                        
                        <!-- Buttons Container -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <button onclick="showSawTab('alternatif-kriteria')" 
                                    id="btn-alternatif-kriteria"
                                    class="px-4 py-2 rounded-xl bg-merah-gelap text-white font-medium hover:bg-opacity-90 transition saw-btn active-tab">
                                Alternatif Kriteria
                            </button>
                            <button onclick="showSawTab('normalisasi')" 
                                    id="btn-normalisasi"
                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition saw-btn">
                                Normalisasi Matriks
                            </button>
                            <button onclick="showSawTab('terbobot')" 
                                    id="btn-terbobot"
                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition saw-btn">
                                Terbobot
                            </button>
                            <button onclick="showSawTab('perankingan')" 
                                    id="btn-perankingan"
                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition saw-btn">
                                Perankingan
                            </button>
                        </div>

                        <!-- Table Container -->
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <!-- Alternatif Kriteria Tab -->
                            <div id="alternatif-kriteria" class="saw-tab active">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Alternatif</th>
                                                @foreach($kriterias as $kriteria)
                                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">{{ $kriteria->kode_kriteria }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($alternatifs as $alternatif)
                                                <tr>
                                                    <td class="px-4 py-4 text-sm font-semibold text-gray-700 text-center">{{ $alternatif->nama_pelanggan }}</td>
                                                    @foreach($kriterias as $kriteria)
                                                        <td class="px-4 py-4 text-sm text-gray-700 text-center">{{ number_format($rawMatrix[$alternatif->id][$kriteria->id], 2) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-sm text-gray-500 mt-3">Matriks nilai alternatif (X) untuk semua kriteria.</p>
                            </div>

                            <!-- Normalisasi Matriks Tab -->
                            <div id="normalisasi" class="saw-tab hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Alternatif</th>
                                                @foreach($kriterias as $kriteria)
                                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">{{ $kriteria->kode_kriteria }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($alternatifs as $alternatif)
                                                <tr>
                                                    <td class="px-4 py-4 text-sm font-semibold text-gray-700 text-center">{{ $alternatif->nama_pelanggan }}</td>
                                                    @foreach($kriterias as $kriteria)
                                                        <td class="px-4 py-4 text-sm text-gray-700 text-center">{{ number_format($normalizedMatrix[$alternatif->id][$kriteria->id], 4) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-sm text-gray-500 mt-3">Matriks normalisasi (R) hasil normalisasi nilai alternatif.</p>
                            </div>

                            <!-- Terbobot Tab -->
                            <div id="terbobot" class="saw-tab hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Alternatif</th>
                                                @foreach($kriterias as $kriteria)
                                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">{{ $kriteria->kode_kriteria }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($alternatifs as $alternatif)
                                                <tr>
                                                    <td class="px-4 py-4 text-sm font-semibold text-gray-700 text-center">{{ $alternatif->nama_pelanggan }}</td>
                                                    @foreach($kriterias as $kriteria)
                                                        <td class="px-4 py-4 text-sm text-gray-700 text-center">{{ number_format($weightedMatrix[$alternatif->id][$kriteria->id], 4) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-sm text-gray-500 mt-3">Matriks terbobot (V) hasil perkalian normalisasi dengan bobot kriteria.</p>
                            </div>

                            <!-- Perankingan Tab -->
                            <div id="perankingan" class="saw-tab hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Peringkat</th>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Pelanggan</th>
                                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Nilai Akhir (V)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($rankings as $index => $item)
                                                <tr class="{{ $index === 0 ? 'bg-emerald-50' : ($index === 1 ? 'bg-yellow-50' : ($index === 2 ? 'bg-sky-50' : '')) }}">
                                                    <td class="px-4 py-4 text-sm font-semibold text-gray-700 text-center">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-4 text-sm text-gray-700 text-center">{{ $item['alternatif']->nama_pelanggan }}</td>
                                                    <td class="px-4 py-4 text-sm font-semibold text-merah-gelap text-center">{{ number_format($item['score'], 4) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 rounded-lg bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                                    <p><strong>Rekomendasi:</strong> Alternatif dengan peringkat teratas menunjukkan pelanggan yang paling layak mendapatkan diskon.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showAhpTab(tabName) {
            // Hide all AHP tabs
            const ahpTabs = document.querySelectorAll('.ahp-tab');
            ahpTabs.forEach(tab => tab.classList.add('hidden'));
            
            // Remove active class from all AHP buttons
            const ahpBtns = document.querySelectorAll('.ahp-btn');
            ahpBtns.forEach(btn => {
                btn.classList.remove('active-tab');
                btn.classList.remove('bg-merah-gelap', 'text-white');
                btn.classList.add('bg-gray-300', 'text-gray-700');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.remove('hidden');
            
            // Add active class to selected button
            document.getElementById('btn-' + tabName).classList.add('active-tab');
            document.getElementById('btn-' + tabName).classList.remove('bg-gray-300', 'text-gray-700');
            document.getElementById('btn-' + tabName).classList.add('bg-merah-gelap', 'text-white');
        }

        function showSawTab(tabName) {
            // Hide all SAW tabs
            const sawTabs = document.querySelectorAll('.saw-tab');
            sawTabs.forEach(tab => tab.classList.add('hidden'));
            
            // Remove active class from all SAW buttons
            const sawBtns = document.querySelectorAll('.saw-btn');
            sawBtns.forEach(btn => {
                btn.classList.remove('active-tab');
                btn.classList.remove('bg-merah-gelap', 'text-white');
                btn.classList.add('bg-gray-300', 'text-gray-700');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.remove('hidden');
            
            // Add active class to selected button
            document.getElementById('btn-' + tabName).classList.add('active-tab');
            document.getElementById('btn-' + tabName).classList.remove('bg-gray-300', 'text-gray-700');
            document.getElementById('btn-' + tabName).classList.add('bg-merah-gelap', 'text-white');
        }
    </script>
@endsection
