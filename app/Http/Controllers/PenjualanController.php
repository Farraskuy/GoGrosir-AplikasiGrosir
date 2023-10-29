<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Kategori;
use App\Models\Penjualan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tanggal_awal = $request->query('tanggal_awal', '');
        $tanggal_akhir = $request->query('tanggal_akhir', '');
        $sortField = $request->query('filtered_by', 'no_trans');
        $sortOrder = $request->query('ordered_by', 'asc');
        $jumlahdata = $request->query('showing', 20);
        $keyword = $request->query('keyword', '');
        $currentPage = $request->query('page', 1);

        if (is_numeric($jumlahdata)) {
            $nourut = ($currentPage - 1) * $jumlahdata + 1;
        } else {
            $nourut = 1;
        }

        $data = Penjualan::with('user')->where('no_trans', 'like', "%$keyword%")->orderBy($sortField, $sortOrder);

        if ($tanggal_awal != '') {
            $data = $data->where('created_at', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir != '') {
            $data = $data->where('created_at', '<=', $tanggal_akhir);
        }

        if ($jumlahdata != 'all') {
            $data = $data->paginate($jumlahdata)->withQueryString();
        } else {
            if ($request->ajax()) {
                return response()->json($data->paginate(20)->withQueryString());
            }
        }

        $datas = [
            'data' => $data,
            'nourut' => $nourut
        ];

        return view('transaksi.penjualan', $datas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('transaksi.tambah-penjualan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, "Maaf anda tidak memiliki izin untuk mengakses halaman ini");
        }
        
        $requests = $request->all();
        $totalBayar =  $request->collect('harga_jual')
            ->map(function ($hargaJual, $key) use ($requests) {
                return $hargaJual * $requests['jumlah_beli'][$key];
            })
            ->sum();

        $totalPotongan = $request->collect('potongan')->sum();
        $bayar = $request['bayar'];
        $kembalian = $bayar - ($totalBayar - $totalPotongan);

        $notrans = KasirController::generateNoTrans();

        // $head =  [
        //     'no_trans' => $notrans,
        //     'id_petugas' => Auth::id(),
        //     'total_bayar' => $totalBayar,
        //     'total_potongan' => $totalPotongan,
        //     'bayar' => $bayar,
        //     'kembalian' => $kembalian
        // ];

        // $detail = [];
        // foreach ($requests['id_barang'] as $key => $value) {
        //     array_push($detail, [
        //         'no_trans' => $notrans,
        //         'id_barang' => $value,
        //         'type_beli' => $requests['type_beli'][$key],
        //         'harga_beli' => $requests['harga_beli'][$key],
        //         'harga_jual' => $requests['harga_jual'][$key],
        //         'jumlah_beli' => $requests['jumlah_beli'][$key],
        //         'potongan' => $requests['potongan'][$key],
        //         'subtotal' => ($requests['harga_jual'][$key] * $requests['jumlah_beli'][$key]) - $requests['potongan'][$key]
        //     ]);
        // }

        // return response()->json(['tohead' => $head, 'todetail' => $detail]);

        DB::beginTransaction();
        try {
            Penjualan::create([
                'no_trans' => $notrans,
                'id_petugas' => Auth::id(),
                'total_bayar' => $totalBayar,
                'total_potongan' => $totalPotongan,
                'bayar' => $bayar,
                'kembalian' => $kembalian
            ]);

            foreach ($requests['id_barang'] as $key => $value) {
                DetailPenjualan::create([
                    'no_trans' => $notrans,
                    'id_barang' => $value,
                    'satuan_beli' => $requests['satuan'][$key],
                    'harga_beli' => $requests['harga_beli'][$key],
                    'harga_jual' => $requests['harga_jual'][$key],
                    'jumlah_beli' => $requests['jumlah_beli'][$key],
                    'potongan' => $requests['potongan'][$key],
                    'subtotal' => ($requests['harga_jual'][$key] * $requests['jumlah_beli'][$key]) - $requests['potongan'][$key]
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message_type' => 'error',
                'message' => 'Transaksi gagal, Error : ' . $e->getMessage(), 
                'oldRequestBody' => $request->all()]);
        }
        DB::commit();
        return response()->json([
            'message_type' => 'success', 
            'message' => 'Transaksi Berhasil', 
            'no_trans' => $notrans, 
            'kembalian' => $kembalian,
            'selalu_cetak' => AppSetting::all(['selalu_cetak_struk'])->first()->selalu_cetak_struk
        ]);
    }
    
    public function searchBarang(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->query('search', "");
            return response()->json([
                "barang" => Barang::where('nama', 'like', "%$search%")->orWhere('barcode', 'like', "%$search%")->limit(5)->get(),
                "search" => $search,
            ]);
        }
        abort(403, "Halaman Tidak Bisa Diakses");
    }

    public function getDataBarang(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->query('search', "");
            $jumlah_data = $request->query('jumlahdata', 20);
            $jumlah_data = $jumlah_data != "all" ? $jumlah_data : 20;
            $barang = Barang::whereNotIn("id", explode(",", $request->get('selected', "")))
                ->where('nama', 'like', "%$search%")
                ->paginate($jumlah_data)->withQueryString();

            return response()->json([
                "barang_terpilih" => Barang::whereIn("id", explode(",", $request->get('selected', "")))->get(),
                "barang" => $barang,
                "pagination" => (string) $barang->onEachSide(1)->links('pagination.penjualan-pagination', ['fontsize' => '13.5px'])
            ]);
        }
        abort(403, "Halaman Tidak Bisa Diakses");
    }

    public function search(Request $request)
    {
        $kategori = [];
        $search = $request->q;
        $kategori = Kategori::select("id", "nama")
            ->where('nama', 'LIKE', "%$search%")->orderBy('nama', 'asc')
            ->get();
        return response()->json($kategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $namaLama = $kategori->nama;
        $namaBaru = $request->get('nama-edit');
        $rulesNama = 'required|max:100';
        if ($namaBaru != $namaLama) {
            $rulesNama .= '|unique:tkategori,nama';
        }
        $validator = Validator::make($request->all(), [
            'nama-edit' => $rulesNama,
        ], [
            'nama-edit.required' => 'Kolom :attribute harus diisi',
            'nama-edit.unique' => 'Kategori ":input" sudah di tambahkan',
            'nama-edit.max' => 'Maksimal memasukan :max karakter pada :attribute',
        ], [
            'nama-edit' => '"Nama Kategori"'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('old_action', url()->current());
        }

        $result = $kategori->update([
            'nama' => $request->get('nama-edit'),
            'keterangan' => $request->get('keterangan-edit')
        ]);
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Data kategori \"' . $namaLama . '\" gagal diedit']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Data kategori \"' . $namaBaru . '\" berhasil diedit']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        $result = $kategori->delete();
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Kategori \"' . $kategori->nama . '\" gagal dihapus']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Kategori \"' . $kategori->nama . '\" berhasil dihapus']);
    }
}
