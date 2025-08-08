<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Selamat Datang di Sistem Pembayaran Sekolah
                    </h1>
                    <p class="text-blue-100 mt-2">
                        Dashboard utama untuk mengelola data siswa, tagihan, dan pembayaran
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-blue-100 text-sm">
                        {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="text-white text-lg font-semibold">
                        {{ now()->format('H:i') }} WIB
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Siswa Card --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Siswa</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ \App\Models\Siswa::count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Siswa Aktif Card --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Siswa Aktif</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ \App\Models\Siswa::where('status', '1')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Tagihan Card --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Tagihan</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ \App\Models\Tagihan::count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tagihan Belum Bayar Card --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Belum Bayar</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ \App\Models\Tagihan::where('status', 'belum_bayar')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('filament.admin.resources.siswas.create') }}"
                       class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Tambah Siswa</div>
                            <div class="text-sm text-gray-600">Daftarkan siswa baru</div>
                        </div>
                    </a>

                    <a href="{{ route('filament.admin.resources.tagihans.create') }}"
                       class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Buat Tagihan</div>
                            <div class="text-sm text-gray-600">Tambah tagihan baru</div>
                        </div>
                    </a>

                    <a href="{{ route('filament.admin.resources.pembayarans.create') }}"
                       class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Proses Pembayaran</div>
                            <div class="text-sm text-gray-600">Catat pembayaran siswa</div>
                        </div>
                    </a>

                    <a href="/admin/generate-tagihan"
                       class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Generate Tagihan</div>
                            <div class="text-sm text-gray-600">Buat tagihan massal</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Payments --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Pembayaran Terbaru</h3>
                </div>
                <div class="p-6">
                    @php
                        $recentPayments = \App\Models\Pembayaran::with('siswa')
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($recentPayments->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentPayments as $payment)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $payment->siswa->nama }}</div>
                                            <div class="text-sm text-gray-600">{{ $payment->tanggal_bayar }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">
                                            Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-green-600">Lunas</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('filament.admin.resources.pembayarans.index') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua Pembayaran →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500">Belum ada pembayaran</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tagihan Mendekati Jatuh Tempo --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tagihan Mendekati Jatuh Tempo</h3>
                </div>
                <div class="p-6">
                    @php
                        $upcomingBills = \App\Models\Tagihan::with(['siswa', 'jenisPembayaran'])
                            ->where('status', '!=', 'lunas')
                            ->where('tanggal_jatuh_tempo', '>=', now())
                            ->where('tanggal_jatuh_tempo', '<=', now()->addDays(7))
                            ->orderBy('tanggal_jatuh_tempo')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($upcomingBills->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingBills as $bill)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $bill->siswa->nama }}</div>
                                            <div class="text-sm text-gray-600">{{ $bill->jenisPembayaran->nama_pembayaran }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">
                                            Rp {{ number_format($bill->jumlah, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-yellow-600">
                                            {{ \Carbon\Carbon::parse($bill->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('filament.admin.resources.tagihans.index') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua Tagihan →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500">Tidak ada tagihan mendekati jatuh tempo</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer Info --}}
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="text-center">
                <div class="text-gray-600 text-sm">
                    Sistem Pembayaran Sekolah - Yayasan Matahari
                </div>
                <div class="text-gray-500 text-xs mt-1">
                    Kelola pembayaran siswa dengan mudah dan efisien
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
