<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Filter Form -->
        <x-filament::card>
            <div class="">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Filter & Pengaturan Laporan</h3>
                </div>
                {{ $this->form }}
            </div>
        </x-filament::card>


        <!-- Detail Table -->
        <x-filament::card>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi Keuangan</h3>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total {{ $this->getFinancialData()->count() }} transaksi
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dari</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Tanggal</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Debit</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kredit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($this->getFinancialData() as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-600 font-medium">
                                    {{ $item->no }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $item->dari }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right {{ $item->debit > 0 ? 'text-green-600' : 'text-gray-300' }}">
                                    {{ $item->debit > 0 ? 'Rp ' . number_format($item->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right {{ $item->kredit > 0 ? 'text-red-600' : 'text-gray-300' }}">
                                    {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $item->keterangan }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada transaksi</p>
                                            <p class="text-xs text-gray-500">Tidak ada transaksi dalam periode yang dipilih</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($this->getFinancialData()->count() > 0)
                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                            <tr class="font-bold text-gray-900">
                                <td colspan="3" class="px-6 py-4 text-center uppercase tracking-wide">Total</td>
                                <td class="px-4 py-4 text-right text-green-600 font-bold">
                                    Rp {{ number_format($this->getTotalDebit(), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-red-600 font-bold">
                                    Rp {{ number_format($this->getTotalKredit(), 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4"></td>
                            </tr>
                            <tr class="font-bold bg-blue-50 border-t border-blue-200">
                                <td colspan="5" class="px-6 py-4 text-center uppercase tracking-wide text-blue-900">Saldo Akhir</td>
                                <td class="px-6 py-4 text-right font-bold {{ $this->getSaldoAkhir() >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    Rp {{ number_format($this->getSaldoAkhir(), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
