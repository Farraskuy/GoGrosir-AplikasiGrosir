<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Penjualan::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Faker::create('id_ID');
        $date = $faker->date('d');
        $j = 1;
        for ($i = 0; $i < 100; $i++) {

            if ($i % 20 == 0 && $i != 0) {
                $j = 1;
                $date = $faker->date('d');
            }

            $total = round($faker->randomNumber(5, true), -3);
            $potongan = round($faker->randomNumber(4, true), -3);

            $bayar = ($total - $potongan) + round($faker->randomNumber(4, true), -3);
            Penjualan::insert([
                'no_trans' => "TRS/202309" . $date . "/TGZ/" . str_pad($j, 6, '0', STR_PAD_LEFT), // TRS/20230925/TGZ/000001
                'id_petugas' => $faker->numberBetween(1, 2),
                'total_bayar' => $total,
                'total_potongan' => $potongan,
                'bayar' => $bayar,
                'kembalian' => $bayar - ($total - $potongan),
                'created_at' => "2023-09-" . $date,
                'updated_at' => "2023-09-" . $date
            ]);

            $j++;
        }
    }
}
