<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
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

        $data = Kategori::orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%");
        if ($jumlahdata != 'all') {
            $data = $data->paginate($jumlahdata)->withQueryString();
        } else {
            if ($request->ajax()) {
                return response()->json(Kategori::orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%")->paginate(20));
            }
        }

        $datas = [
            'data' => $data,
            'nourut' => $nourut
        ];

        return view('barang.kategori', $datas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'unique:tkategori,nama|required|max:100',
        ], [
            'nama.required' => 'Kolom :attribute harus diisi',
            'nama.unique' => 'Kategori ":input" sudah ada',
            'nama.max' => 'Maksimal memasukan :max karakter pada :attribute',
        ], [
            'nama' => '"Nama Kategori"',
            'keterangan' => '"Keterangan"'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('show_modal_tambah', true);
        }

        $result = Kategori::create($request->all());
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Kategori \"' . $request->nama . '\" gagal ditambahkan']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Kategori \"' . $request->nama . '\" berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        if ($request->ajax()) {
            return response()->json(Kategori::select('id', 'nama as nama-edit', 'keterangan as keterangan-edit')->find($id));
        }
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
