<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        RolePermission::truncate();
        Schema::enableForeignKeyConstraints();

        // mengecek ketersediaan user admin
        $roleadmin = Role::firstOrCreate([
            'nama' => 'admin',
            'keterangan' => 'administrator',
        ]);

        // memberikan role pada admin
        $permission = Permission::all();
        foreach ($permission as $value) {
            RolePermission::create([
                'role_id' => $roleadmin->id,
                'permission_id' => $value->id
            ]);
        }
        
        // mengecek ketersediaan user kasir
        $rolekasir = Role::firstOrCreate([
            'nama' => 'kasir',
            'keterangan' => 'kasir',
        ]);
        
        // memberikan role pada kasir
        $permission = DB::table('permissions')->where('nama', 'like', "%penjualan%")->where('nama', 'not like', '%hapus%')->get();
        foreach ($permission as $value) {
            RolePermission::create([
                'role_id' => $rolekasir->id,
                'permission_id' => $value->id
            ]);
        }
    }
}
