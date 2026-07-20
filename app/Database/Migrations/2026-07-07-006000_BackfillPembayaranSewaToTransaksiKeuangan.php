<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillPembayaranSewaToTransaksiKeuangan extends Migration
{
    private int $categoryId = 2;

    public function up()
    {
        $kategori = $this->db->table('kategori_keuangan')
            ->where('id', $this->categoryId)
            ->get()
            ->getRowArray();

        if (! $kategori) {
            return;
        }

        $pembayaranRows = $this->db->table('pembayaran_sewa ps')
            ->select('ps.*, transaksi_sewa.kode_transaksi, pelanggan.nama_lengkap')
            ->join('transaksi_sewa', 'transaksi_sewa.id = ps.transaksi_sewa_id', 'left')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id', 'left')
            ->where('ps.transaksi_keuangan_id', null)
            ->where('ps.jumlah_bayar >', 0)
            ->get()
            ->getResultArray();

        foreach ($pembayaranRows as $pembayaran) {
            $catatan = 'Pembayaran sewa ' . ($pembayaran['kode_transaksi'] ?? ('#' . $pembayaran['transaksi_sewa_id']));

            if (! empty($pembayaran['nama_lengkap'])) {
                $catatan .= ' - ' . $pembayaran['nama_lengkap'];
            }

            if (! empty($pembayaran['catatan'])) {
                $catatan .= '. ' . trim((string) $pembayaran['catatan']);
            }

            $this->db->table('transaksi_keuangan')->insert([
                'tanggal'              => $pembayaran['tanggal_pembayaran'],
                'jenis'                => 'Pemasukan',
                'kategori_keuangan_id' => $this->categoryId,
                'kategori'             => $kategori['kategori'],
                'nominal'              => (int) $pembayaran['jumlah_bayar'],
                'catatan'              => $catatan,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);

            $this->db->table('pembayaran_sewa')
                ->where('id', $pembayaran['id'])
                ->update([
                    'transaksi_keuangan_id' => $this->db->insertID(),
                    'updated_at'            => date('Y-m-d H:i:s'),
                ]);
        }
    }

    public function down()
    {
        // Backfill tidak dibalik otomatis agar data keuangan yang sudah tercatat tidak terhapus tanpa audit manual.
    }
}
