@extends('layout')

@section('title', 'Role | ' . nama_aplikasi())

@section('content')
    <section class="p-3">
        <h4 class="fw-semibold">Daftar Role</h4>
        <button class="btn btn-sm btn-success fw-semibold mb-3" data-bs-toggle="modal" data-bs-target="#modaltambah">Tambah Data</button>
        <div class="row g-0 gap-3">
            <form method="get" onchange="filterData(this)" class="col rounded-3 bg-white p-3 pt-0" style="height: fit-content">
                <div class="alert-container"></div>
                <div class="bg-white position-sticky pt-3 pb-2" style="top: 61px">
                    <div class="d-flex gap-2 justify-content-end mb-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari" value="{{ request()->query('keyword', '') }}" name="keyword" oninput="searchData(this)">
                        <select class="form-select fs-14px w-auto h-100" style="line-height: 1.7" name="filtered_by">
                            <option value="" {{ request()->query('filtered_by') == '' ? 'selected' : '' }}>Urutkan berdasarkan</option>
                            <option value="id" {{ request()->query('filtered_by') == 'id' ? 'selected' : '' }}>Nomor Urut</option>
                            <option value="nama" {{ request()->query('filtered_by') == 'nama' ? 'selected' : '' }}>Nama</option>
                            <option value="permissions_count" {{ request()->query('filtered_by') == 'permissions_count' ? 'selected' : '' }}>Jumlah Izin</option>
                        </select>
                        <label for="desc" class="btn btn border {{ request()->query('ordered_by') == 'desc' ? 'd-none' : '' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Dari atas ke bawah"><i class="fa-solid fa-arrow-down-short-wide"></i></label>
                        <input type="radio" name="ordered_by" {{ request()->query('ordered_by') == 'desc' ? 'selected' : '' }} value="desc" id="desc" hidden>
                        <label for="asc" class="btn btn border {{ request()->query('ordered_by') == '' || request()->query('ordered_by') == 'asc' ? 'd-none' : '' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Dari bawah ke atas"><i class="fa-solid fa-arrow-up-wide-short"></i></label>
                        <input type="radio" name="ordered_by" {{ request()->query('ordered_by') == '' || request()->query('ordered_by') == 'asc' ? 'selected' : '' }} value="asc" id="asc" hidden>
                    </div>
                    @if (request()->query('showing') == 'all')
                        <div class="d-flex justify-content-between gap-2 flex-wrap">
                            <div class="d-flex fs-14px align-items-center gap-1">
                                Menampilkan
                                <select class="form-select form-select-sm w-auto" name="showing">
                                    <option {{ request()->query('showing') == '10' ? 'selected' : '' }}>10</option>
                                    <option {{ request()->query('showing') == '' || request()->query('showing') == '20' ? 'selected' : '' }}>
                                        20</option>
                                    <option {{ request()->query('showing') == '50' ? 'selected' : '' }}>50</option>
                                    <option {{ request()->query('showing') == '100' ? 'selected' : '' }}>100</option>
                                    <option {{ request()->query('showing') == 'all' ? 'selected' : '' }} value="all">Semua
                                    </option>
                                </select>
                                Data
                            </div>
                        </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover fs-14px">
                        <thead>
                            <tr>
                                <th class="fit" scope="col">#</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Jumlah Izin</th>
                                <th class="text-center" scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($data as $item)
                                <tr>
                                    <th class="fit" scope="row">{{ $nourut++ }}</th>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->permissions_count }}</td>
                                    <td class="fit">
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-primary fw-semibold fs-13px flex-grow-1" data-bs-toggle="modal" data-bs-target="#modaldetail" data-bs-id="{{ $item->id }}">Detail</button>
                                            @if (!isSuperAdmin($item->id))
                                                <button type="button" class="btn btn-sm btn-warning text-white fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modaledit" data-bs-id="{{ $item->id }}">Edit</button>
                                                <button type="button" class="btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="{{ $item->id }}" data-bs-nama="{{ $item->nama }}">Hapus</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <script>
                                function rowData(data, nourut) {
                                    console.log(data);
                                    const tr = document.createElement("tr");
                                    const otherButton = `
                                        <button type="button" class="btn btn-sm btn-warning text-white fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modaledit" data-bs-id="${ data.id }">Edit</button>
                                        <button type="button" class="btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="${ data.id }" data-bs-nama="${ data.nama }">Hapus</button>
                                    `;
                                    tr.innerHTML = `
                                        <th class="fit" scope="row">${ nourut }</th>
                                        <td>${ data.nama }</td>
                                        <td>${ data.keterangan }</td>
                                        <td>${ data.permissions_count }</td>
                                        <td class="fit">
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-primary fw-semibold fs-13px flex-grow-1" data-bs-toggle="modal" data-bs-target="#modaldetail" data-bs-id="${ data.id }">Detail</button>
                                                ${!data.isSuperAdmin ? otherButton : ''}
                                            </div>
                                        </td>
                                    `;
                                    return tr;
                                }
                            </script>
                        </tbody>
                    </table>
                </div>
                @if (request()->query('showing') != 'all')
                    <div class="d-flex justify-content-between gap-2 flex-wrap">
                        <div class="d-flex fs-14px align-items-center gap-1">
                            Menampilkan
                            <select class="form-select form-select-sm w-auto" name="showing">
                                <option {{ request()->query('showing') == '10' ? 'selected' : '' }}>10</option>
                                <option {{ request()->query('showing') == '' || request()->query('showing') == '20' ? 'selected' : '' }}>20</option>
                                <option {{ request()->query('showing') == '50' ? 'selected' : '' }}>50</option>
                                <option {{ request()->query('showing') == '100' ? 'selected' : '' }}>100</option>
                                <option {{ request()->query('showing') == 'all' ? 'selected' : '' }} value="all">Semua</option>
                            </select>
                            Data
                        </div>
                        <div class="paginate">
                            {{ $data->onEachSide(1)->links('pagination.custom-pagination') }}
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal modal-xl fade" id="modaltambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ Session::get('old_action') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-0">
                        <div class="position-relative col-4 pe-2 me-2 border-end">
                            <div class="position-sticky" style="top: 0">
                                <div class="mb-2">
                                    <label for="nama" class="form-label fs-14px">Nama Role</label>
                                    <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}">
                                    <div class="invalid-feedback">
                                        @error('nama')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label fs-14px">Keterangan</label>
                                    <textarea type="text" class="form-control form-control-sm @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan">{{ old('keterangan') }}</textarea>
                                    <div class="invalid-feedback">
                                        @error('keterangan')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    <small class="fw-semibold"><i class="fa-regular fa-lightbulb me-2"></i>Informasi</small>
                                    <hr class="my-2">
                                    <ul class="fs-14px">
                                        <li>Harap berikan izin terhadap role secara bijak dan teliti</li>
                                        <li>Jika salah satu aksi <strong>Tambah, Edit, dan Hapus Data</strong> dipilih, Otomatis aksi <strong>lihat data</strong> akan ikut terpilih</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col d-flex flex-wrap gap-2">
                            <div class="w-100 mb-2">
                                <h1 class="fs-6 m-0">Izin Role</h1>
                                <span class="fs-14px text-secondary">Ceklist Izin yang tersedia untuk memberikan suatu izin kepada role</span>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger w-100">
                                    <small class="fw-semibold"><i class="fa-regular fa-lightbulb me-2"></i>Peringatan</small>
                                    <hr class="my-2">
                                    <ul class="fs-14px m-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="w-100 border rounded p-2 px-3">
                                <label for="all_izin" class="d-flex align-items-center gap-2 form-label fs-14px m-0">
                                    <input class="form-checkbox all_izin" type="checkbox" id="all_izin" @checked(empty(array_diff(old('permission.kategori', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && empty(array_diff(old('permission.supplier', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && empty(array_diff(old('permission.penjualan', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && Arr::has(old('permission', ['']), ['kasir', 'dashboard', 'laporan', 'pengaturan']))>
                                    <i class="fa-duotone fa-spinner-third fa-spin d-none"></i>
                                    Izinkan Semua Aksi Yang Ada
                                </label>

                                <div class="custom-modal hide modalkonfirmasi position-fixed d-flex align-items-center justify-content-center" style="top: 0; bottom: 0; left: 0; right: 0; z-index: 1; background-color: rgba(0, 0, 0, 0.2)">
                                    <div class="bg-white p-3 rounded">
                                        <div class="border-bottom pb-2">
                                            <h5 class="modal-title fs-6" id="staticBackdropLabel">Konfirmasi Pemberian Semua Izin</h5>
                                        </div>
                                        <div class="modal-body">
                                            <p class="m-0">Apakah anda yakin ingin memberikan semua izin yang tersedia untuk role ini?</p>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="rounded btn-cancle fw-semibold btn btn-sm btn-secondary">Batal</button>
                                            <button type="button" class="rounded btn-confirm fw-semibold btn btn-sm btn-success">Ya, Berikan</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Barang</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[barang][]" value="lihat" id="lihat_barang" @checked(in_array('lihat', old('permission.barang', [])))>
                                    <label class="form-check-label" for="lihat_barang">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[barang][]" value="tambah" id="tambah_barang" @checked(in_array('tambah', old('permission.barang', [])))>
                                    <label class="form-check-label" for="tambah_barang">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[barang][]" value="edit" id="edit_barang" @checked(in_array('edit', old('permission.barang', [])))>
                                    <label class="form-check-label" for="edit_barang">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[barang][]" value="hapus" id="hapus_barang" @checked(in_array('hapus', old('permission.barang', [])))>
                                    <label class="form-check-label" for="hapus_barang">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_barang" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_barang" @checked(empty(array_diff(old('permission.barang', ['']), ['lihat', 'tambah', 'edit', 'hapus'])))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Kategori</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[kategori][]" value="lihat" id="lihat_kategori" @checked(in_array('lihat', old('permission.kategori', [])))>
                                    <label class="form-check-label" for="lihat_kategori">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[kategori][]" value="tambah" id="tambah_kategori" @checked(in_array('tambah', old('permission.kategori', [])))>
                                    <label class="form-check-label" for="tambah_kategori">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[kategori][]" value="edit" id="edit_kategori" @checked(in_array('edit', old('permission.kategori', [])))>
                                    <label class="form-check-label" for="edit_kategori">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[kategori][]" value="hapus" id="hapus_kategori" @checked(in_array('hapus', old('permission.kategori', [])))>
                                    <label class="form-check-label" for="hapus_kategori">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_kategori" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_kategori" @checked(empty(array_diff(old('permission.kategori', ['']), ['lihat', 'tambah', 'edit', 'hapus'])))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Supplier</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[supplier][]" value="lihat" id="lihat_supplier" @checked(in_array('lihat', old('permission.supplier', [])))>
                                    <label class="form-check-label" for="lihat_supplier">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[supplier][]" value="tambah" id="tambah_supplier" @checked(in_array('tambah', old('permission.supplier', [])))>
                                    <label class="form-check-label" for="tambah_supplier">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[supplier][]" value="edit" id="edit_supplier" @checked(in_array('edit', old('permission.supplier', [])))>
                                    <label class="form-check-label" for="edit_supplier">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[supplier][]" value="hapus" id="hapus_supplier" @checked(in_array('hapus', old('permission.supplier', [])))>
                                    <label class="form-check-label" for="hapus_supplier">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_supplier" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_supplier" @checked(empty(array_diff(old('permission.supplier', ['']), ['lihat', 'tambah', 'edit', 'hapus'])))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Penjualan</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[kasir]" id="kasir" @checked(Arr::exists(old('permission', []), 'kasir'))>
                                    <label class="form-check-label" for="kasir">Izin Mengakses Halaman Kasir</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[penjualan][]" value="lihat" id="lihat_penjualan" @checked(in_array('hapus', old('permission.penjualan', [])))>
                                    <label class="form-check-label" for="lihat_penjualan">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[penjualan][]" value="tambah" id="tambah_penjualan" @checked(in_array('hapus', old('permission.penjualan', [])))>
                                    <label class="form-check-label" for="tambah_penjualan">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[penjualan][]" value="edit" id="edit_penjualan" @checked(in_array('hapus', old('permission.penjualan', [])))>
                                    <label class="form-check-label" for="edit_penjualan">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[penjualan][]" value="hapus" id="hapus_penjualan" @checked(in_array('hapus', old('permission.penjualan', [])))>
                                    <label class="form-check-label" for="hapus_penjualan">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_penjualan" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_penjualan" @checked(empty(array_diff(old('permission.penjualan', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && Arr::exists(old('permission', []), 'kasir'))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Izin Lain-lain</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[dashboard]" id="dashboard" @checked(Arr::exists(old('permission', []), 'dashboard'))>
                                    <label class="form-check-label" for="dashboard">Izin Mengakses Halaman Dashboard</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[laporan]" id="laporan" @checked(Arr::exists(old('permission', []), 'laporan'))>
                                    <label class="form-check-label" for="laporan">Laporan</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission[pengaturan]" id="pengaturan" @checked(Arr::exists(old('permission', []), 'pengaturan'))>
                                    <label class="form-check-label" for="pengaturan">Pengaturan Aplikasi</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_lain_lain" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_lain_lain" @checked(Arr::has(old('permission', ['']), ['dashboard', 'laporan', 'pengaturan']))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-success">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal modal-xl fade" id="modaledit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <form class="modal-content" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body position-relative">
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white loading" style="z-index: 1; top: 0; bottom: 0; right: 0; left: 0;">
                        <div class="p-3 fs-1"><i class="fa-duotone fa-spinner-third fa-spin"></i></div>
                    </div>
                    <div class="row g-0">
                        <div class="position-relative col-4 pe-2 me-2 border-end">
                            <div class="position-sticky" style="top: 0">
                                <div class="mb-2">
                                    <label for="nama_edit" class="form-label fs-14px">Nama Role</label>
                                    <input type="text" class="form-control form-control-sm @error('nama_edit') is-invalid @enderror" id="nama_edit" name="nama_edit" value="{{ old('nama_edit') }}">
                                    <div class="invalid-feedback">
                                        @error('nama_edit')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan_edit" class="form-label fs-14px">Keterangan</label>
                                    <textarea type="text" class="form-control form-control-sm @error('keterangan_edit') is-invalid @enderror" id="keterangan_edit" name="keterangan_edit">{{ old('keterangan_edit') }}</textarea>
                                    <div class="invalid-feedback">
                                        @error('keterangan_edit')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    <small class="fw-semibold"><i class="fa-regular fa-lightbulb me-2"></i>Informasi</small>
                                    <hr class="my-2">
                                    <ul class="fs-14px">
                                        <li>Harap berikan izin terhadap role secara bijak dan teliti</li>
                                        <li>Jika salah satu aksi <strong>Tambah, Edit, dan Hapus Data</strong> dipilih, Otomatis aksi <strong>lihat data</strong> akan ikut terpilih</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col d-flex flex-wrap gap-2">
                            <div class="w-100 mb-2">
                                <h1 class="fs-6 m-0">Izin Role</h1>
                                <span class="fs-14px text-secondary">Ceklist Izin yang tersedia untuk memberikan suatu izin kepada role</span>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger w-100">
                                    <small class="fw-semibold"><i class="fa-regular fa-lightbulb me-2"></i>Peringatan</small>
                                    <hr class="my-2">
                                    <ul class="fs-14px m-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="w-100 border rounded p-2 px-3">
                                <label for="all_izin_edit" class="d-flex align-items-center gap-2 form-label fs-14px m-0">
                                    <input class="form-checkbox all_izin" type="checkbox" id="all_izin_edit" @checked(empty(array_diff(old('permission_edit.barang', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && empty(array_diff(old('permission_edit.kategori', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && empty(array_diff(old('permission_edit.supplier', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && empty(array_diff(old('permission_edit.penjualan', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && Arr::has(old('permission_edit', ['']), ['kasir', 'dashboard', 'laporan', 'pengaturan']))>
                                    <i class="fa-duotone fa-spinner-third fa-spin d-none"></i>
                                    Izinkan Semua Aksi Yang Ada
                                </label>

                                <div class="custom-modal hide modalkonfirmasi position-fixed d-flex align-items-center justify-content-center" style="top: 0; bottom: 0; left: 0; right: 0; z-index: 1; background-color: rgba(0, 0, 0, 0.2)">
                                    <div class="bg-white p-3 rounded">
                                        <div class="border-bottom pb-2">
                                            <h5 class="modal-title fs-6" id="staticBackdropLabel">Konfirmasi Pemberian Semua Izin</h5>
                                        </div>
                                        <div class="modal-body">
                                            <p class="m-0">Apakah anda yakin ingin memberikan semua izin yang tersedia untuk role ini?</p>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="rounded btn-cancle fw-semibold btn btn-sm btn-secondary">Batal</button>
                                            <button type="button" class="rounded btn-confirm fw-semibold btn btn-sm btn-success">Ya, Berikan</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Barang</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[barang][]" value="lihat" id="lihat_barang_edit" @checked(in_array('lihat', old('permission_edit.barang', [])))>
                                    <label class="form-check-label" for="lihat_barang_edit">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[barang][]" value="tambah" id="tambah_barang_edit" @checked(in_array('tambah', old('permission_edit.barang', [])))>
                                    <label class="form-check-label" for="tambah_barang_edit">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[barang][]" value="edit" id="edit_barang_edit" @checked(in_array('edit', old('permission_edit.barang', [])))>
                                    <label class="form-check-label" for="edit_barang_edit">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[barang][]" value="hapus" id="hapus_barang_edit" @checked(in_array('hapus', old('permission_edit.barang', [])))>
                                    <label class="form-check-label" for="hapus_barang_edit">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_barang_edit" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_barang_edit" @checked(empty(array_diff(old('permission_edit.barang', ['']), ['lihat', 'tambah', 'edit', 'hapus'])))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Kategori</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[kategori][]" value="lihat" id="lihat_kategori_edit" @checked(in_array('lihat', old('permission_edit.kategori', [])))>
                                    <label class="form-check-label" for="lihat_kategori_edit">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[kategori][]" value="tambah" id="tambah_kategori_edit" @checked(in_array('tambah', old('permission_edit.kategori', [])))>
                                    <label class="form-check-label" for="tambah_kategori_edit">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[kategori][]" value="edit" id="edit_kategori_edit" @checked(in_array('edit', old('permission_edit.kategori', [])))>
                                    <label class="form-check-label" for="edit_kategori_edit">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[kategori][]" value="hapus" id="hapus_kategori_edit" @checked(in_array('hapus', old('permission_edit.kategori', [])))>
                                    <label class="form-check-label" for="hapus_kategori_edit">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_kategori_edit" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_kategori_edit" @checked(empty(array_diff(old('permission_edit.kategori', ['']), ['lihat', 'tambah', 'edit', 'hapus'])))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Supplier</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[supplier][]" value="lihat" id="lihat_supplier_edit" @checked(in_array('lihat', old('permission_edit.supplier', [])))>
                                    <label class="form-check-label" for="lihat_supplier_edit">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[supplier][]" value="tambah" id="tambah_supplier_edit" @checked(in_array('tambah', old('permission_edit.supplier', [])))>
                                    <label class="form-check-label" for="tambah_supplier_edit">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[supplier][]" value="edit" id="edit_supplier_edit" @checked(in_array('edit', old('permission_edit.supplier', [])))>
                                    <label class="form-check-label" for="edit_supplier_edit">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[supplier][]" value="hapus" id="hapus_supplier_edit" @checked(in_array('hapus', old('permission_edit.supplier', [])))>
                                    <label class="form-check-label" for="hapus_supplier_edit">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_supplier_edit" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_supplier_edit" @checked(empty(array_diff(old('permission_edit.supplier', ['']), ['lihat', 'tambah', 'edit', 'hapus'])))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Penjualan</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[kasir]" id="kasir_edit" @checked(Arr::exists(old('permission_edit', []), 'kasir'))>
                                    <label class="form-check-label" for="kasir_edit">Izin Mengakses Halaman Kasir</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[penjualan][]" value="lihat" id="lihat_penjualan_edit" @checked(in_array('hapus', old('permission_edit.penjualan', [])))>
                                    <label class="form-check-label" for="lihat_penjualan_edit">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[penjualan][]" value="tambah" id="tambah_penjualan_edit" @checked(in_array('hapus', old('permission_edit.penjualan', [])))>
                                    <label class="form-check-label" for="tambah_penjualan_edit">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[penjualan][]" value="edit" id="edit_penjualan_edit" @checked(in_array('hapus', old('permission_edit.penjualan', [])))>
                                    <label class="form-check-label" for="edit_penjualan_edit">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[penjualan][]" value="hapus" id="hapus_penjualan_edit" @checked(in_array('hapus', old('permission_edit.penjualan', [])))>
                                    <label class="form-check-label" for="hapus_penjualan_edit">Hapus Data</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_penjualan_edit" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_penjualan_edit" @checked(empty(array_diff(old('permission_edit.penjualan', ['']), ['lihat', 'tambah', 'edit', 'hapus'])) && Arr::exists(old('permission_edit', []), 'kasir'))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                            <fieldset class="flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Izin Lain-lain</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[dashboard]" id="dashboard_edit" @checked(Arr::exists(old('permission_edit', []), 'dashboard'))>
                                    <label class="form-check-label" for="dashboard_edit">Izin Mengakses Halaman Dashboard</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[laporan]" id="laporan_edit" @checked(Arr::exists(old('permission_edit', []), 'laporan'))>
                                    <label class="form-check-label" for="laporan_edit">Laporan</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" type="checkbox" role="switch" name="permission_edit[pengaturan]" id="pengaturan_edit" @checked(Arr::exists(old('permission_edit', []), 'pengaturan'))>
                                    <label class="form-check-label" for="pengaturan_edit">Pengaturan Aplikasi</label>
                                </div>
                                <hr class="m-2">
                                <label for="izin_lain_lain_edit" class="d-flex align-items-center gap-2 form-label fs-14px">
                                    <input class="form-checkbox check-semua" type="checkbox" id="izin_lain_lain_edit" @checked(Arr::has(old('permission_edit', ['']), ['dashboard', 'laporan', 'pengaturan']))>
                                    Izinkan Semua Aksi
                                </label>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-warning text-white">Edit Data</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal modal-xl fade" id="modaldetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body position-relative">
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white loading" style="z-index: 1; top: 0; bottom: 0; right: 0; left: 0;">
                        <div class="p-3 fs-1"><i class="fa-duotone fa-spinner-third fa-spin"></i></div>
                    </div>
                    <div class="row g-0">
                        <div class="position-relative col-4 pe-5 me-2 border-end">
                            <div class="position-sticky fs-15px" style="top: 0">
                                <p class="fw-semibold mb-0">Nama :</p>
                                <p class="text-nama">nama role</p>
                                <p class="fw-semibold mb-0">Keterangan :</p>
                                <p class="text-keterangan">keterangan role</p>
                                <p class="fw-semibold mb-0">Jumlah Izin :</p>
                                <p class="text-jumlah_izin">0</p>
                                <p class="fw-semibold mb-0">Jumlah Pengguna Yang Menggunakan Role Ini :</p>
                                <p class="text-jumlah_pengguna">0</p>
                            </div>
                        </div>
                        <div class="col d-flex flex-wrap gap-2">
                            <div class="w-100 mb-0">
                                <h1 class="fs-6 m-0">Izin Role</h1>
                                <span class="fs-14px text-secondary">Izin Yang Dimiliki</span>
                            </div>
                            <fieldset class="bg-white flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Barang</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[barang][]" value="lihat">
                                    <label class="form-check-label" style="opacity: 1;" for="lihat_barang">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[barang][]" value="tambah">
                                    <label class="form-check-label" style="opacity: 1;" for="tambah_barang">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[barang][]" value="edit">
                                    <label class="form-check-label" style="opacity: 1;" for="edit_barang">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[barang][]" value="hapus">
                                    <label class="form-check-label" style="opacity: 1;" for="hapus_barang">Hapus Data</label>
                                </div>
                            </fieldset>
                            <fieldset class="bg-white flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Kategori</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[kategori][]" value="lihat">
                                    <label class="form-check-label" style="opacity: 1;" for="lihat_kategori">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[kategori][]" value="tambah">
                                    <label class="form-check-label" style="opacity: 1;" for="tambah_kategori">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[kategori][]" value="edit">
                                    <label class="form-check-label" style="opacity: 1;" for="edit_kategori">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[kategori][]" value="hapus">
                                    <label class="form-check-label" style="opacity: 1;" for="hapus_kategori">Hapus Data</label>
                                </div>
                            </fieldset>
                            <fieldset class="bg-white flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Supplier</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[supplier][]" value="lihat">
                                    <label class="form-check-label" style="opacity: 1;" for="lihat_supplier">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[supplier][]" value="tambah">
                                    <label class="form-check-label" style="opacity: 1;" for="tambah_supplier">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[supplier][]" value="edit">
                                    <label class="form-check-label" style="opacity: 1;" for="edit_supplier">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[supplier][]" value="hapus">
                                    <label class="form-check-label" style="opacity: 1;" for="hapus_supplier">Hapus Data</label>
                                </div>
                            </fieldset>
                            <fieldset class="bg-white flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Data Penjualan</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[kasir]" id="kasir">
                                    <label class="form-check-label" style="opacity: 1;" for="kasir">Izin Mengakses Halaman Kasir</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[penjualan][]" value="lihat">
                                    <label class="form-check-label" style="opacity: 1;" for="lihat_penjualan">Lihat Data</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[penjualan][]" value="tambah">
                                    <label class="form-check-label" style="opacity: 1;" for="tambah_penjualan">Tambah Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[penjualan][]" value="edit">
                                    <label class="form-check-label" style="opacity: 1;" for="edit_penjualan">Edit Data</label>
                                </div>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[penjualan][]" value="hapus">
                                    <label class="form-check-label" style="opacity: 1;" for="hapus_penjualan">Hapus Data</label>
                                </div>
                            </fieldset>
                            <fieldset class="bg-white flex-grow-1 border rounded p-2 px-3 field-permissions">
                                <legend class="float-none w-auto px-2 small m-0">Izin Lain-lain</legend>
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[dashboard]">
                                    <label class="form-check-label" style="opacity: 1;" for="dashboard">Izin Mengakses Halaman Dashboard</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[laporan]">
                                    <label class="form-check-label" style="opacity: 1;" for="laporan">Laporan</label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check form-switch fs-14px">
                                    <input class="form-check-input" disabled type="checkbox" role="switch" name="permission[pengaturan]">
                                    <label class="form-check-label" style="opacity: 1;" for="pengaturan">Pengaturan Aplikasi</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-1">
                        <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="fw-semibold btn btn-sm btn-warning text-white btn-edit" data-bs-toggle="modal" data-bs-target="#modaledit" hidden>Edit Data</button>
                        <button type="button" class="fw-semibold btn btn-sm btn-danger btn-hapus" data-bs-toggle="modal" data-bs-target="#modalhapus" hidden>Hapus Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalhapus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="m-0">Apakah anda yakin ingin menghapus role "<strong class="nama"></strong>" ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-danger">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const checkboxSemuaIzin = document.querySelectorAll('.all_izin');
        checkboxSemuaIzin.forEach((checkbox) => {
            checkbox.addEventListener('change', (event) => {
                const parentElement = checkbox.parentElement.parentElement.parentElement;
                const fieldset = parentElement.querySelectorAll('fieldset');
                const modalKonfirmasi = parentElement.querySelector('.modalkonfirmasi')
                if (checkbox.checked) {
                    checkbox.checked = false;
                    modalKonfirmasi.classList.remove('hide');

                    modalKonfirmasi.querySelector('.btn-cancle').addEventListener('click', () => {
                        modalKonfirmasi.classList.add('hide');
                    });

                    modalKonfirmasi.querySelector('.btn-confirm').addEventListener('click', () => {
                        modalKonfirmasi.classList.add('hide');
                        setTimeout(() => {
                            fieldset.forEach((element) => {
                                element.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                                    input.checked = true;
                                });
                            });
                            checkbox.checked = true;
                        }, 300);
                    });
                } else {
                    fieldset.forEach((element) => {
                        element.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                            input.checked = false;
                        });
                    });
                    checkbox.checked = false;
                }
            });
        });

        document.querySelectorAll('.check-semua').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const fieldSet = checkbox.parentElement.parentElement;
                console.log(fieldSet);
                if (checkbox.checked) {
                    fieldSet.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                        checkbox.checked = true;
                    });
                } else {
                    fieldSet.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                }
                checkAllChecked(fieldSet.parentElement);
            });
        });

        document.querySelectorAll('input[type="checkbox"][value="tambah"], input[type="checkbox"][value="edit"], input[type="checkbox"][value="hapus"]').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const fieldSet = checkbox.parentElement.parentElement;
                if (checkbox.checked) {
                    fieldSet.querySelector('input[type="checkbox"][value="lihat"]').checked = true;
                } else {
                    let checkedCount = fieldSet.querySelectorAll('input[value="tambah"]:checked, input[value="edit"]:checked, input[value="hapus"]:checked').length;
                    console.log(checkedCount);
                    if (checkedCount == 0) {
                        fieldSet.querySelector('input[value="lihat"]').checked = false;
                    }
                }
                checkAllChecked(fieldSet.parentElement);
            })
        });

        document.querySelectorAll('input[type="checkbox"][name="permission[kasir]"], input[type="checkbox"][name="permission_edit[kasir]"]').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const fieldSet = checkbox.parentElement.parentElement;
                if (checkbox.checked) {
                    fieldSet.querySelectorAll('input[value="tambah"], input[value="lihat"]').forEach((checkbox) => {
                        checkbox.checked = true;
                    });
                } else {
                    fieldSet.querySelectorAll('input[value="tambah"], input[value="lihat"]').forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                }
                checkAllChecked(fieldSet.parentElement);
            })
        });

        const fieldSets = document.querySelectorAll('fieldset');
        fieldSets.forEach((element) => {
            element.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                input.addEventListener('change', () => {
                    if (element.querySelectorAll('input[type="checkbox"][role="switch"]:not(:checked)').length == 0) {
                        element.querySelector('.check-semua').checked = true;
                    } else {
                        element.querySelector('.check-semua').checked = false;
                    }
                    checkAllChecked(element.parentElement);
                });
            });
            element.querySelectorAll('input[type="checkbox"][value="lihat"]').forEach((input) => {
                input.addEventListener('change', () => {
                    if (!input.checked) {
                        element.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                            checkbox.checked = false;
                        });
                    }
                });
            });
        });

        function checkAllChecked(parentElement) {
            if (parentElement.querySelectorAll('fieldset input[type="checkbox"]:not(:checked)').length == 0) {
                parentElement.querySelector('.all_izin').checked = true;
            } else {
                parentElement.querySelector('.all_izin').checked = false;
            }
        }

        const modalDetail = document.getElementById('modaldetail');
        modalDetail.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-bs-id');
            const loading = modalDetail.querySelector('.loading');

            loading.classList.remove('hide');
            modalDetail.querySelector('.modal-body').classList.add("overflow-y-hidden");
            modalDetail.querySelector('.modal-body').scrollTop = 0;


            fetch(currenturl + "/" + id, {
                    method: "get",
                    headers: {
                        "Content-type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    modalDetail.querySelector('.btn-edit').setAttribute('data-bs-id', data.id);
                    modalDetail.querySelector('.btn-hapus').setAttribute('data-bs-id', data.id);
                    modalDetail.querySelector('.btn-hapus').setAttribute('data-bs-nama', data.nama);

                    modalDetail.querySelector('.text-nama').innerHTML = data.nama;
                    modalDetail.querySelector('.text-keterangan').innerHTML = data.keterangan;
                    modalDetail.querySelector('.text-jumlah_izin').innerHTML = data.permissions.length;
                    modalDetail.querySelector('.text-jumlah_pengguna').innerHTML = data.jumlah_user;

                    data.permissions.forEach(permission => {
                        const aksiIzin = permission.nama.split("_")[0];
                        let namaIzin = permission.nama.split("_")[1];

                        if (namaIzin) {
                            const checkboxIzinMulti = modalDetail.querySelector(`input[name="permission[${namaIzin}][]"][value="${aksiIzin}"]`);
                            if (checkboxIzinMulti) {
                                checkboxIzinMulti.checked = true;
                            }
                        } else {
                            namaIzin = aksiIzin;
                            const checkboxIzin = modalDetail.querySelector(`input[name="permission[${namaIzin}]"]`);
                            if (checkboxIzin) {
                                checkboxIzin.checked = true;
                            }
                        }

                    });

                    loading.classList.add('hide');
                    modalDetail.querySelector('.modal-body').classList.remove("overflow-y-hidden");
                }).catch(err => {
                    console.log(err);
                });
        });

        modalDetail.addEventListener('hidden.bs.modal', (event) => {
            modalDetail.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.checked = false;
            });
        });

        const modalEdit = document.getElementById('modaledit');
        modalEdit.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const loading = modalEdit.querySelector('.loading');
            if (!button) {
                loading.classList.add('hide');
                return false;
            }

            const id = button.getAttribute('data-bs-id');
            
            loading.classList.remove('hide');
            modalEdit.querySelector('.modal-content').setAttribute('action', currenturl + "/" + id);
            modalEdit.querySelector('.modal-body').classList.add("overflow-y-hidden");
            modalEdit.querySelector('.modal-body').scrollTop = 0;

            fetch(currenturl + "/" + id, {
                    method: "get",
                    headers: {
                        "Content-type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                })
                .then(response => response.json())
                .then(data => {
                    modalEdit.querySelector('input[name="nama_edit"]').value = data.nama;
                    modalEdit.querySelector('textarea[name="keterangan_edit"]').innerHTML = data.keterangan;


                    data.permissions.forEach(permission => {
                        const aksiIzin = permission.nama.split("_")[0];
                        let namaIzin = permission.nama.split("_")[1];

                        if (namaIzin) {
                            const checkboxIzinMulti = modalEdit.querySelector(`input[name="permission_edit[${namaIzin}][]"][value="${aksiIzin}"]`);
                            if (checkboxIzinMulti) {
                                checkboxIzinMulti.checked = true;
                            }
                        } else {
                            namaIzin = aksiIzin;
                            const checkboxIzin = modalEdit.querySelector(`input[name="permission_edit[${namaIzin}]"]`);
                            if (checkboxIzin) {
                                checkboxIzin.checked = true;
                            }
                        }

                    });

                    const fieldset = modalEdit.querySelectorAll('.field-permissions');
                    fieldSets.forEach((element) => {
                        if (element.querySelectorAll('input[type="checkbox"][role="switch"]:not(:checked)').length == 0) {
                            element.querySelector('.check-semua').checked = true;
                        }
                    });

                    if (modalEdit.querySelectorAll('.field-permissions .check-semua:not(:checked)').length == 0) {
                        modalEdit.querySelector('.all_izin').checked = true;
                    }

                    loading.classList.add('hide');
                    modalEdit.querySelector('.modal-body').classList.remove("overflow-y-hidden");
                }).catch(err => {
                    console.log(err);
                });
        });

        modalEdit.addEventListener('hidden.bs.modal', (event) => {
            modalEdit.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.checked = false;
            });
        });
    </script>
@endsection
