<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Supplier::truncate();
        Schema::enableForeignKeyConstraints();
        
        $faker = Faker::create('id_ID');
        for ($i = 0; $i < 100; $i++) {
            Supplier::create([
                'nama' => $faker->name($faker->randomElement(['male', 'female'])),
                'nomor_telepon' => str_replace(' ', '', str_replace('(+62)', '', $faker->phoneNumber())),
                'alamat' => $faker->address(),
            ]);
        }
    }
}
