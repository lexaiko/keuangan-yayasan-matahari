{{-- filepath: c:\coding\laravel\kaido\resources\views\filament\pages\siswa-dashboard.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Monitoring Data Siswa & Pembayaran
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Dashboard untuk memantau data siswa dan status pembayaran SPP bulan {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">
                        Terakhir diperbarui
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
