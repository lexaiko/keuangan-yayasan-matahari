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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                <a href="{{ route('filament.admin.resources.gaji-pegawais.index') }}"
                   class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Gaji Pegawai</p>
                        <p class="text-sm text-gray-500">Kelola gaji pegawai</p>
                    </div>
                </a>

                <a href="{{ route('filament.admin.resources.saldo-koperasis.index') }}"
                   class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Koperasi</p>
                        <p class="text-sm text-gray-500">Kelola saldo koperasi</p>
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
