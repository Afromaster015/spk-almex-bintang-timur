@extends('layouts.app')

@section('title', 'Bobot Kriteria AHP - SPK')

@section('content')
    <div class="max-w-full space-y-6 rounded-3xl bg-white p-8 shadow-sm">
        <div>
            <h1 class="text-3xl font-semibold">Nilai Bobot Kriteria (AHP)</h1>
            <p class="text-gray-600 mt-2">Masukkan perbandingan berpasangan antar kriteria menggunakan skala 1-9. Nilai yang dimasukkan akan diproses untuk menghasilkan bobot prioritas kriteria.</p>

            @if(!empty($latestMatrixAt))
                <div class="mt-4 rounded-2xl bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    <p class="font-semibold">Matrix terakhir tersimpan:</p>
                    <p>Disimpan pada {{ $latestMatrixAt->isoFormat('DD MMMM YYYY HH:mm:ss') }}.</p>
                </div>
            @endif

            <div class="mt-6 rounded-3xl bg-blue-50 border border-blue-200 p-6">
                <h3 class="font-semibold text-blue-900 mb-4">Skala Perbandingan AHP</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                    <div><strong>1</strong> = Sama Penting (Equally Preferred)</div>
                    <div><strong>3</strong> = Sedikit Lebih Penting (Moderately Preferred)</div>
                    <div><strong>5</strong> = Lebih Penting (Strongly Preferred)</div>
                    <div><strong>7</strong> = Sangat Lebih Penting (Very Strongly Preferred)</div>
                    <div><strong>9</strong> = Mutlak Lebih Penting (Extremely Preferred)</div>
                    <div><strong>2,4,6,8</strong> = Nilai Tengah (Antara dua nilai utama)</div>
                </div>
            </div>
        </div>

        <form action="{{ route('kriteria.bobot.store') }}" method="POST">
            @csrf
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-gray-50 p-4">
                <table class="min-w-full table-auto divide-y divide-gray-200 text-left">
                    <thead>
                        <tr>
                            <th class="px-4 py-3"></th>
                            @foreach($kriteria as $index => $item)
                                <th class="px-4 py-3 text-sm font-medium text-gray-600">{{ $item->kode_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($kriteria as $rowIndex => $rowKriteria)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">{{ $rowKriteria->kode_kriteria }}</td>
                                @foreach($kriteria as $colIndex => $colKriteria)
                                    <td class="px-4 py-3">
                                        @if($rowIndex === $colIndex)
                                            <input type="text" value="1 - Sama" readonly class="w-full rounded-xl border border-gray-300 bg-gray-100 px-3 py-2 text-center text-sm text-gray-700">
                                        @elseif($rowIndex < $colIndex)
                                            @php
                                                $selectedValue = old('matrix.' . $rowIndex . '.' . $colIndex, $savedMatrix[$rowIndex][$colIndex] ?? '');
                                            @endphp
                                            <select
                                                name="matrix[{{ $rowIndex }}][{{ $colIndex }}]"
                                                data-i="{{ $rowIndex }}"
                                                data-j="{{ $colIndex }}"
                                                data-upper="true"
                                                class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-merah-terang focus:outline-none"
                                                required
                                            >
                                                <option value="">Pilih nilai</option>
                                                <option value="1" {{ $selectedValue === '1' ? 'selected' : '' }}>1 - Sama Penting</option>
                                                <option value="2" {{ $selectedValue === '2' ? 'selected' : '' }}>2 - Di Antara 1 dan 3</option>
                                                <option value="3" {{ $selectedValue === '3' ? 'selected' : '' }}>3 - Sedikit Lebih Penting</option>
                                                <option value="4" {{ $selectedValue === '4' ? 'selected' : '' }}>4 - Di Antara 3 dan 5</option>
                                                <option value="5" {{ $selectedValue === '5' ? 'selected' : '' }}>5 - Lebih Penting</option>
                                                <option value="6" {{ $selectedValue === '6' ? 'selected' : '' }}>6 - Di Antara 5 dan 7</option>
                                                <option value="7" {{ $selectedValue === '7' ? 'selected' : '' }}>7 - Sangat Lebih Penting</option>
                                                <option value="8" {{ $selectedValue === '8' ? 'selected' : '' }}>8 - Di Antara 7 dan 9</option>
                                                <option value="9" {{ $selectedValue === '9' ? 'selected' : '' }}>9 - Mutlak Lebih Penting</option>
                                            </select>
                                        @else
                                            <input
                                                type="text"
                                                readonly
                                                data-i="{{ $rowIndex }}"
                                                data-j="{{ $colIndex }}"
                                                data-lower="true"
                                                class="w-full rounded-xl border border-gray-300 bg-gray-100 px-3 py-2 text-center text-sm text-gray-700"
                                            >
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-4 pt-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-gray-600">
                    <p><strong>Catatan:</strong> Saat <span class="font-semibold">Consistency Ratio (CR)</span> kurang dari <span class="font-semibold">0.1</span>, bobot akan disimpan otomatis.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('kriteria.index') }}" class="rounded-md border border-gray-300 bg-white px-5 py-3 text-sm text-gray-700 hover:bg-gray-50">Kembali</a>
                    <button type="submit" class="rounded-md bg-merah-terang px-5 py-3 text-sm font-semibold text-white hover:bg-red-600">Proses Bobot</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const ahpDescriptions = {
        '1': 'Sama Penting',
        '2': 'Di Antara 1 dan 3',
        '3': 'Sedikit Lebih Penting',
        '4': 'Di Antara 3 dan 5',
        '5': 'Lebih Penting',
        '6': 'Di Antara 5 dan 7',
        '7': 'Sangat Lebih Penting',
        '8': 'Di Antara 7 dan 9',
        '9': 'Mutlak Lebih Penting'
    };

    function getClosestAhpValue(reciprocal) {
        const ahpValues = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        let closest = 1;
        let minDiff = Math.abs(reciprocal - 1);
        
        for (let val of ahpValues) {
            const diff = Math.abs(reciprocal - val);
            if (diff < minDiff) {
                minDiff = diff;
                closest = val;
            }
        }
        return closest;
    }

    function updateReciprocals() {
        document.querySelectorAll('select[data-upper]').forEach(function(select) {
            const i = select.dataset.i;
            const j = select.dataset.j;
            const value = parseFloat(select.value) || 0;
            const target = document.querySelector('input[data-lower][data-i="' + j + '"][data-j="' + i + '"]');
            if (target) {
                if (value > 0) {
                    const reciprocal = 1 / value;
                    const closestValue = getClosestAhpValue(reciprocal);
                    const description = ahpDescriptions[closestValue];
                    target.value = reciprocal.toFixed(4) + ' - ' + description;
                } else {
                    target.value = '';
                }
            }
        });
    }

    document.addEventListener('change', function(event) {
        if (event.target.matches('select[data-upper]')) {
            updateReciprocals();
        }
    });

    window.addEventListener('load', updateReciprocals);
</script>
@endpush
