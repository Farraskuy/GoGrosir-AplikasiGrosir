<?php

use App\Models\AppSetting;
use App\Models\Role;

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin($id)
    {
        return Role::isSuperAdmin($id);
    }
}
if (!function_exists('format_tanggal')) {
    function format_tanggal($date)
    {
        $bulan = [
            'Januari', 'Februari', 'Maret',
            'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ];
        return date('d', strtotime($date)) . ' ' . $bulan[((int) date('m', strtotime($date))) - 1] . ' ' . date('Y', strtotime($date));
    }
}
if (!function_exists('nama_toko')) {
    function nama_toko()
    {
       return AppSetting::select('nama_toko')->first()->nama_toko;
    }
}
if (!function_exists('nama_aplikasi')) {
    function nama_aplikasi()
    {
       return "Aplikasi " . AppSetting::select('nama_toko')->first()->nama_toko;
    }
}
if (!function_exists('appSettings')) {
    function appSettings()
    {
       return AppSetting::first();
    }
}

