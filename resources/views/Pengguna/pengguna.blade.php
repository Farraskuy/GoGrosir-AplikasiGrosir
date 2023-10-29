@extends('layout')

@section('title', 'Pengguna | ' . nama_aplikasi())

@section('content')
    <section class="p-3">
        <h4 class="fw-semibold">Daftar Pengguna</h4>
        <button class="btn btn-sm btn-success fw-semibold mb-3" data-bs-toggle="modal" data-bs-action="tambah" data-bs-target="#modaltambah">Tambah Data</button>
        <div class="row g-0 gap-3">
            <form method="get" onchange="filterData(this)" class="col rounded-3 bg-white p-3 pt-0" style="height: fit-content">
                <div class="alert-container"></div>
                <div class="bg-white position-sticky pt-3 pb-2" style="top: 61px">
                    <div class="d-flex gap-2 justify-content-end mb-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari" value="{{ request()->query('keyword', '') }}" name="keyword" oninput="searchData(this)">
                        <select class="form-select fs-14px w-auto h-100" style="line-height: 1.7" name="filtered_by">
                            <option value="" {{ request()->query('filtered_by') == '' ? 'selected' : '' }}>Urutkan berdasarkan</option>
                            <option value="nama" {{ request()->query('filtered_by') == 'nama' ? 'selected' : '' }}>Nama</option>
                            <option value="username" {{ request()->query('filtered_by') == 'username' ? 'selected' : '' }}>USername</option>
                            <option value="role_id" {{ request()->query('filtered_by') == 'role_id' ? 'selected' : '' }}>Role</option>
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
                                    <option {{ request()->query('showing') == '' || request()->query('showing') == '20' ? 'selected' : '' }}>20</option>
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
                                <th scope="col">Username</th>
                                <th scope="col">Role</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Jenis Kelamin</th>
                                <th scope="col">Nomor Telepon</th>
                                <th class="text-center" scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($data as $item)
                                <tr>
                                    <th class="fit" scope="row">{{ $nourut++ }}</th>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->role->nama }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{!! $item->jenis_kelamin ? ($item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '<i class="text-secondary">Belum Di Data</i>' !!}</td>
                                    <td>{{ $item->nomor_telepon }}</td>
                                    <td class="fit">
                                        <div class="row g-0 gap-1 flex-row flex-nowrap">
                                            <button type="button" class="col btn btn-sm btn-primary fw-semibold fs-13px" data-bs-toggle="modal" data-bs-id="{{ $item->id }}" data-bs-nama="{{ $item->nama }}" data-bs-target="#modaldetail">Detail</button>
                                            <button type="button" class="col btn btn-sm btn-warning text-white fw-semibold fs-13px" data-bs-toggle="modal" data-bs-id="{{ $item->id }}" data-bs-action="edit" data-bs-target="#modaledit">Edit</button>
                                            @if ($item->role->nama != 'super admin')
                                                <button type="button" class="col btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="{{ $item->id }}" data-bs-nama="{{ $item->nama }}">Hapus</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <script>
                                function rowData(data, nourut) {
                                    const tr = document.createElement("tr");
                                    const otherButton = `
                                        <button type="button" class="col btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="${ data.id }" data-bs-nama="${ data.nama }">Hapus</button>
                                    `;
                                    tr.innerHTML = `
                                        <th class="fit" scope="row">${ nourut }</th>
                                        <td>${ data.username }</td>
                                        <td>${ data.role.nama }</td>
                                        <td>${ data.nama }</td>
                                        <td>${ data.jenis_kelamin ? (data.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '<i class="text-secondary">Belum Di Data</i>'}</td>
                                        <td>${ data.nomor_telepon }</td>
                                        <td class="fit">
                                            <div class="row g-0 gap-1 flex-row flex-nowrap">
                                                <button type="button" class="col btn btn-sm btn-primary fw-semibold fs-13px" data-bs-toggle="modal" data-bs-id="${ data.id }" data-bs-nama="${ data.nama }" data-bs-target="#modaldetail">Detail</button>
                                                <button type="button" class="col btn btn-sm btn-warning text-white fw-semibold fs-13px" data-bs-toggle="modal" data-bs-id="${ data.id }" data-bs-action="edit" data-bs-target="#modaledit">Edit</button>
                                                ${ !data.role.isSuperAdmin ? otherButton : ''}                                                
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
                                <option {{ request()->query('showing') == 'all' ? 'selected' : '' }} value="all"> Semua</option>
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

     {{-- Modal Tambah --}}
    <div class="modal modal-lg fade" id="modaltambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <form class="modal-content" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title m-0">Tambah Data</h5>
                        <p class="m-0 fs-13px">Kolom dengan <span class="text-danger">*</span> wajib di isi</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body position-relative">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white loading d-none" style="z-index: 1; top: 0; bottom: 0; right: 0; left: 0;">
                        <div class="p-3 fs-1"><i class="fa-duotone fa-spinner-third fa-spin"></i></div>
                    </div>
                    <div class="row g-0">
                        <div class="col-6 p-2 pe-3 border-end">
                            <div class="p-3 pb-2 position-relative border rounded">
                                <p class="position-absolute bg-white px-2 small fw-semibold" style="top: -10px">Data Login</p>
                                <div class="mb-2">
                                    <label for="username" class="form-label fs-14px mustFilled">Username</label>
                                    <input type="text" class="form-control form-control-sm @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}">
                                    <div class="invalid-feedback">
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="role_id" class="form-label fs-14px mustFilled">Role</label>
                                    <select class="form-select form-select-sm  @error('role_id') is-invalid @enderror" name="role_id" id="role_id">
                                        <option>Pilih Role</option>
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id }}" {{ old('role_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('role_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="password" class="form-label fs-14px mustFilled">Password</label>
                                    <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}">
                                    <div class="invalid-feedback">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="password_repeat" class="form-label fs-14px mustFilled">Ulangi Password</label>
                                    <input type="password" class="form-control form-control-sm @error('password_repeat') is-invalid @enderror" id="password_repeat" name="password_repeat" value="{{ old('password_repeat') }}">
                                    <div class="invalid-feedback">
                                        @error('password_repeat')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col p-2 ps-3">
                            <div class="mb-2">
                                <label for="nama" class="form-label fs-14px mustFilled">Nama</label>
                                <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}">
                                <div class="invalid-feedback">
                                    @error('nama')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="jenis_kelamin" class="form-label fs-14px mustFilled">Jenis Kelamin</label>
                                <select class="form-select form-select-sm @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin">
                                    <option value="" {{ old('jenis_kelamin') == '' ? 'selected' : '' }}>Pilih Jenis kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('jenis_kelamin')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="nomor_telepon" class="form-label fs-14px mustFilled">Nomor Telepon</label>
                                <input type="text" class="form-control form-control-sm @error('nomor_telepon') is-invalid @enderror" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}">
                                <div class="invalid-feedback">
                                    @error('nomor_telepon')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="alamat" class="form-label fs-14px">Alamat</label>
                                <textarea class="form-control form-control-sm @error('alamat') is-invalid @enderror" id="alamat" name="alamat">{{ old('alamat') }}</textarea>
                                <div class="invalid-feedback">
                                    @error('alamat')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="alamat" class="form-label fs-14px">Foto</label>
                                <div class="row g-0 gap-2">
                                    <div class="p-2 border rounded" style="height: 200px; width: 150px;">
                                        <div class="mb-2 d-flex justify-content-between align-items-center" style="height: 25px">
                                            <p class="fs-14px m-0">Preview</p>
                                            <button type="button" class="btn btn-sm btn-clear-preview hide"><i class="fa-regular fa-xmark"></i></button>
                                        </div>
                                        <img class="bg-secondary w-100 rounded preview-gambar" style="height: 150px; object-fit: cover;" src="{{ asset('storage/upload') }}/default.jpg" alt="dafault user">
                                    </div>
                                    <div class="col">
                                        <p class="text-secondary fs-12px nama-gambar hide mb-2 wrap-text"></p>
                                        <label for="gambar" class="btn btn-sm w-100 btn-primary fw-semibold mb-2">Pilih Gambar</label>
                                        <input type="file" id="gambar" name="gambar" class="form-control form-control-sm form-control-file mb-2 input-gambar" hidden>
                                        <p class="text-secondary fs-12px">Pilih file foto dengan ukuran yang kurang dari 2MB, dan berekstensi .png dan .jpg atau .jpeg</p>
                                        <div class="invalid-feedback">
                                            @error('alamat')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-success text-white">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal modal-lg fade" id="modaledit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <form class="modal-content" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title m-0">Edit Data</h5>
                        <p class="m-0 fs-13px">Kolom dengan <span class="text-danger">*</span> wajib di isi</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body position-relative">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 fs-14px">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white loading d-none" style="z-index: 1; top: 0; bottom: 0; right: 0; left: 0;">
                        <div class="p-3 fs-1"><i class="fa-duotone fa-spinner-third fa-spin"></i></div>
                    </div>
                    <div class="row g-0">
                        <div class="col-6 p-2 pe-3 border-end">
                            <div class="p-3 pb-2 position-relative border rounded">
                                <p class="position-absolute bg-white px-2 small fw-semibold" style="top: -10px">Data Login</p>
                                <div class="mb-2">
                                    <label for="username_edit" class="form-label fs-14px mustFilled">Username</label>
                                    <input type="text" class="form-control form-control-sm @error('username_edit') is-invalid @enderror" id="username_edit" name="username_edit" value="{{ old('username_edit') }}">
                                    <div class="invalid-feedback">
                                        @error('username_edit')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <fieldset class="mb-2 role-wrapper">
                                    <label for="role_id_edit" class="form-label fs-14px mustFilled">Role</label>
                                    <select class="form-select form-select-sm  @error('role_id_edit') is-invalid @enderror" name="role_id_edit" id="role_id_edit">
                                        <option>Pilih Role</option>
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id }}" {{ old('role_id_edit') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('role_id_edit')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </fieldset>
                                <div class="d-flex justify-content-end">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal_reset_password" class="btn btn-sm btn-warning text-white fw-semibold btn-reset-password">Ubah Password</button>
                                </div>
                            </div>
                        </div>
                        <div class="col p-2 ps-3">
                            <div class="mb-2">
                                <label for="nama_edit" class="form-label fs-14px mustFilled">Nama</label>
                                <input type="text" class="form-control form-control-sm @error('nama_edit') is-invalid @enderror" id="nama_edit" name="nama_edit" value="{{ old('nama_edit') }}">
                                <div class="invalid-feedback">
                                    @error('nama_edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="jenis_kelamin_edit" class="form-label fs-14px mustFilled">Jenis Kelamin</label>
                                <select class="form-select form-select-sm @error('jenis_kelamin_edit') is-invalid @enderror" name="jenis_kelamin_edit" id="jenis_kelamin_edit">
                                    <option value="" {{ old('jenis_kelamin_edit') == '' ? 'selected' : '' }}>Pilih Jenis kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin_edit') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin_edit') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('jenis_kelamin_edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="nomor_telepon_edit" class="form-label fs-14px mustFilled">Nomor Telepon</label>
                                <input type="text" class="form-control form-control-sm @error('nomor_telepon_edit') is-invalid @enderror" id="nomor_telepon_edit" name="nomor_telepon_edit" value="{{ old('nomor_telepon_edit') }}">
                                <div class="invalid-feedback">
                                    @error('nomor_telepon_edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="alamat_edit" class="form-label fs-14px">Alamat</label>
                                <textarea class="form-control form-control-sm @error('alamat_edit') is-invalid @enderror" id="alamat_edit" name="alamat_edit">{{ old('alamat_edit') }}</textarea>
                                <div class="invalid-feedback">
                                    @error('alamat_edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fs-14px">Foto</label>
                                <div class="row g-0 gap-2">
                                    <div class="p-2 border rounded" style="height: 200px; width: 150px;">
                                        <div class="mb-2 d-flex justify-content-between align-items-center" style="height: 25px">
                                            <p class="fs-14px m-0">Preview</p>
                                            <button type="button" class="btn btn-sm btn-clear-preview hide"><i class="fa-regular fa-xmark"></i></button>
                                        </div>
                                        <img class="bg-secondary w-100 rounded preview-gambar" style="height: 150px; object-fit: cover;" src="{{ asset('storage/upload') }}/default.jpg" alt="dafault user">
                                    </div>
                                    <div class="col">
                                        <p class="text-secondary fs-12px nama-gambar hide mb-2 wrap-text"></p>
                                        <div class="d-flex gap-1 mb-2">
                                            @if (old('gambar_lama', '') != "default.jpg" && old('gambar_lama', '') != "default_female.jpg")
                                                <button type="button" class="btn btn-sm btn-danger text-white fw-semibold btn-hapus-gambar">Hapus</button>
                                            @endif
                                            <label for="gambar_edit" class="btn btn-sm w-100 btn-primary fw-semibold">Pilih Gambar</label>
                                        </div>
                                        <input type="hidden" name="gambar_lama" class="input-gambar-lama" value="{{ old('gambar_lama') }}">
                                        <input type="file" id="gambar_edit" name="gambar_edit" class="form-control form-control-sm form-control-file mb-2 input-gambar @error('gambar_edit') is-invalid @enderror" hidden>
                                        <div class="invalid-feedback">
                                            @error('gambar_edit')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                        <p class="text-secondary fs-12px">Pilih file foto dengan ukuran yang kurang dari 2MB, dan berekstensi .png dan .jpg atau .jpeg</p>
                                    </div>
                                </div>
                            </div>
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

     {{-- Modal Detail --}}
    <div class="modal modal-lg fade" id="modaldetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">Detail Data Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body position-relative">
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white loading" style="z-index: 1; top: 0; bottom: 0; right: 0; left: 0;">
                        <div class="p-3 fs-1"><i class="fa-duotone fa-spinner-third fa-spin"></i></div>
                    </div>
                    <div class="row g-0">
                        <div class="col-6 p-2 pe-3 border-end">
                            <div class="w-100 d-flex justify-content-center mb-4">
                                <img src="{{ asset('storage/upload') }}/default.jpg" class="rounded gambar" style="width: 150px; height: 200px; object-fit: cover;" alt="">
                            </div>
                            <div class="border rounded p-3 pb-2 position-relative">
                                <p class="position-absolute bg-white px-2 small fw-semibold" style="top: -10px">Data Login</p>
                                <table class="fs-15px">
                                    <tr>
                                        <td>Username</td>
                                        <td class="px-3 py-1">:</td>
                                        <td class="text-username">Admin</td>
                                    </tr>
                                    <tr>
                                        <td>Role</td>
                                        <td class="px-3 py-1">:</td>
                                        <td class="text-role">Admin</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col p-2 ps-3">
                            <p class="fw-semibold mb-0">Nama :</p>
                            <p class="text-nama">Nama Admin</p>
                            <p class="fw-semibold mb-0">Jenis Kelamin :</p>
                            <p class="text-jenis_kelamin">Laki-laki</p>
                            <p class="fw-semibold mb-0">Nomor Telepon :</p>
                            <p class="text-nomor_telepon">0812345678910</p>
                            <p class="fw-semibold mb-0">Alamat :</p>
                            <p class="text-alamat">Alamat Admin</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-1">
                        <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modaledit" class="fw-semibold btn btn-sm btn-warning text-white">Edit Data</button>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalhapus" class="fw-semibold btn btn-sm btn-danger text-white d-none" data-bs-action="onmodal">Hapus Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

     {{-- Modal Hapus --}}
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
                    <p class="m-0">Apakah anda yakin ingin menghapus pengguna "<strong class="nama"></strong>" ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-danger">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_reset_password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST">
                @csrf
                @method('patch')
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="m-0">Apakah anda yakin ingin menghapus pengguna "<strong class="nama"></strong>" ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#modaltambahedit">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-danger">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modaldetail = document.getElementById('modaldetail');
        if (modaldetail) {
            modaldetail.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-bs-action');
                const id = button.getAttribute('data-bs-id');

                const loading = modaldetail.querySelector('.loading');
                loading.classList.remove('hide');

                fetch(currenturl + "/detail/" + id, {
                        method: "get",
                        headers: {
                            "Content-type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        const btnEdit = modaldetail.querySelector('button[data-bs-target="#modaledit"]');
                        btnEdit.setAttribute('data-bs-id', id);

                        const btnHapus = modaldetail.querySelector('button[data-bs-target="#modalhapus"]');
                        if (data.isSuperAdmin) {
                            btnHapus.classList.add('d-none');
                        } else {
                            btnHapus.setAttribute('data-bs-id', id);
                            btnHapus.setAttribute('data-bs-nama', button.getAttribute('data-bs-nama'));
                            btnHapus.classList.remove('d-none');
                        }

                        for (const key in data) {
                            if (key == "foto") {
                                modaldetail.querySelector('.gambar').src = "{{ asset('storage/upload') }}/" + data[key];
                            } else {
                                if (modaldetail.querySelector('.text-' + key)) {
                                    modaldetail.querySelector('.text-' + key).innerHTML = data[key] ? data[key] : `<i class='text-secondary'>- Belum Di Data -</i>`;
                                }
                            }
                        }
                        loading.classList.add('hide');
                    }).catch(err => {
                        console.log(err);
                    })
            });
        }

        const modalHapus = document.getElementById('modalhapus');
        if (modalHapus) {
            modalHapus.addEventListener('hidden.bs.modal', (event) => {
                const btnBatalModalHapus = document.querySelector('#modalhapus button[data-bs-dismiss="modal"]');
                btnBatalModalHapus.setAttribute('data-bs-dismiss', "modal");
                btnBatalModalHapus.removeAttribute('data-bs-toogle', "modal");
                btnBatalModalHapus.removeAttribute('data-bs-target', "#modaldetail");
            });
        }

        const modalEdit = document.getElementById('modaledit');
        const btnHapusGambar = modalEdit.querySelector('.btn-hapus-gambar');

        let stateBtnHapus = "hapus";
        let valueGambarLama = "";
        btnHapusGambar.addEventListener(('click'), () => {
            const previewGambar = modalEdit.querySelector('.preview-gambar');
            const inputGambarLama = modalEdit.querySelector('.input-gambar-lama');
            const inputSelectJenisKelamin = modalEdit.querySelector('select[name="jenis_kelamin_edit"]');
            if (stateBtnHapus == "hapus") {
                valueGambarLama = inputGambarLama.value;
                inputGambarLama.setAttribute('disabled', true);

                previewGambar.classList.add('changing');
                previewGambar.src = "{{ asset('storage/upload') }}/" + (inputSelectJenisKelamin.value == "L" ? "default.jpg" : "default_female.jpg");
                setTimeout(() => {
                    previewGambar.classList.remove('changing');
                }, 300);

                btnHapusGambar.classList.remove('btn-danger');
                btnHapusGambar.classList.add('btn-secondary');
                btnHapusGambar.innerHTML = "Batal";
                stateBtnHapus = "batal";
            } else {
                inputGambarLama.removeAttribute('disabled');

                previewGambar.classList.add('changing');
                previewGambar.src = "{{ asset('storage/upload') }}/" + valueGambarLama;
                setTimeout(() => {
                    previewGambar.classList.remove('changing');
                }, 300);

                btnHapusGambar.classList.add('btn-danger');
                btnHapusGambar.classList.remove('btn-secondary');
                btnHapusGambar.innerHTML = "Hapus";
                stateBtnHapus = "hapus";
            }
        });

        if (modalEdit) {
            modalEdit.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                if (!button) {
                    loading.classList.remove('hide');
                    return false;
                }


                const modalBodyInput = modalEdit.querySelector('.modal-body input');
                const btnResetPass = modalEdit.querySelector('.btn-reset-password');
                const loading = modalEdit.querySelector('.loading');
                const fieldSelectRole = modalEdit.querySelector('.role-wrapper');

                loading.classList.remove('d-none');
                loading.classList.remove('hide');
                modalEdit.querySelector('.modal-body').classList.add("overflow-y-hidden");
                modalEdit.querySelector('.modal-body').scrollTop = 0;

                modalEdit.querySelector('.input-gambar-lama').removeAttribute('disabled');

                const id = button.getAttribute('data-bs-id');
                const form = modalEdit.querySelector('form');
                form.setAttribute('action', currenturl + "/" + id);

                fetch(currenturl + "/" + id, {
                        method: "get",
                        headers: {
                            "Content-type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.isSuperAdmin) {
                            fieldSelectRole.classList.add('d-none');
                            fieldSelectRole.setAttribute('disabled', true);
                        } else {
                            fieldSelectRole.classList.remove('d-none');
                            fieldSelectRole.removeAttribute('disabled');
                        }

                        if (data['foto'] == 'default.jpg' || data['foto'] == 'default_female.jpg') {
                            btnHapusGambar.classList.add('d-none');
                        } else {
                            btnHapusGambar.classList.remove('d-none');
                        }

                        modalEdit.querySelector('.input-gambar-lama').value = data['foto'];
                        modalEdit.querySelector('.preview-gambar').src = "{{ asset('storage/upload') }}/" + data['foto'];

                        for (const key in data) {
                            const input = modalEdit.querySelector(`form [name="${key}_edit"]`);
                            if (input) {
                                input.value = data[key];
                            }

                            if (key == 'alamat') {
                                input.innerHTML = data[key];
                            }

                            if (key == 'input_select') {
                                for (const inputSelectkey in data[key]) {
                                    const inputSelect = modalEdit.querySelector(`form select[name="${inputSelectkey}_edit"] option[value="${data[key][inputSelectkey] ? data[key][inputSelectkey] : ''}"]`);
                                    if (inputSelect) {
                                        inputSelect.setAttribute('selected', true);
                                    }
                                }
                            }
                        }
                        loading.classList.add('hide');
                        modalEdit.querySelector('.modal-body').classList.remove("overflow-y-hidden");
                    }).catch(err => {
                        console.log(err);
                    });

            });
        }

        const inputSelectJenisKelamin = document.querySelectorAll('select[name="jenis_kelamin"], select[name="jenis_kelamin_edit"]');
        inputSelectJenisKelamin.forEach((element) => {
            element.addEventListener('change', () => {
                const parentElement = element.parentElement.parentElement;
                const previewGambar = parentElement.querySelector('.preview-gambar');
                const urlStorage = "{{ asset('storage/upload') }}/";
                let srcGambar = urlStorage + "default.jpg";
                if (element.value == "P") {
                    srcGambar = urlStorage + "default_female.jpg";
                }

                const elInputGambarLama = parentElement.querySelector('.input-gambar-lama:not(:disabled)');
                if (elInputGambarLama) {
                    if (elInputGambarLama.value != "default.jpg" && elInputGambarLama.value != "default_female.jpg") {
                        srcGambar = urlStorage + elInputGambarLama.value;
                    }
                }

                const elInputGambar = parentElement.querySelector('.input-gambar');
                if (elInputGambar.files[0]) {
                    srcGambar = URL.createObjectURL(elInputGambar.files[0]);
                }

                previewGambar.classList.add('changing');
                previewGambar.src = srcGambar;
                setTimeout(() => {
                    previewGambar.classList.remove('changing');
                }, 300);
            });
        });

        const inputGambar = document.querySelectorAll('.input-gambar');
        inputGambar.forEach(element => {
            element.addEventListener('change', (e) => {
                const file = element.files[0];
                const filename = file.name;
                if (file) {
                    const parentElement = element.parentElement.parentElement;

                    parentElement.querySelector('.nama-gambar').classList.remove('hide');
                    parentElement.querySelector('.nama-gambar').innerHTML = filename;
                    parentElement.querySelector('.preview-gambar').src = URL.createObjectURL(file);
                    parentElement.querySelector('.btn-clear-preview').classList.remove('hide');
                }
            });
        });

        const btnClearPreview = document.querySelectorAll('.btn-clear-preview');
        btnClearPreview.forEach(element => {
            element.addEventListener('click', () => {
                const parentElement = element.parentElement.parentElement.parentElement;
                const elGambarLama = parentElement.querySelector('.input-gambar-lama');

                let elJenisKelamin = parentElement.parentElement.parentElement.querySelector('select[name="jenis_kelamin"]');
                if (!elJenisKelamin) {
                    elJenisKelamin = parentElement.parentElement.parentElement.querySelector('select[name="jenis_kelamin_edit"]')
                }
                let gambarDefault = elJenisKelamin.value == "L" ? "default.jpg" : "default_female.jpg";
                if (elGambarLama) {
                    gambarDefault = elGambarLama.value;
                }

                parentElement.querySelector('.preview-gambar').src = "{{ asset('storage/upload') }}/" + gambarDefault;
                element.classList.add('hide');
                parentElement.querySelector('.nama-gambar').classList.add('hide');
                parentElement.querySelector('.nama-gambar').innerHTML = "";
                parentElement.querySelector('.input-gambar').value = "";
            });
        });
    </script>
@endsection
