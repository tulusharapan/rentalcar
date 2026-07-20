<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecalculateLateReturnBilling extends Migration
{
    public function up()
    {
        $this->db->query("
            UPDATE transaksi_sewa
            SET lama_sewa = DATEDIFF(tanggal_dikembalikan, tanggal_sewa) + 1,
                subtotal_sewa = (DATEDIFF(tanggal_dikembalikan, tanggal_sewa) + 1) * harga_sewa_per_hari,
                total_tagihan = ((DATEDIFF(tanggal_dikembalikan, tanggal_sewa) + 1) * harga_sewa_per_hari) + total_layanan + denda
            WHERE tanggal_dikembalikan IS NOT NULL
                AND tanggal_dikembalikan > tanggal_kembali
        ");

        $this->syncPaymentStatusForLateReturns();
    }

    public function down()
    {
        $this->db->query("
            UPDATE transaksi_sewa
            SET lama_sewa = DATEDIFF(tanggal_kembali, tanggal_sewa) + 1,
                subtotal_sewa = (DATEDIFF(tanggal_kembali, tanggal_sewa) + 1) * harga_sewa_per_hari,
                total_tagihan = ((DATEDIFF(tanggal_kembali, tanggal_sewa) + 1) * harga_sewa_per_hari) + total_layanan + denda
            WHERE tanggal_dikembalikan IS NOT NULL
                AND tanggal_dikembalikan > tanggal_kembali
        ");

        $this->syncPaymentStatusForLateReturns();
    }

    private function syncPaymentStatusForLateReturns(): void
    {
        $this->db->query("
            UPDATE transaksi_sewa ts
            LEFT JOIN (
                SELECT transaksi_sewa_id, SUM(jumlah_bayar) AS total_bayar
                FROM pembayaran_sewa
                GROUP BY transaksi_sewa_id
            ) pb ON pb.transaksi_sewa_id = ts.id
            SET ts.status_pembayaran = CASE
                WHEN COALESCE(pb.total_bayar, 0) <= 0 THEN 'belum_bayar'
                WHEN COALESCE(pb.total_bayar, 0) >= ts.total_tagihan THEN 'lunas'
                ELSE 'belum_lunas'
            END
            WHERE ts.tanggal_dikembalikan IS NOT NULL
                AND ts.tanggal_dikembalikan > ts.tanggal_kembali
        ");
    }
}
