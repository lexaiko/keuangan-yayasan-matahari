{{-- filepath: c:\coding\laravel\kaido\resources\views\filament\pages\keuangan-dashboard.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Dashboard Keuangan Yayasan & Koperasi
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Pantau kondisi keuangan yayasan dan koperasi secara real-time
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">
                        Update terakhir
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ now()->format('d/m/Y H:i:s') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('filament.admin.resources.pembayarans.index') }}"
                   class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Pembayaran</p>
                        <p class="text-sm text-gray-500">Kelola pembayaran siswa</p>
                    </div>
                </a>

                <a href="{{ route('filament.admin.resources.tagihans.index') }}"
                   class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Tagihan</p>
                        <p class="text-sm text-gray-500">Kelola tagihan siswa</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Widgets akan dirender di sini oleh Filament --}}
    </div>
</x-filament-panels::page>
