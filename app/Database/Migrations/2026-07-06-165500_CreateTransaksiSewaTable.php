<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiSewaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'pelanggan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mobil_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_transaksi' => [
                'type' => 'DATE',
            ],
            'tanggal_sewa' => [
                'type' => 'DATE',
            ],
            'tanggal_kembali' => [
                'type' => 'DATE',
            ],
            'lama_sewa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'harga_sewa_per_hari' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'subtotal_sewa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_layanan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'total_tagihan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'booking',
            ],
            'status_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'belum_bayar',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('pelanggan_id');
        $this->forge->addKey('mobil_id');
        $this->forge->addUniqueKey('kode_transaksi');
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('mobil_id', 'mobil', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('transaksi_sewa');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaksi_sewa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'layanan_tambahan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_layanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'harga_layanan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_harga' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaksi_sewa_id');
        $this->forge->addKey('layanan_tambahan_id');
        $this->forge->addForeignKey('transaksi_sewa_id', 'transaksi_sewa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('layanan_tambahan_id', 'layanan_tambahan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('transaksi_sewa_layanan');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaksi_sewa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_pembayaran' => [
                'type' => 'DATE',
            ],
            'jumlah_bayar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaksi_sewa_id');
        $this->forge->addForeignKey('transaksi_sewa_id', 'transaksi_sewa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembayaran_sewa');
    }

    public function down()
    {
        $this->forge->dropTable('pembayaran_sewa');
        $this->forge->dropTable('transaksi_sewa_layanan');
        $this->forge->dropTable('transaksi_sewa');
    }
}
