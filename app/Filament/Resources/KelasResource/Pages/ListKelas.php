<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Filament\Resources\KelasResource;
use App\Models\Kelas;
use App\Models\TahunAkademik;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListKelas extends ListRecords
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('ubahTahunAkademikMasal')
                ->label('Ubah Tahun Akademik Masal')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('tahun_lama_id')
                        ->label('Tahun Akademik Saat Ini')
                        ->options(TahunAkademik::pluck('nama', 'id'))
                        ->required()
                        ->live()
                        ->helperText('Pilih tahun akademik yang akan diubah'),

                    Forms\Components\CheckboxList::make('kelas_ids')
                        ->label('Pilih Kelas')
                        ->options(function (callable $get) {
                            $tahunId = $get('tahun_lama_id');
                            if (!$tahunId) return [];

                            return Kelas::where('tahun_id', $tahunId)
                                ->with('tingkat')
                                ->get()
                                ->mapWithKeys(function ($kelas) {
                                    $siswaCount = $kelas->siswas()->count();
                                    return [$kelas->id => "{$kelas->nama} ({$kelas->tingkat->nama}) - {$siswaCount} siswa"];
                                });
                        })
                        ->searchable()
                        ->bulkToggleable()
                        ->required()
                        ->visible(fn (callable $get) => !empty($get('tahun_lama_id'))),

                    Forms\Components\Select::make('tahun_baru_id')
                        ->label('Tahun Akademik Tujuan')
                        ->options(function (callable $get) {
                            $tahunLamaId = $get('tahun_lama_id');
                            return TahunAkademik::when($tahunLamaId, function ($query) use ($tahunLamaId) {
                                return $query->where('id', '!=', $tahunLamaId);
                            })->pluck('nama', 'id');
                        })
                        ->required()
                        ->visible(fn (callable $get) => !empty($get('kelas_ids'))),
                ])
                ->action(function (array $data) {
                    $kelasIds = $data['kelas_ids'] ?? [];
                    $tahunBaruId = $data['tahun_baru_id'];

                    $tahunBaru = TahunAkademik::find($tahunBaruId);
                    $updatedCount = 0;

                    foreach ($kelasIds as $kelasId) {
                        Kelas::find($kelasId)->update(['tahun_id' => $tahunBaruId]);
                        $updatedCount++;
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Berhasil!')
                        ->body("Berhasil mengubah tahun akademik {$updatedCount} kelas ke {$tahunBaru->nama}")
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Ubah Tahun Akademik Masal')
                ->modalDescription('Pilih tahun akademik saat ini, kelas yang akan diubah, dan tahun akademik tujuan.')
                ->modalWidth('3xl'),
        ];
    }
}
