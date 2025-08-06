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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        // Pilih siswa
                        Forms\Components\Select::make('siswa_id')
                            ->relationship('siswa', 'nama')
                            ->searchable()
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

                        // Tombol pilih tagihan dengan perbaikan search
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('pilihTagihan')
                                ->label('+ Add Tagihan')
                                ->button()
                                ->modalHeading('Pilih Tagihan Siswa')
                                ->modalSubmitActionLabel('Pilih')
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
                                    $selectedIds = $get('tagihan_ids') ?? []; // Ambil ID yang sudah dipilih

                                    return [
                                        Forms\Components\Select::make('selected_tagihan')
                                            ->label('Tagihan Tersedia')
                                            ->options(function () use ($siswaId, $selectedIds) {
                                                if (!$siswaId) return ['debug' => 'Pilih siswa terlebih dahulu'];

                                                $tagihans = Tagihan::where('siswa_id', $siswaId)
                                                    ->where('status', '!=', Tagihan::STATUS_LUNAS)
                                                    ->whereNotIn('id', $selectedIds) // Exclude yang sudah dipilih
                                                    ->with('jenisPembayaran')
                                                    ->get();

                                                if ($tagihans->count() == 0) {
                                                    return ['debug' => count($selectedIds) > 0 ? 'Semua tagihan sudah dipilih' : 'Tidak ada tagihan untuk siswa ID: ' . $siswaId];
                                                }

                                                return $tagihans->mapWithKeys(function ($item) {
                                                    // Hitung sisa tagihan yang belum dibayar
                                                    $totalDibayar = $item->detailPembayarans()->sum('jumlah_bayar');
                                                    $sisaTagihan = $item->jumlah - $totalDibayar;

                                                    $label = ($item->jenisPembayaran->nama_pembayaran ?? 'Unknown') . ' - ' . ($item->bulan ?? '');

                                                    // Tambahkan info sisa jika sudah ada pembayaran
                                                    if ($totalDibayar > 0) {
                                                        $label .= ' - Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.') . ' (Total: Rp ' . number_format($item->jumlah, 0, ',', '.') . ', Sudah dibayar: Rp ' . number_format($totalDibayar, 0, ',', '.') . ')';
                                                    } else {
                                                        $label .= ' - Rp ' . number_format($item->jumlah, 0, ',', '.') ;
                                                    }

                                                    return [$item->id => $label];
                                                })->toArray();
                                            })
                                            ->multiple()
                                            ->searchable()
                                            ->required()
                                            ->helperText(count($selectedIds) > 0 ? 'Sudah dipilih: ' . count($selectedIds) . ' tagihan' : null),
                                    ];
                                })
                                ->action(function (array $data, callable $get, callable $set) {
                                    $currentIds = $get('tagihan_ids') ?? [];
                                    $newIds = array_merge($currentIds, $data['selected_tagihan']);
                                    $uniqueIds = array_unique($newIds);
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
                                                'jumlah_tagihan' => $infoText, // ✅ PERBAIKI: Set langsung ke info text, bukan angka
                                                'jumlah_bayar' => $sisaTagihan, // Set ke sisa yang belum dibayar
                                                'total_tagihan_asli' => $tagihan->jumlah, // Simpan total asli untuk referensi
                                                'sudah_dibayar' => $totalDibayar, // Simpan yang sudah dibayar untuk referensi
                                            ];
                                        })->toArray();

                                        $set('detail_pembayarans', $details);

                                        $total = collect($details)->sum(function ($item) {
                                            return floatval($item['jumlah_bayar'] ?? 0);
                                        });
                                        $set('total_bayar', $total);
                                        $set('tunai', $total); // Set tunai sama dengan total
                                        $set('kembalian', 0); // Kembalian 0
                                    }
                                })
                                ->modalWidth('4xl'),
                        ]),

                        // Daftar tagihan yang dipilih
                        Forms\Components\Repeater::make('detail_pembayarans')
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
            ->defaultSort('tanggal_bayar', 'desc');
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
