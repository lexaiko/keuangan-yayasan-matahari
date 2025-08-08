{{-- filepath: c:\coding\laravel\kaido\resources\views\filament\pages\ubah-tahun-akademik-masal.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Ubah Tahun Akademik Secara Masal
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Fitur ini memungkinkan Anda untuk mengubah tahun akademik beberapa kelas sekaligus.
                        Sangat berguna untuk transisi tahun ajaran baru.
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">
                        Tahun Akademik Aktif
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ \App\Models\TahunAkademik::where('is_active', true)->first()?->nama ?? 'Belum diset' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-lg shadow">
            {{ $this->form }}

            <div class="px-6 py-4 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <p><strong>Petunjuk:</strong></p>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li>Pilih tahun akademik saat ini untuk melihat daftar kelas</li>
                            <li>Gunakan filter tingkat untuk mempermudah pencarian (opsional)</li>
                            <li>Pilih kelas yang akan dipindah ke tahun akademik baru</li>
                            <li>Tentukan tahun akademik tujuan</li>
                            <li>Klik "Ubah Tahun Akademik" untuk memproses</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 flex space-x-4">
                    {{ ($this->getFormActions())['ubah'] ?? '' }}
                </div>
            </div>
        </div>

        {{-- Warning Alert --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Peringatan Penting
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Perubahan tahun akademik akan mempengaruhi semua siswa di kelas yang dipilih</li>
                            <li>Pastikan tahun akademik tujuan sudah benar sebelum melakukan perubahan</li>
                            <li>Proses ini tidak dapat dibatalkan setelah dikonfirmasi</li>
                            <li>Disarankan untuk melakukan backup data sebelum melakukan perubahan masal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
