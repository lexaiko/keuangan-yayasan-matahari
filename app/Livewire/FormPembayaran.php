<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\DetailPembayaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class FormPembayaran extends Component
{
    public $siswaId;
    public $tagihans = [];
    public $selectedTagihan = [];
    public $jumlahBayar = [];

    public function updatedSiswaId($value)
    {
        $this->tagihans = Tagihan::with('jenisPembayaran')
            ->where('siswa_id', $value)
            ->where('status', 'belum')
            ->get();
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::create([
                'siswa_id' => $this->siswaId,
                'tanggal' => now(),
            ]);

            foreach ($this->selectedTagihan as $index => $tagihanId) {
                DetailPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id' => $tagihanId,
                    'jumlah_bayar' => $this->jumlahBayar[$index],
                ]);
            }

            DB::commit();
            session()->flash('success', 'Pembayaran berhasil disimpan!');
            $this->reset();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.form-pembayaran', [
            'siswas' => Siswa::all()
        ]);
    }
}

