<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortField = $request->query('filtered_by', 'id');
        $sortOrder = $request->query('ordered_by', 'asc');
        $jumlahdata = $request->query('showing', 20);
        $keyword = $request->query('keyword', '');
        $currentPage = $request->query('page', 1);

        if (is_numeric($jumlahdata)) {
            $nourut = ($currentPage - 1) * $jumlahdata + 1;
        } else {
            $nourut = 1;
        }

        $data = Supplier::orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%");
        if ($jumlahdata != 'all') {
            $data = $data->paginate($jumlahdata)->withQueryString();
        } else {
            if ($request->ajax()) {
                return response()->json(Supplier::orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%")->paginate(20));
            }
        }
        $datas = [
            'data' => $data,
            'nourut' => $nourut
        ];

        return view('barang.supplier', $datas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'unique:tsupplier,nama|required|max:100',
            'nomor-telepon' => 'numeric|required|max_digits:20',
            'alamat' => 'required',
        ], [
            '*.required' => 'Kolom :attribute harus diisi',
            'nama.unique' => 'Supplier ":input" sudah di tambahkan',
            'nama.max' => 'Maksimal memasukan :max karakter pada :attribute',
            'nomor-telepon.numeric' => 'Kolom :attribute harus berupa angka tanpa spasi dan huruf',
            'nomor-telepon.max_digits' => 'Maksimal memasukan :max_digits karakter pada :attribute',
        ], [
            'nama' => '"Nama Supplier"',
            'nomor-telepon' => '"Nomor Telepon"',
            'alamat' => '"Alamat"',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('show_modal_tambah', true);
        }

        $result = Supplier::create([
            'nama' => $request->get('nama'),
            'nomor_telepon' => $request->get('nomor-telepon'),
            'alamat' => $request->get('alamat'),
        ]);
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Supplier <strong>\"' . $request->nama . '\"</strong> gagal ditambahkan']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Supplier <strong>\"' . $request->nama . '\"</strong> berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $supplier)
    {
        if ($request->ajax()) {
            return response()->json(Supplier::select('id', 'nama as nama-edit', 'nomor_telepon as nomor-telepon-edit', 'alamat as alamat-edit')->find($supplier));
        }
    }

    public function search(Request $request)
    {
        $supplier = [];
        $search = $request->q;
        $supplier = Supplier::select("id", "nama")
            ->where('nama', 'LIKE', "%$search%")->orderBy('nama', 'asc')
            ->get();
        return response()->json($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $namaLama = $supplier->nama;
        $namaBaru = $request->get('nama-edit');
        $rulesNama = 'required|max:100';
        if ($namaBaru != $namaLama) {
            $rulesNama .= '|unique:tsupplier,nama';
        }
        $validator = Validator::make($request->all(), [
            'nama-edit' => $rulesNama,
            'nomor-telepon-edit' => 'numeric|required|max_digits:20',
            'alamat-edit' => 'required',
        ], [
            '*.required' => 'Kolom :attribute harus diisi',
            'nama-edit.unique' => 'Supplier ":input" sudah di tambahkan',
            'nama-edit.max' => 'Maksimal memasukan :max karakter pada :attribute',
            'nomor-telepon-edit.numeric' => 'Kolom :attribute harus berupa angka',
            'nomor-telepon-edit.max_digits' => 'Maksimal memasukan :max_digits karakter pada :attribute',
        ], [
            'nama-edit' => '"Nama Supplier"',
            'nomor-telepon-edit' => '"Nomor Telepon"',
            'alamat-edit' => '"Alamat"',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('old_action', url()->current());
        }

        $result = $supplier->update([
            'nama' => $request->get('nama-edit'),
            'nomor_telepon' => $request->get('nomor-telepon-edit'),
            'alamat' => $request->get('alamat-edit'),
        ]);

        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Data supplier <strong>\"' . $namaLama . '\"</strong> gagal diedit']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Data supplier <strong>\"' . $namaLama . '\"</strong> berhasil diedit']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $result = $supplier->delete();
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Supplier <strong>\"' . $supplier->nama . '\"</strong> gagal dihapus']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Supplier <strong>\"' . $supplier->nama . '\"</strong> berhasil dihapus']);
    }
}
