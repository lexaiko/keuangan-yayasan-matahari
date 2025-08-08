<?php

namespace App\Filament\Resources;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\PembayaranResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Pembayaran Siswa';
    protected static ?string $title = 'Pembayaran Siswa';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?int $navigationSort = -21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        // Pilih siswa
                        Forms\Components\Select::make('siswa_id')
                            ->relationship('siswa', 'nama', function ($query) {
                                return $query->select('id', 'nama', 'nis');
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama} - {$record->nis}")
                            ->searchable(['nama', 'nis'])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('tagihan_ids', []))
                            ->live(),

                        // Tanggal bayar
                        Forms\Components\DatePicker::make('tanggal_bayar')
                            ->default(now())
                            ->required(),

                        // Hidden field untuk menyimpan ID tagihan
                        Forms\Components\Hidden::make('tagihan_ids')
                            ->default([]),

                        // Tombol pilih tagihan dengan tabel selection
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('pilihTagihan')
                                ->label('+ Add Tagihan')
                                ->button()
                                ->modalHeading('Pilih Tagihan Siswa')
                                ->modalSubmitActionLabel('Tambah Tagihan Terpilih')
                                ->disabled(function (callable $get) {
                                    $siswaId = $get('siswa_id');
                                    if (!$siswaId) return true;

                                    $selectedIds = $get('tagihan_ids') ?? [];
                                    $availableCount = Tagihan::where('siswa_id', $siswaId)
                                        ->where('status', '!=', Tagihan::STATUS_LUNAS)
                                        ->whereNotIn('id', $selectedIds)
                                        ->count();

                                    return $availableCount == 0;
                                })
                                ->badge(function (callable $get) {
                                    $siswaId = $get('siswa_id');
                                    if (!$siswaId) return null;

                                    $selectedIds = $get('tagihan_ids') ?? [];
                                    $availableCount = Tagihan::where('siswa_id', $siswaId)
                                        ->where('status', '!=', Tagihan::STATUS_LUNAS)
                                        ->whereNotIn('id', $selectedIds)
                                        ->count();

                                    return $availableCount > 0 ? $availableCount : 'Semua dipilih';
                                })
                                ->form(function (callable $get) {
                                    $siswaId = $get('siswa_id');
                                    $selectedIds = $get('tagihan_ids') ?? [];

                                    if (!$siswaId) {
                                        return [
                                            Forms\Components\Placeholder::make('warning')
                                                ->content('Pilih siswa terlebih dahulu')
                                        ];
                                    }

                                    $tagihans = Tagihan::where('siswa_id', $siswaId)
                                        ->where('status', '!=', Tagihan::STATUS_LUNAS)
                                        ->whereNotIn('id', $selectedIds)
                                        ->with('jenisPembayaran')
                                        ->orderBy('tanggal_jatuh_tempo')
                                        ->get();

                                    if ($tagihans->count() == 0) {
                                        return [
                                            Forms\Components\Placeholder::make('no_tagihan')
                                                ->content(count($selectedIds) > 0 ?
                                                    'Semua tagihan sudah dipilih (' . count($selectedIds) . ' tagihan)' :
                                                    'Tidak ada tagihan yang perlu dibayar')
                                        ];
                                    }

                                    $options = $tagihans->mapWithKeys(function ($tagihan) {
                                        $totalDibayar = $tagihan->detailPembayarans()->sum('jumlah_bayar');
                                        $sisaTagihan = $tagihan->jumlah - $totalDibayar;

                                        $label = $tagihan->jenisPembayaran->nama_pembayaran . ' - ' . ($tagihan->bulan ?? '');
                                        $label .= ' | Total: Rp ' . number_format($tagihan->jumlah, 0, ',', '.');

                                        if ($totalDibayar > 0) {
                                            $label .= ' | Sudah dibayar: Rp ' . number_format($totalDibayar, 0, ',', '.');
                                            $label .= ' | Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.');
                                        }

                                        $label .= ' | Status: ' . ucfirst(str_replace('_', ' ', $tagihan->status));

                                        if ($tagihan->tanggal_jatuh_tempo) {
                                            $label .= ' | Jatuh tempo: ' . \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d/m/Y');
                                        }

                                        return [$tagihan->id => $label];
                                    })->toArray();

                                    return [
                                        Forms\Components\CheckboxList::make('selected_tagihan_table')
                                            ->label('Pilih Tagihan')
                                            ->options($options)
                                            ->columns(1)
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->required()
                                            ->helperText('Pilih satu atau lebih tagihan yang akan dibayar'),
                                    ];
                                })
                                ->action(function (array $data, callable $get, callable $set) {
                                    $currentIds = $get('tagihan_ids') ?? [];
                                    $newIds = $data['selected_tagihan_table'] ?? [];
                                    $uniqueIds = array_unique(array_merge($currentIds, $newIds));
                                    $set('tagihan_ids', $uniqueIds);

                                    // Load semua data tagihan yang dipilih (termasuk yang baru)
                                    if (!empty($uniqueIds)) {
                                        $tagihans = Tagihan::whereIn('id', $uniqueIds)
                                            ->with('jenisPembayaran')
                                            ->get();

                                        $details = $tagihans->map(function ($tagihan) {
                                            // Hitung sisa tagihan yang belum dibayar
                                            $totalDibayar = $tagihan->detailPembayarans()->sum('jumlah_bayar');
                                            $sisaTagihan = $tagihan->jumlah - $totalDibayar;

                                            // Buat info text langsung
                                            if ($totalDibayar > 0) {
                                                $infoText = 'Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.') . ' dari total Rp ' . number_format($tagihan->jumlah, 0, ',', '.');
                                            } else {
                                                $infoText = 'Total: Rp ' . number_format($tagihan->jumlah, 0, ',', '.') ;
                                            }

                                            return [
                                                'tagihan_id' => $tagihan->id,
                                                'nama_pembayaran' => $tagihan->jenisPembayaran->nama_pembayaran . ' - ' . ($tagihan->bulan ?? ''),
                                                'jumlah_tagihan' => $infoText,
                                                'jumlah_bayar' => $sisaTagihan,
                                                'total_tagihan_asli' => $tagihan->jumlah,
                                                'sudah_dibayar' => $totalDibayar,
                                            ];
                                        })->toArray();

                                        $set('detail_pembayarans', $details);

                                        $total = collect($details)->sum(function ($item) {
                                            return floatval($item['jumlah_bayar'] ?? 0);
                                        });
                                        $set('total_bayar', $total);
                                        $set('tunai', $total);
                                        $set('kembalian', 0);
                                    }
                                })
                                ->modalWidth('6xl'),
                        ]),

                        // Daftar tagihan yang dipilih
                        Forms\Components\Repeater::make('detail_pembayarans')
                            ->label('Daftar Pembayaran')
                            ->schema([
                                Forms\Components\Hidden::make('tagihan_id')
                                    ->live() // ✅ TAMBAHKAN live()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        // Trigger update info tagihan saat tagihan_id berubah
                                        $tagihanId = $get('tagihan_id');

                                        if ($tagihanId) {
                                            $tagihan = \App\Models\Tagihan::find($tagihanId);
                                            if ($tagihan) {
                                                $totalTagihan = $tagihan->jumlah;
                                                $totalDibayar = $tagihan->detailPembayarans()->sum('jumlah_bayar');
                                                $sisaTagihan = $totalTagihan - $totalDibayar;

                                                if ($totalDibayar > 0) {
                                                    $infoText = 'Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.') . ' dari total Rp ' . number_format($totalTagihan, 0, ',', '.');
                                                } else {
                                                    $infoText = 'Total: Rp ' . number_format($totalTagihan, 0, ',', '.') . ' (belum dibayar)';
                                                }

                                                $set('jumlah_tagihan', $infoText);
                                            }
                                        }
                                    }),
                                Forms\Components\Hidden::make('total_tagihan_asli'),
                                Forms\Components\Hidden::make('sudah_dibayar'),

                                // Nama pembayaran (otomatis)
                                Forms\Components\TextInput::make('nama_pembayaran')
                                    ->label('Jenis Pembayaran')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(function ($state, callable $get) {
                                        if (is_object($state) && isset($state->jenisPembayaran)) {
                                            return $state->jenisPembayaran->nama_pembayaran . ' - ' . ($state->bulan ?? '');
                                        }
                                        return $state;
                                    }),

                                // Jumlah tagihan dengan info sisa/total
                                Forms\Components\TextInput::make('jumlah_tagihan')
                                    ->label('Info Tagihan')
                                    ->disabled()
                                    ->dehydrated(false),

                                // Input jumlah bayar
                                Forms\Components\TextInput::make('jumlah_bayar')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->default(0)
                                    ->live(onBlur: true) // ✅ PERBAIKI: Hanya update setelah user selesai input (onBlur)
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        // Delay untuk memastikan state sudah terupdate
                                        $details = $get('../../detail_pembayarans') ?? [];
                                        $total = 0;

                                        foreach ($details as $detail) {
                                            $total += floatval($detail['jumlah_bayar'] ?? 0);
                                        }

                                        $set('../../total_bayar', $total);

                                        // Auto-update tunai untuk UX yang lebih baik
                                        $set('../../tunai', $total);

                                        // Update kembalian (akan jadi 0 karena tunai = total)
                                        $set('../../kembalian', 0);
                                    }),
                            ])
                            ->columns(3)
                            ->addable(false)
                            ->deletable(true)
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['nama_pembayaran'] ?? null)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                // Update tagihan_ids ketika ada perubahan di repeater (termasuk saat delete)
                                $details = $get('detail_pembayarans') ?? [];
                                $currentIds = [];

                                foreach ($details as $detail) {
                                    if (!empty($detail['tagihan_id'])) {
                                        $currentIds[] = $detail['tagihan_id'];
                                    }
                                }

                                $set('tagihan_ids', $currentIds);

                                // Recalculate total
                                $total = 0;
                                foreach ($details as $detail) {
                                    $total += floatval($detail['jumlah_bayar'] ?? 0);
                                }
                                $set('total_bayar', $total);

                                // Auto-update tunai untuk UX yang lebih baik
                                $set('tunai', $total);

                                // Update kembalian (akan jadi 0 karena tunai = total)
                                $set('kembalian', 0);
                            }),

                        // Total bayar (otomatis)
                        Forms\Components\TextInput::make('total_bayar')
                            ->label('Total Bayar')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->formatStateUsing(fn ($state) => number_format(floatval($state ?? 0), 0, ',', '.')),

                        // Input tunai dengan validasi
                        Forms\Components\TextInput::make('tunai')
                            ->label('Uang Tunai')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->live(onBlur: true) // ✅ PERBAIKI: Hanya update setelah user selesai input (onBlur)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $tunai = floatval(str_replace(['.', ','], ['', '.'], $get('tunai') ?? 0));
                                $total = floatval($get('total_bayar') ?? 0);
                                $kembalian = $tunai - $total;
                                $set('kembalian', max($kembalian, 0));
                            })
                            ->helperText(function (callable $get) {
                                $tunai = floatval(str_replace(['.', ','], ['', '.'], $get('tunai') ?? 0));
                                $total = floatval($get('total_bayar') ?? 0);

                                if ($total > 0 && $tunai < $total) {
                                    $kurang = $total - $tunai;
                                    return '⚠️ Uang tunai kurang Rp ' . number_format($kurang, 0, ',', '.') . ' dari total bayar';
                                }

                                return null;
                            })
                            ->rules([
                                function (callable $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $tunai = floatval(str_replace(['.', ','], ['', '.'], $value ?? 0));
                                        $total = floatval($get('total_bayar') ?? 0);

                                        if ($total > 0 && $tunai < $total) {
                                            $kurang = $total - $tunai;
                                            $fail('Uang tunai kurang Rp ' . number_format($kurang, 0, ',', '.') . ' dari total bayar (Rp ' . number_format($total, 0, ',', '.') . ')');
                                        }
                                    };
                                },
                            ]),

                        // Kembalian (otomatis)
                        Forms\Components\TextInput::make('kembalian')
                            ->label('Kembalian')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->formatStateUsing(fn ($state) => number_format(floatval($state ?? 0), 0, ',', '.')),

                        // Keterangan
                        Forms\Components\Textarea::make('keterangan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Pembayaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->label('Total Bayar')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('tunai')
                    ->label('Tunai')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('kembalian')
                    ->label('Kembalian')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('siswa')
                    ->relationship('siswa', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record) => route('pembayaran.print', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // ✅ PERBAIKI: Collect affected tagihan IDs BEFORE delete
                        $affectedTagihanIds = \App\Models\DetailPembayaran::where('pembayaran_id', $record->id)
                            ->pluck('tagihan_id')
                            ->unique();

                        // Hapus detail pembayaran
                        \App\Models\DetailPembayaran::where('pembayaran_id', $record->id)->delete();

                        // ✅ PERBAIKI: Update status SETELAH delete dengan logic yang benar
                        foreach ($affectedTagihanIds as $tagihanId) {
                            $tagihan = \App\Models\Tagihan::find($tagihanId);
                            if ($tagihan) {
                                // Hitung ulang total dibayar SETELAH penghapusan
                                $totalDibayarSekarang = $tagihan->detailPembayarans()->sum('jumlah_bayar');

                                if ($totalDibayarSekarang == 0) {
                                    // Tidak ada pembayaran sama sekali
                                    $tagihan->update(['status' => \App\Models\Tagihan::STATUS_BELUM_BAYAR]);
                                } elseif ($totalDibayarSekarang >= $tagihan->jumlah) {
                                    // Masih lunas
                                    $tagihan->update(['status' => \App\Models\Tagihan::STATUS_LUNAS]);
                                } else {
                                    // Ada pembayaran tapi belum lunas = sebagian
                                    $tagihan->update(['status' => \App\Models\Tagihan::STATUS_SEBAGIAN]);
                                }
                            }
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // ✅ PERBAIKI: Collect all affected tagihan IDs BEFORE bulk delete
                            $allAffectedTagihanIds = collect();

                            foreach ($records as $record) {
                                $affectedIds = \App\Models\DetailPembayaran::where('pembayaran_id', $record->id)
                                    ->pluck('tagihan_id');
                                $allAffectedTagihanIds = $allAffectedTagihanIds->merge($affectedIds);
                            }

                            $uniqueTagihanIds = $allAffectedTagihanIds->unique();

                            // Hapus semua detail pembayaran
                            foreach ($records as $record) {
                                \App\Models\DetailPembayaran::where('pembayaran_id', $record->id)->delete();
                            }

                            // ✅ PERBAIKI: Update status SETELAH bulk delete
                            foreach ($uniqueTagihanIds as $tagihanId) {
                                $tagihan = \App\Models\Tagihan::find($tagihanId);
                                if ($tagihan) {
                                    // Hitung ulang total dibayar SETELAH penghapusan
                                    $totalDibayarSekarang = $tagihan->detailPembayarans()->sum('jumlah_bayar');

                                    if ($totalDibayarSekarang == 0) {
                                        // Tidak ada pembayaran sama sekali
                                        $tagihan->update(['status' => \App\Models\Tagihan::STATUS_BELUM_BAYAR]);
                                    } elseif ($totalDibayarSekarang >= $tagihan->jumlah) {
                                        // Masih lunas
                                        $tagihan->update(['status' => \App\Models\Tagihan::STATUS_LUNAS]);
                                    } else {
                                        // Ada pembayaran tapi belum lunas = sebagian
                                        $tagihan->update(['status' => \App\Models\Tagihan::STATUS_SEBAGIAN]);
                                    }
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'view' => Pages\ViewPembayaran::route('/{record}'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
