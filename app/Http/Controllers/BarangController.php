<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterKategori = $request->query('filter_kategori', '');
        $filterSupplier = $request->query('filter_supplier', '');
        $sortField = $request->query('sorted_by', 'nama');
        $sortOrder = $request->query('ordered_by', 'asc');
        $jumlahdata = $request->query('showing', 20);
        $keyword = $request->query('keyword', '');

        $data = Barang::with('kategori', 'supplier')->orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%");
        if ($filterKategori) {
            $data = $data->where('id', $filterKategori);
        }
        if ($filterSupplier) {
            $data = $data->where('id', $filterSupplier);
        }
        if ($jumlahdata != 'all') {
            $data = $data->paginate($jumlahdata)->withQueryString();
        } else {
            if ($request->ajax()) {
                return response()->json(Barang::with('kategori', 'supplier')->orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%")->paginate(20));
            }
        }

        $datas = [
            'data' => $data,
            'supplier' => Supplier::orderBy('nama', 'asc')->get(),
            'kategori' => Kategori::orderBy('nama', 'asc')->get(),
        ];

        return view('barang.barang', $datas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'nama' => 'unique:tbarang,nama|required|max:100',
            'barcode' => 'unique:tbarang,barcode|max:20',
            'supplier' => 'exists:tsupplier,id|required',
            'kategori' => 'exists:tkategori,id|required',

            'harga_beli_grosir' => 'min:1|numeric|required',
            'harga_jual_grosir' => 'gt:harga_beli_grosir|numeric|required',
            'satuan_grosir' => 'required|max:100',

            'satuan_ecer' => 'max:100|required_if:with_eceran,on',
            'isi_barang' => 'numeric|required_if:with_eceran,on',
            'harga_beli_ecer' => 'numeric|required_if:with_eceran,on',
            'harga_jual_ecer' => 'gt:harga_beli_ecer|numeric|required_if:with_eceran,on',
        ], [
            '*.required' => 'Kolom :attribute harus diisi',
            '*.required_if' => 'Kolom :attribute harus diisi',
            'nama.unique' => 'Barang ":input" sudah di tambahkan',
            'barcode.unique' => 'Barang dengan Barcode ":input" sudah di tambahkan',
            '*.max' => 'Maksimal mengisi :max karakter pada :attribute',
            '*.min' => 'Tidak boleh memasukan :attribute di bawah 0',
            '*.exists' => 'Harap pilih :attribute yang ada dalam list',
            'harga_beli_grosir.gt' => 'Tidak boleh memasukan :attribute di bawah Harga Beli Grosir',
            'harga_jual_ecer.gt' => 'Tidak boleh memasukan :attribute di bawah Harga Beli Ecer',
            '*.numeric' => 'Kolom :attribute harus angka',
        ], [
            'nama' => '"Nama Barang"',
            'barcode' => '"Barcode"',
            'supplier' => '"Supplier"',
            'kategori' => '"Kategori"',
            'harga_beli_grosir' => '"Harga Beli Grosir"',
            'harga_beli_ecer' => '"Harga Beli Ecer"',
            'harga_jual_ecer' => '"Harga Jual Ecer"',
            'harga_jual_grosir' => '"Harga Jual Grosir"',
            'satuan_grosir' => '"Satuan"',
            'satuan_ecer' => '"Satuan Ecer"',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('show_modal_tambah', true)
                ->with('supplier', Supplier::find($request->supplier))
                ->with('kategori', Kategori::find($request->kategori));
        }

        $request['id_kategori'] = $request->kategori;
        $request['id_supplier'] = $request->supplier;

        $result = Barang::create($request->all());

        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Barang \"' . $request->nama . '\" gagal ditambahkan']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Barang \"' . $request->nama . '\" berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $barang)
    {
        if ($request->ajax()) {
            $data = Barang::with('kategori', 'supplier')->find($barang);
            return response()->json($data);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        // dd($request, $barang);
        $rulesNama = 'required|max:100';
        if ($request->get('nama-edit') !=  $barang->nama) {
            $rulesNama .= '|unique:tbarang,nama';
        }

        $rulesBarcode = 'required|max:20';
        if ($request->get('barcode-edit') != $barang->barcode) {
            $rulesBarcode .= '|unique:tbarang,barcode';
        }

        $validator = Validator::make($request->all(), [
            'nama-edit' => $rulesNama,
            'barcode-edit' => $rulesBarcode,
            'supplier-edit' => 'exists:tsupplier,id|required',
            'kategori-edit' => 'exists:tkategori,id|required',

            'harga_beli_grosir-edit' => 'min:1|numeric|required',
            'harga_jual_grosir-edit' => 'gt:harga_beli_grosir-edit|numeric|required',
            'satuan_grosir-edit' => 'required|max:100',

            'satuan_ecer-edit' => 'max:100|required_if:with_eceran,on',
            'isi_barang-edit' => 'numeric|required_if:with_eceran,on',
            'harga_beli_ecer-edit' => 'numeric|required_if:with_eceran,on',
            'harga_jual_ecer-edit' => 'gt:harga_beli_ecer-edit|numeric|required_if:with_eceran,on',
        ], [
            '*.required' => 'Kolom :attribute harus diisi',
            '*.required_if' => 'Kolom :attribute harus diisi',
            'nama-edit.unique' => 'Barang ":input" sudah di tambahkan',
            'barcode-edit.unique' => 'Barang dengan Barcode ":input" sudah di tambahkan',
            '*.max' => 'Maksimal mengisi :max karakter pada :attribute',
            '*.min' => 'Tidak boleh memasukan :attribute di bawah 0',
            '*.exists' => 'Harap pilih :attribute yang ada dalam list',
            'harga_jual_grosir-edit.gt' => 'Tidak boleh memasukan :attribute di bawah Harga Beli Grosir',
            'harga_jual_ecer-edit.gt' => 'Tidak boleh memasukan :attribute di bawah Harga Beli Ecer',
            '*.numeric' => 'Kolom :attribute harus angka',
        ], [
            'nama-edit' => '"Nama Barang"',
            'barcode-edit' => '"Barcode"',
            'supplier-edit' => '"Supplier"',
            'kategori-edit' => '"Kategori"',
            'harga_beli_grosir-edit' => '"Harga Beli Grosir"',
            'harga_beli_ecer-edit' => '"Harga Beli Ecer"',
            'harga_jual_ecer-edit' => '"Harga Jual Ecer"',
            'harga_jual_grosir-edit' => '"Harga Jual Grosir"',
            'satuan_grosir-edit' => '"Satuan"',
            'satuan_ecer-edit' => '"Satuan Ecer"',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()
                ->with('old_action', url()->current())
                ->with('show_modal_edit', true)
                ->with('supplier-edit', Supplier::find($request->get('supplier-edit')))
                ->with('kategori-edit', Kategori::find($request->get('kategori-edit')));
        }

        $data = [
            'nama' => $request->get('nama-edit'),
            'barcode' => $request->get('barcode-edit'),
            'id_supplier' => $request->get('supplier-edit'),
            'id_kategori' => $request->get('kategori-edit'),
            'harga_beli_grosir' => $request->get('harga_beli_grosir-edit'),
            'harga_jual_grosir' => $request->get('harga_jual_grosir-edit'),
            'satuan_grosir' => $request->get('satuan_grosir-edit'),
        ];

        // dump($request->all());
        if ($request->get('with_eceran-edit') == 'on') {
            $data = array_merge($data, [
                'with_eceran' => $request->get('with_eceran-edit'),
                'isi_barang' => $request->get('isi_barang-edit'),
                'harga_beli_ecer' => $request->get('harga_beli_ecer-edit'),
                'harga_jual_ecer' => $request->get('harga_jual_ecer-edit'),
                'satuan_ecer' => $request->get('satuan_ecer-edit'),
            ]);
        }
        // dd($request->get('with_eceran-edit'), $data);

        $result = $barang->update($data);

        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Data barang \"' . $request->get('nama-edit') . '\" gagal diedit']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Data barang \"' . $request->get('nama-edit') . '\" berhasil diedit']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        $result = $barang->delete();
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Barang \"' . $barang->nama . '\" gagal dihapus']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Barang \"' . $barang->nama . '\" berhasil dihapus']);
    }
}
