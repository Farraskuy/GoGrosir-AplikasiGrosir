<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Schema::enableForeignKeyConstraints();

        $permission = [
            ['nama' => 'dashboard', 'keterangan' => 'izin ke halaman dashboard'],
            ['nama' => 'kasir', 'keterangan' => 'izin ke halaman kasir'],
        ];

        foreach ($permission as $value) {
            Permission::create([
                'nama' => $value['nama'],
                'keterangan' => $value['keterangan']
            ]);
        }

        $fitur = ['barang', 'kategori', 'supplier', 'penjualan'];
        $keterangan = ['tambah', 'lihat', 'edit', 'hapus'];
        foreach ($fitur as $valuefitur) {
            foreach ($keterangan as $valueketerangan) {
                Permission::create([
                    'nama' => $valueketerangan . "_" . $valuefitur,
                    'keterangan' => "izin " . $valueketerangan . "_" . $valuefitur
                ]);
            }
        }

    }
}
