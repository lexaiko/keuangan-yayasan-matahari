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
    protected static ?string $navigationLabel = 'Generate Tagihan Siswa';
    protected static ?string $navigationGroup = 'Manajemen Tagihan';
    protected static ?int $navigationSort = -3;

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
                        ->options(Kelas::with('tingkat')->get()->mapWithKeys(function ($kelas) {
                            return [$kelas->id => $kelas->nama . ' (' . $kelas->tingkat->nama . ')'];
                        }))
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
                                ->options(JenisPembayaran::pluck('nama_pembayaran', 'id'))
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

                            Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
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
            $kelas = Kelas::findOrFail($data['kelas']);
            $tahunAkademikId = $kelas->tahun?->id;

            if (!$tahunAkademikId) {
                throw new \Exception('Kelas tidak memiliki tahun akademik yang valid');
            }

            $tagihanItems = $data['tagihan_items'] ?? [];

            if (empty($tagihanItems)) {
                throw new \Exception('Minimal harus ada 1 jenis tagihan');
            }

            $totalCreated = 0;
            $totalSkipped = 0;
            $skippedDetails = [];

            foreach ($siswaList as $siswa) {
                foreach ($tagihanItems as $item) {
                    // Check if tagihan already exists for this specific combination
                    $existingTagihan = Tagihan::where([
                        'siswa_id' => $siswa->id,
                        'jenis_pembayaran_id' => $item['jenis_pembayaran_id'],
                        'tahun_akademik_id' => $tahunAkademikId,
                        'bulan' => $item['bulan'] ?? null,
                    ])->first();

                    if (!$existingTagihan) {
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
                    } else {
                        $totalSkipped++;
                        $jenisPembayaran = JenisPembayaran::find($item['jenis_pembayaran_id']);
                        $skippedDetails[] = "{$siswa->nama} - {$jenisPembayaran->nama_pembayaran}" .
                                          ($item['bulan'] ? " ({$item['bulan']})" : '');
                    }
                }
            }

            DB::commit();
            $this->form->fill([]);

            $message = "Berhasil membuat {$totalCreated} tagihan untuk " . count($siswaList) . " siswa.";

            if ($totalSkipped > 0) {
                $message .= "\n\nTagihan yang dilewati ({$totalSkipped}):\n" . implode("\n", array_slice($skippedDetails, 0, 5));
                if (count($skippedDetails) > 5) {
                    $message .= "\n... dan " . (count($skippedDetails) - 5) . " lainnya";
                }
            }

            Notification::make()
                ->title('Generate Tagihan Selesai')
                ->body($message)
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
