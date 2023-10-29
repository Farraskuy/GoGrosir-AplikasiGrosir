<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppSettingController extends Controller
{
    public function selalucetak(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'value' => 'in:true,false'
        ],[
            'value.in' => 'Harap Masukan Nilai Baru Dengan Benar'
        ]);

        if ($validation->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validation->errors(), 'message_type' => 'danger']);
            }
            return redirect()->back()->withErrors($validation)->with(['message_type' => 'danger']);
        }
        
        $action = DB::table('app_settings')->update(['selalu_cetak_struk' => $request->value]);
        
        $message = [
            'message_type' => 'danger',
            'message' => 'Gagal Mengubah Pengaturan Selalu Cetak Struk',
            'selalu_cetak_struk' => "false"
        ];
        if ($action) {
            $message = [
                'message_type' => 'success',
                'message' => 'Berhasil Merubah Pengaturan Selalu Cetak Struk',
                'selalu_cetak_struk' => AppSetting::all(['selalu_cetak_struk'])->first()->selalu_cetak_struk
            ];
        }

        if ($request->ajax()) {
            return response()->json($message);
        }
        return redirect()->back()->with($message);
    }
}
