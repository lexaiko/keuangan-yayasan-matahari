<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use Filament\Pages\Page;
use App\Models\TahunAkademik;
use App\Models\JenisPembayaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;

class GenerateTagihan extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Tagihan';

    protected static string $view = 'filament.pages.generate-tagihan';

    public array $data = [];

    protected function getFormStatePath(): string
    {
        return 'data';
    }



    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Informasi Kelas')
                ->schema([
                    Forms\Components\Select::make('kelas')
                        ->label('Kelas')
                        ->options(Kelas::all()->pluck('nama', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Reset repeater when class changes
                            $set('tagihan_items', []);
                        }),
                ])
                ->columns(1),

            Forms\Components\Section::make('Daftar Tagihan')
                ->schema([
                    Forms\Components\Repeater::make('tagihan_items')
                        ->label('Tagihan yang akan digenerate')
                        ->schema([
                            Select::make('jenis_pembayaran_id')
                                ->label('Jenis Pembayaran')
                                ->options(JenisPembayaran::all()->pluck('nama_pembayaran', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $jenis = JenisPembayaran::find($state);

                                    $set('jumlah', $jenis?->nominal ?? 0);

                                    if ($jenis?->tipe === 'sekali') {
                                        $set('bulan', null);
                                    }
                                }),

                            Select::make('bulan')
                                ->options([
                                    'Januari' => 'Januari',
                                    'Februari' => 'Februari',
                                    'Maret' => 'Maret',
                                    'April' => 'April',
                                    'Mei' => 'Mei',
                                    'Juni' => 'Juni',
                                    'Juli' => 'Juli',
                                    'Agustus' => 'Agustus',
                                    'September' => 'September',
                                    'Oktober' => 'Oktober',
                                    'November' => 'November',
                                    'Desember' => 'Desember',
                                ])
                                ->native(false)
                                ->visible(function (callable $get) {
                                    $jenis = JenisPembayaran::find($get('jenis_pembayaran_id'));
                                    return $jenis?->tipe === 'bulanan';
                                }),

                            Forms\Components\TextInput::make('jumlah')
                                ->label('Jumlah')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),

                            DatePicker::make('tanggal_jatuh_tempo')
                                ->label('Tanggal Jatuh Tempo')
                                ->required()
                                ->default(now()->addDays(30)),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->addActionLabel('+ Tambah Jenis Tagihan')
                        ->itemLabel(function (array $state): ?string {
                            if (!empty($state['jenis_pembayaran_id'])) {
                                $jenis = JenisPembayaran::find($state['jenis_pembayaran_id']);
                                $label = $jenis?->nama_pembayaran ?? 'Tagihan';

                                if (!empty($state['bulan'])) {
                                    $label .= ' - ' . $state['bulan'];
                                }

                                if (!empty($state['jumlah'])) {
                                    $label .= ' (Rp ' . number_format($state['jumlah'], 0, ',', '.') . ')';
                                }

                                return $label;
                            }
                            return 'Tagihan Baru';
                        })
                        ->collapsible()
                        ->cloneable()
                        ->required()
                        ->minItems(1),
                ])
                ->visible(fn (callable $get) => !empty($get('kelas'))),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('Generate')
                ->label('Generate Tagihan')
                ->requiresConfirmation()
                ->modalHeading('Yakin ingin generate tagihan?')
                ->modalDescription(function (callable $get) {
                    $kelas = Kelas::find($get('kelas'));
                    $siswaCount = $kelas ? Siswa::where('kelas_id', $kelas->id)->count() : 0;
                    $tagihanCount = count($get('tagihan_items') ?? []);
                    $totalTagihan = $siswaCount * $tagihanCount;

                    return "Akan dibuat {$totalTagihan} tagihan ({$tagihanCount} jenis tagihan × {$siswaCount} siswa) untuk kelas {$kelas?->nama}.";
                })
                ->modalButton('Ya, Generate')
                ->cancelButtonText('Batal')
                ->submit('generate')
                ->color('primary'),
        ];
    }

    public function generate()
    {
        $data = $this->form->getState();

        try {
            DB::beginTransaction();

            $siswaList = Siswa::where('kelas_id', $data['kelas'])->get();
            $tahunAkademikId = Kelas::findOrFail($data['kelas'])->tahun?->id;
            $tagihanItems = $data['tagihan_items'] ?? [];

            if (empty($tagihanItems)) {
                throw new \Exception('Minimal harus ada 1 jenis tagihan');
            }

            $totalCreated = 0;

            foreach ($siswaList as $siswa) {
                foreach ($tagihanItems as $item) {
                    // Check if tagihan already exists to prevent duplicates
                    $exists = Tagihan::where([
                        'siswa_id' => $siswa->id,
                        'jenis_pembayaran_id' => $item['jenis_pembayaran_id'],
                        'tahun_akademik_id' => $tahunAkademikId,
                        'bulan' => $item['bulan'] ?? null,
                    ])->exists();

                    if (!$exists) {
                        Tagihan::create([
                            'siswa_id' => $siswa->id,
                            'tahun_akademik_id' => $tahunAkademikId,
                            'jenis_pembayaran_id' => $item['jenis_pembayaran_id'],
                            'bulan' => $item['bulan'] ?? null,
                            'jumlah' => $item['jumlah'],
                            'status' => Tagihan::STATUS_BELUM_BAYAR,
                            'tanggal_jatuh_tempo' => $item['tanggal_jatuh_tempo'],
                        ]);
                        $totalCreated++;
                    }
                }
            }

            DB::commit();
            $this->form->fill([]);

            Notification::make()
                ->title('Berhasil!')
                ->body("Berhasil membuat {$totalCreated} tagihan untuk " . count($siswaList) . " siswa dengan " . count($tagihanItems) . " jenis pembayaran.")
                ->success()
                ->send();

            return redirect()->to('/admin/tagihans');

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Gagal!')
                ->body('Terjadi kesalahan saat generate tagihan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
