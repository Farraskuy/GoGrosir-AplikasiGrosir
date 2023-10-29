<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // dd(Role::firstOrCreate(['nama' => 'admin', 'keterangan' => 'administrator'])->id);
        $data = [
            [
                'username' => 'admin',
                'role_id' => Role::firstOrCreate(['nama' => 'admin', 'keterangan' => 'administrator'])->id,
                'email' => 'admin@gmail.com',
                'password' => Hash::make('1'),
                'nama' => 'nama admin',
                'jenis_kelamin' => 'L',
                'nomor_telepon' => '01234567890',
            ],
            [
                'username' => 'kasir',
                'role_id' => Role::firstOrCreate(['nama' => 'kasir', 'keterangan' => 'kasir'])->id,
                'email' => 'kasir@gmail.com',
                'password' => Hash::make('1'),
                'nama' => 'nama kasir',
                'jenis_kelamin' => 'L',
                'nomor_telepon' => '01234567890',
            ],
        ];
        foreach ($data as $value) {
            User::create([
                'username' => $value['username'],
                'role_id' => $value['role_id'],
                'email' => $value['email'],
                'password' => $value['password'],
                'nama' => $value['nama'],
                'jenis_kelamin' => $value['jenis_kelamin'],
                'nomor_telepon' => $value['nomor_telepon'],
            ]);
        }
        
    }
}
