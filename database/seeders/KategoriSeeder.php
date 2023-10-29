<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  
        // Kategori::truncate();
        $faker = Faker::create('id_ID');
        $count = 0;
        $jumlahdata = 100;
        for ($i = 0; $i < $jumlahdata; $i++) {
            Kategori::create([
                'nama' => $faker->word() . $faker->word(),
                'keterangan' => $faker->sentence(),
            ]);
            $count++;
        }
        dump("Berhasil menambahkan $count dari $jumlahdata");
    }
}
