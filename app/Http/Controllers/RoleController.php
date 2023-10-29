<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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

        $data = Role::with('permissions')->withCount('permissions', 'users')->orderBy($sortField, $sortOrder)->where('nama', 'like', "%$keyword%");
        if ($jumlahdata != 'all') {
            $data = $data->paginate($jumlahdata)->withQueryString();
        } else {
            $jumlahdata = 20;
            if ($request->ajax()) {
                $data = $data->paginate($jumlahdata)->withQueryString()->toArray();
                $data['data'] = collect($data['data'])->map(function ($item) {
                    $item['isSuperAdmin'] = isSuperAdmin($item['id']);
                    return $item;
                })->all();
                return response()->json($data);
            }
        }

        $datas = [
            'data' => $data,
            'nourut' => $nourut,
        ];

        return view('pengguna.role', $datas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'unique:roles,nama|required|max:100',
            'keterangan' => 'required|max:255',
            'permission' => 'required',
        ], [
            'permission.required' => 'Harap pilih minimal 1 izin yang ingin di berikan kepada role',
            '*.required' => 'Kolom :attribute harus diisi',
            'nama.unique' => 'Role ":input" sudah ada',
            'nama.max' => 'Maksimal memasukan :max karakter pada :attribute',
        ], [
            'nama' => '"Nama Role"',
            'keterangan' => '"Keterangan"',
            'permission' => '"Izin"'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('show_modal_tambah', true);
        }

        DB::beginTransaction();
        try {
            $result = Role::create($request->all());
            $roleID = $result->id;
            foreach ($request['permission'] as $namaIzin => $izin) {
                if (!is_array($izin)) {
                    RolePermission::create([
                        'role_id' => $roleID,
                        'permission_id' => Permission::where('nama', '=', $namaIzin)->first()->id
                    ]);
                    continue;
                }
                foreach ($izin as $detailIzin) {
                    RolePermission::create([
                        'role_id' => $roleID,
                        'permission_id' => Permission::where('nama', '=', $detailIzin . "_" . $namaIzin)->first()->id
                    ]);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'message_type' => 'danger',
                'message' => 'Role <strong>\"' . $request->nama . '\"</strong> gagal ditambahkan, Karna Error : "' . $e->getMessage() . '"'
            ]);
        }
        DB::commit();
        return redirect()->back()->with([
            'message_type' => 'success',
            'message' => 'Role <strong>\"' . $request->nama . '\"</strong> berhasil ditambahkan'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        if ($request->ajax()) {
            $role = Role::with('permissions', 'users')->findOrFail($id);

            return response()->json([
                'nama' => $role->nama,
                'keterangan' => $role->keterangan,
                'permissions' => $role->permissions->map->only(['nama']),
                'jumlah_user' => count($role->users)
            ]);
        }
        abort(404, "Halaman Tidak Ditemukan");
    }

    public function search(Request $request)
    {
        $role = [];
        $search = $request->q;
        $role = Role::select("id", "nama")
            ->where('nama', 'LIKE', "%$search%")->orderBy('nama', 'asc')
            ->get();
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->isSuperAdmin($role->id)) {
            return redirect()->back()->with(['message_type' => 'warning', 'message' => 'Maaf tetapi Role <strong>\"Super Admin\"</strong> tidak dapat diedit']);
        }

        $namaLama = $role->nama;
        $namaBaru = $request->nama_edit;
        $rulesNama = 'required|max:100';
        if ($namaBaru != $namaLama) {
            $rulesNama .= '|unique:trole,nama';
        }

        $validator = Validator::make($request->all(), [
            'nama_edit' => $rulesNama,
            'keterangan_edit' => 'required|max:255',
            'permission_edit' => 'required',
        ], [
            'permission_edit.required' => 'Harap pilih minimal 1 izin yang ingin di berikan kepada role',
            '*.required' => 'Kolom :attribute harus diisi',
            'nama.unique' => 'Role ":input" sudah ada',
            'nama.max' => 'Maksimal memasukan :max karakter pada :attribute',
        ], [
            'nama' => '"Nama Role"',
            'keterangan' => '"Keterangan"',
            'permission_edit' => '"Izin"'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with([
                'show_modal_edit' => true,
                'old_action' => $request->url()
            ]);
        }

        $data = [];
        foreach ($request->all() as $key => $value) {
            $data[str_replace("_edit", "", $key)] = $value;
        }

        DB::beginTransaction();
        try {
            $role->update($data);

            DB::table('role_permissions')->where('role_id', '=', $role->id)->delete();
            
            $roleID = $role->id;
            foreach ($data['permission'] as $namaIzin => $izin) {
                if (!is_array($izin)) {
                    RolePermission::create([
                        'role_id' => $roleID,
                        'permission_id' => Permission::where('nama', '=', $namaIzin)->first()->id
                    ]);
                    continue;
                }
                foreach ($izin as $detailIzin) {
                    RolePermission::create([
                        'role_id' => $roleID,
                        'permission_id' => Permission::where('nama', '=', $detailIzin . "_" . $namaIzin)->first()->id
                    ]);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'message_type' => 'danger',
                'message' => 'Role \"' . $request->nama_edit . '\" gagal diedit, <br> Karna <strong>Error</strong> : "' . $e->getMessage() . '"'
            ]);
        }
        DB::commit();
        return redirect()->back()->with([
            'message_type' => 'success',
            'message' => 'Role <strong>\"' . $request->nama_edit . '\"</strong> berhasil diedit'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->users()->count() != 0) {
            return redirect()->back()->with(['message_type' => 'warning', 'message' => 'Maaf Role <strong>\"'. $role->nama .'\"</strong> tidak dapat dihapus, Karna role ini digunakan oleh <strong>'. $role->users()->count() .' Pengguna</strong>. <br><b>Solusi</b> : Harap pastikan tidak ada pengguna yang menggunakan Role ini sebelum menghapusnya']);
        }
        if ($role->isSuperAdmin($role->id)) {
            return redirect()->back()->with(['message_type' => 'warning', 'message' => 'Maaf Role <strong>\"Super Admin\"</strong> tidak dapat dihapus']);
        }

        $result = $role->delete();
        if (!$result) {
            return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Role <strong>\"' . $role->nama . '\"</strong> gagal dihapus']);
        }
        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Role <strong>\"' . $role->nama . '\"</strong> berhasil dihapus']);
    }
}
