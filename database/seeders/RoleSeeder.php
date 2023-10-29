<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();
        $role = [
            [
                'nama' => 'admin',
                'keterangan' => 'administrator',
            ],
            [
                'nama' => 'kasir',
                'keterangan' => 'kasir',
            ]
        ];
        foreach ($role as $value) {
            Role::create([
                'nama' => $value['nama'],
                'keterangan' => $value['keterangan'],
            ]);
        }
    }
}
