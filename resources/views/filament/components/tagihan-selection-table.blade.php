{{-- filepath: c:\coding\laravel\kaido\resources\views\filament\components\tagihan-selection-table.blade.php --}}
@php
    use App\Models\Tagihan;

    $tagihans = collect();

    if ($siswa_id) {
        $tagihans = Tagihan::where('siswa_id', $siswa_id)
            ->where('status', '!=', Tagihan::STATUS_LUNAS)
            ->whereNotIn('id', $selected_ids)
            ->with('jenisPembayaran')
            ->orderBy('tanggal_jatuh_tempo')
            ->get();
    }
@endphp

<div class="w-full">
    @if($tagihans->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-4 py-2 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300">
                        </th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Jenis Pembayaran</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Bulan</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total Tagihan</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Sudah Dibayar</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Sisa</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihans as $tagihan)
                        @php
                            $totalDibayar = $tagihan->detailPembayarans()->sum('jumlah_bayar');
                            $sisaTagihan = $tagihan->jumlah - $totalDibayar;
                            $statusBadgeClass = match($tagihan->status) {
                                'belum_bayar' => 'bg-red-100 text-red-800',
                                'sebagian' => 'bg-yellow-100 text-yellow-800',
                                'lunas' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $statusText = match($tagihan->status) {
                                'belum_bayar' => 'Belum Bayar',
                                'sebagian' => 'Sebagian',
                                'lunas' => 'Lunas',
                                default => ucfirst($tagihan->status)
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">
                                <input
                                    type="checkbox"
                                    name="tagihan_selection[]"
                                    value="{{ $tagihan->id }}"
                                    class="tagihan-checkbox rounded border-gray-300"
                                    data-tagihan-id="{{ $tagihan->id }}"
                                >
                            </td>
                            <td class="border border-gray-300 px-4 py-2 font-medium">
                                {{ $tagihan->jenisPembayaran->nama_pembayaran ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $tagihan->bulan ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">
                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusBadgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>Petunjuk:</strong> Pilih tagihan yang ingin dibayar dengan mencentang checkbox.
                Anda dapat memilih beberapa tagihan sekaligus.
            </p>
            <p class="text-xs text-blue-600 mt-1">
                Total {{ $tagihans->count() }} tagihan tersedia untuk siswa ini.
            </p>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-500">
                @if($siswa_id)
                    @if(count($selected_ids) > 0)
                        <p>✅ Semua tagihan sudah dipilih</p>
                        <p class="text-sm mt-1">{{ count($selected_ids) }} tagihan sudah ditambahkan ke pembayaran</p>
                    @else
                        <p>Tidak ada tagihan yang perlu dibayar untuk siswa ini</p>
                    @endif
                @else
                    <p>Pilih siswa terlebih dahulu</p>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- filepath: c:\coding\laravel\kaido\resources\views\filament\components\tagihan-selection-table.blade.php --}}
{{-- ...existing code... --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for the modal to be fully loaded
    setTimeout(function() {
        initializeTagihanSelection();
    }, 100);
});

function initializeTagihanSelection() {
    // Handle select all checkbox
    const selectAllCheckbox = document.getElementById('select-all');
    const tagihanCheckboxes = document.querySelectorAll('.tagihan-checkbox');

    if (selectAllCheckbox && tagihanCheckboxes.length > 0) {
        selectAllCheckbox.addEventListener('change', function() {
            tagihanCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedTagihan();
        });

        // Handle individual checkbox changes
        tagihanCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Update select all checkbox state
                const checkedCount = document.querySelectorAll('.tagihan-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === tagihanCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < tagihanCheckboxes.length;

                updateSelectedTagihan();
            });
        });
    }

    function updateSelectedTagihan() {
        const selectedIds = Array.from(document.querySelectorAll('.tagihan-checkbox:checked'))
            .map(checkbox => parseInt(checkbox.value));

        console.log('Selected IDs:', selectedIds); // Debug log

        // Find the hidden field - try multiple selectors
        let hiddenField = document.querySelector('input[name="selected_tagihan_table"]');
        if (!hiddenField) {
            hiddenField = document.querySelector('input[id*="selected_tagihan_table"]');
        }
        if (!hiddenField) {
            hiddenField = document.querySelector('input[data-key="selected_tagihan_table"]');
        }

        console.log('Hidden field found:', hiddenField); // Debug log

        if (hiddenField) {
            hiddenField.value = JSON.stringify(selectedIds);

            // Trigger multiple events to ensure Filament picks up the change
            hiddenField.dispatchEvent(new Event('input', { bubbles: true }));
            hiddenField.dispatchEvent(new Event('change', { bubbles: true }));

            // Also try triggering Alpine.js events if available
            if (window.Alpine) {
                hiddenField.dispatchEvent(new CustomEvent('input', { bubbles: true }));
            }

            console.log('Updated hidden field value:', hiddenField.value); // Debug log
        } else {
            console.error('Hidden field not found!'); // Debug log
        }
    }
}
</script>
