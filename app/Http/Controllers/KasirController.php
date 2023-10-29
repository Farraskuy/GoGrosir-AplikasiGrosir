<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kasir.kasir', [
            'appSettings' => json_encode(AppSetting::all()->first())
        ]);
    }

    public function getDataBarang(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->get('search', '');
            $data = Barang::where('nama', 'like', "%$search%")->orWhere('barcode', 'like', "%$search%")->orderBy('nama', 'asc')->paginate(20)->withQueryString();
            return response()->json([
                'data' => $data,
                'pagination' =>  (string) $data->onEachSide(1)->links('pagination.kasir-pagination', ['fontsize' => '13.5px'])
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $requests = $request->all();
    //     $totalBayar =  $request->collect('harga_jual')
    //         ->map(function ($hargaJual, $key) use ($requests) {
    //             return $hargaJual * $requests['jumlah_beli'][$key];
    //         })
    //         ->sum();

    //     $totalPotongan = $request->collect('potongan')->sum();
    //     $bayar = $request['bayar'];
    //     $kembalian = $bayar - ($totalBayar - $totalPotongan);

    //     $notrans = $this->generateNoTrans();

    //     // $head =  [
    //     //     'no_trans' => $notrans,
    //     //     'id_petugas' => Auth::id(),
    //     //     'total_bayar' => $totalBayar,
    //     //     'total_potongan' => $totalPotongan,
    //     //     'bayar' => $bayar,
    //     //     'kembalian' => $kembalian
    //     // ];

    //     // $detail = [];
    //     // foreach ($requests['id_barang'] as $key => $value) {
    //     //     array_push($detail, [
    //     //         'no_trans' => $notrans,
    //     //         'id_barang' => $value,
    //     //         'type_beli' => $requests['type_beli'][$key],
    //     //         'harga_beli' => $requests['harga_beli'][$key],
    //     //         'harga_jual' => $requests['harga_jual'][$key],
    //     //         'jumlah_beli' => $requests['jumlah_beli'][$key],
    //     //         'potongan' => $requests['potongan'][$key],
    //     //         'subtotal' => ($requests['harga_jual'][$key] * $requests['jumlah_beli'][$key]) - $requests['potongan'][$key]
    //     //     ]);
    //     // }

    //     // return response()->json(['tohead' => $head, 'todetail' => $detail]);

    //     DB::beginTransaction();
    //     try {
    //         Penjualan::create([
    //             'no_trans' => $notrans,
    //             'id_petugas' => Auth::id(),
    //             'total_bayar' => $totalBayar,
    //             'total_potongan' => $totalPotongan,
    //             'bayar' => $bayar,
    //             'kembalian' => $kembalian
    //         ]);

    //         foreach ($requests['id_barang'] as $key => $value) {
    //             DetailPenjualan::create([
    //                 'no_trans' => $notrans,
    //                 'id_barang' => $value,
    //                 'satuan_beli' => $requests['satuan'][$key],
    //                 'harga_beli' => $requests['harga_beli'][$key],
    //                 'harga_jual' => $requests['harga_jual'][$key],
    //                 'jumlah_beli' => $requests['jumlah_beli'][$key],
    //                 'potongan' => $requests['potongan'][$key],
    //                 'subtotal' => ($requests['harga_jual'][$key] * $requests['jumlah_beli'][$key]) - $requests['potongan'][$key]
    //             ]);
    //         }
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message_type' => 'error',
    //             'message' => 'Transaksi gagal, Error : ' . $e->getMessage(),
    //             'oldRequestBody' => $request->all()
    //         ]);
    //     }
    //     DB::commit();
    //     return response()->json([
    //         'message_type' => 'success',
    //         'message' => 'Transaksi Berhasil',
    //         'no_trans' => $notrans,
    //         'kembalian' => $kembalian,
    //         'selalu_cetak' => AppSetting::all(['selalu_cetak_struk'])->first()->selalu_cetak_struk
    //     ]);
    // }

    public function getNotrans(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->generateNoTrans());
        }
        return $this->generateNoTrans();
    }

    public static function generateNoTrans()
    {
        $headNoTrans = "TRS/" . date('Ymd') . "/TGZ/";
        $lastNoTrans = DB::table('tpenjualan')
            ->selectRaw('RIGHT(no_trans, 5) AS last_notrans')
            ->whereRaw('DATE(created_at) = DATE(NOW())')
            ->orderByDesc('no_trans')
            ->orderByDesc('created_at')->limit(1)->first();
        // dd($lastNoTrans);
        if ($lastNoTrans) {
            $lastNoTrans = str_pad($lastNoTrans->last_notrans + 1, 5, "0", STR_PAD_LEFT);
        } else {
            $lastNoTrans = str_pad("1", 5, "0", STR_PAD_LEFT);
        }
        return $headNoTrans . $lastNoTrans;
    }

    /**
     * Display the specified resource.
     */
    public function struk($notrans)
    {
        $notrans = str_replace('-', '/', $notrans);
        $data = Penjualan::with('detailPenjualan.barang', 'user')->findOrFail($notrans);
        return view('kasir.struk', [
            'data' => $data,
        ]);
    }

    public function histori()
    {
        return view('kasir.histori');
    }

    public function getHistori(Request $request)
    {
        if ($request->ajax()) {
            $data = Penjualan::whereRaw('DATE(created_at) = DATE(NOW())')->orderByDesc('created_at')->paginate(20)->withQueryString();
            return response()->json([
                'data' => $data,
                'pagination' =>  (string) $data->onEachSide(1)->links('pagination.kasir-pagination', ['fontsize' => '13.5px'])
            ]);
        }
        abort(404, "Page Not Found");
    }

    public function getDetailHistori(Request $request, $notrans)
    {
        if ($request->ajax()) {
            $notrans = str_replace('-', "/", $notrans);
            $data = Penjualan::with('detailPenjualan.barang', 'user')->findOrFail($notrans);
            return response()->json($data);
        }
        abort(404, "Page Not Found");
    }
}
