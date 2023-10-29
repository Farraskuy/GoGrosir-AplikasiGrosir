<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class PenggunaController extends Controller
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

        $data = User::with('role')->orderBy($sortField, $sortOrder)
            ->where('username', 'like', "%$keyword%")
            ->where('nama', 'like', "%$keyword%");

        if ($jumlahdata != 'all') {
            $data = $data->paginate($jumlahdata)->withQueryString();
        } else {
            if ($request->ajax()) {
                $data = $data->paginate(20)->withQueryString()->toArray();
                $data['data'] = collect($data['data'])->map(function ($item) {
                    $newItem = [
                        'id' => $item['id'],
                        'username' => $item['username'],
                        'nama' => $item['nama'],
                        'jenis_kelamin' => $item['jenis_kelamin'],
                        'nomor_telepon' => $item['nomor_telepon'],
                        'role' => [
                            'nama' => $item['role']['nama'],
                            'isSuperAdmin' => isSuperAdmin($item['role']['id']),
                        ],
                    ];
                    return $newItem;
                });

                // $data = $data->paginate($jumlahdata)->withQueryString()->toArray();
                // $data['data'] = collect($data['data'])->map(function ($item) {
                //     $item['isSuperAdmin'] = isSuperAdmin($item['id']);
                //     return $item;
                // })->all();
                return response()->json($data);
            }
        }

        $datas = [
            'data' => $data,
            'nourut' => $nourut,
            'role' => Role::where('nama', '!=', 'super admin')->get()
        ];

        return view('Pengguna.Pengguna', $datas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'unique:users,username|required|max:100',
            'password' => 'required',
            'password_repeat' => 'required|same:password',
            'nama' => 'required|max:100',
            'nomor_telepon' => 'required',
            'role_id' => 'required|exists:roles,id',
            'jenis_kelamin' => 'required|in:L,P',
            'gambar' => File::image()->types(['jpg', 'jpeg', 'png'])->max('2mb')
        ], [
            '*.required' => 'Kolom :attribute harus diisi',
            '*.exists' => 'Harap pilih kolom :attribute dengan pilihan yang tersedia',
            '*.max' => 'Maksimal memasukan :max karakter pada :attribute',
            'username.unique' => 'Username ":input" sudah digunakan, Harap coba yang lain',
            'password.same' => 'Password tidak sama',
            'gambar.max' => 'Harap pilih gambar yang ukurannya tidak lebih dari 2mb',
            'gambar.mimes' => 'Harap pilih gambar yang bertype .jpg .jpeg .png',
        ], [
            'username' => '"Username"',
            'password' => '"Password"',
            'password_repeat' => '"Ulangi Password"',
            'nama' => '"Nama"',
            'nomor_telepon' => '"Nomor Telepon"',
            'jenis_kelamin' => '"Jenis Kelamin"',
            'role_id' => '"Role"',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('show_modal_tambah', true);
        }

        $request['foto'] = $request->jenis_kelamin == "L" ? "default.jpg" : "dafault_female.jpg";
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $request['foto'] = $fileName = $file->hashName();
            $file->storeAs('/upload', $fileName, 'public');
        }

        $request['password'] = Hash::make($request->password);
        $result = User::create($request->all());
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Pengguna \"' . $request->nama . '\" gagal ditambahkan']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Pengguna \"' . $request->nama . '\" berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function detail(Request $request, User $pengguna)
    {
        if ($request->ajax()) {
            return response()->json([
                'username' => $pengguna->username,
                'nama' => $pengguna->nama,
                'nomor_telepon' => $pengguna->nomor_telepon,
                'alamat' => $pengguna->alamat,
                'foto' => $pengguna->foto,
                'isSuperAdmin' => isSuperAdmin($pengguna->role_id),
                'jenis_kelamin' => $pengguna->jenis_kelamin == "L" ? 'Laki-laki' : 'Perempuan',
                'role' => Role::where('id', '=', $pengguna->role_id)->first()->nama
            ]);
        }
        abort(404, "Halaman Tidak Ditemukan");
    }

    public function show(Request $request, User $pengguna)
    {
        if ($request->ajax()) {
            return response()->json([
                'username' => $pengguna->username,
                'nama' => $pengguna->nama,
                'nomor_telepon' => $pengguna->nomor_telepon,
                'alamat' => $pengguna->alamat,
                'foto' => $pengguna->foto,
                'isSuperAdmin' => isSuperAdmin($pengguna->role_id),
                'input_select' => [
                    'role_id' => $pengguna->role_id,
                    'jenis_kelamin' => $pengguna->jenis_kelamin
                ],
            ]);
        }
        abort(404, "Halaman Tidak Ditemukan");
    }

    public function search(Request $request)
    {
        $pengguna = [];
        $search = $request->q;
        $pengguna = User::select("id", "nama")
            ->where('nama', 'LIKE', "%$search%")->orderBy('nama', 'asc')
            ->get();
        return response()->json($pengguna);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $pengguna)
    {
        $usernameLama = $pengguna->username;
        $usernameBaru = $request->username_edit;
        $rulesUsername = 'required|max:100';
        if ($usernameBaru != $usernameLama) {
            $rulesUsername .= '|unique:users,username';
        }
        $rulesRole = [];
        if (!Role::isSuperAdmin($pengguna->role_id_edit)) {
            $rulesRole = ['role_id' => 'exists:roles,id'];
        }

        $validator = Validator::make($request->all(), $rulesRole + [
            'username_edit' => $rulesUsername,
            'nama_edit' => 'required|max:100',
            'nomor_telepon_edit' => 'required',
            'jenis_kelamin_edit' => 'required|in:L,P',
            'gambar_edit' => File::image()->types(['jpg', 'jpeg', 'png'])->max('2mb')
        ], [
            '*.required' => 'Kolom :attribute harus diisi',
            '*.exists' => 'Harap pilih kolom :attribute dengan pilihan yang tersedia',
            '*.max' => 'Maksimal memasukan :max karakter pada :attribute',
            'username_edit.unique' => 'Username ":input" sudah digunakan, Harap coba yang lain',
            'gambar_edit.max' => 'Harap pilih gambar yang ukurannya tidak lebih dari 2mb',
            'gambar_edit.mimes' => 'Harap pilih gambar yang bertype .jpg .jpeg .png',
        ], [
            'username_edit' => '"Username"',
            'nama_edit' => '"Nama"',
            'nomor_telepon_edit' => '"Nomor Telepon"',
            'jenis_kelamin_edit' => '"Jenis Kelamin"',
            'role_id_edit' => '"Role"',
            'gambar_edit' => '"Gambar"',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with(['old_action' => url()->current(), 'show_modal_edit' => true]);
        }

        // dd($request->all());
        if (!isset($request['gambar_lama']) && $pengguna->foto != 'default.jpg' && $pengguna->foto != 'default_female.jpg') {
            $request['foto'] = $request->jenis_kelamin_edit == "L" ? "default.jpg" : "default_female.jpg";
            Storage::disk('public')->delete("upload/" . $pengguna->foto);
        } else if ($pengguna->foto != 'default.jpg' && $pengguna->foto != 'default_female.jpg') {
            $request['foto'] = $request['gambar_lama'];
        } else {
            $request['foto'] = $request->jenis_kelamin_edit == "L" ? "default.jpg" : "default_female.jpg";
        }

        if ($request->hasFile('gambar_edit')) {
            $file = $request->file('gambar_edit');
            $request['foto'] = $fileName = $file->hashName();
            $file->storeAs('/upload', $fileName, 'public');
            if ($pengguna->foto != 'default.jpg' && $pengguna->foto != 'default_female.jpg') {
                Storage::disk('public')->delete("upload/" . $pengguna->foto);
            }
        }

        if (Role::isSuperAdmin($pengguna->role_id)) {
            unset($request['role_id_edit']);
        }

        $data = [];
        foreach ($request->all() as $key => $value) {
            $data[str_replace("_edit", "", $key)] = $value;
        }

        $result = $pengguna->update($data);
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Data pengguna \"' . $usernameLama . '\" gagal diedit']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Data pengguna \"' . $usernameBaru . '\" berhasil diedit']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $pengguna)
    {
        if (Role::isSuperAdmin($pengguna->role_id)) {
            return redirect()->back()->with(['message_type' => 'warning', 'message' => 'Anda tidak dapat menghapus <strong>"Super Admin"</strong>']);
        }

        $gambar = $pengguna->foto;
        $result = $pengguna->delete();
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Pengguna \"' . $pengguna->nama . '\" gagal dihapus']);
        }
        if ($gambar != 'default.jpg' && $gambar != 'default_female.jpg') {
            Storage::disk('public')->delete('upload/' . $gambar);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Pengguna \"' . $pengguna->nama . '\" berhasil dihapus']);
    }
}
