@extends('layout')

@section('title', 'Data Barang | ' . nama_aplikasi())
@section('content')
    <section class="p-3 aaa">
        @if (Auth::user()->hasPermission('tambah_barang'))
            <h4 class="fw-semibold">Daftar Barang</h4>
            <button class="btn btn-sm btn-success fw-semibold mb-3" data-bs-toggle="modal" data-bs-target="#modaltambah">Tambah Data</button>
        @else
            <h4 class="fw-semibold mb-3">Daftar Barang</h4>
        @endif

        <div class="row g-0 gap-3">
            <form method="get" onchange="filterData(this)" class="col rounded-3 bg-white p-3 pt-0" style="height: fit-content">
                <div class="alert-container"></div>
                <div class="bg-white position-sticky pt-3 pb-2" style="top: 61px; z-index: 10;">
                    <div class="d-flex gap-2 justify-content-end mb-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari" value="{{ request()->query('keyword', '') }}" name="keyword" oninput="searchData(this)">
                        <div class="dropdown">
                            <button type="button" class="btn border text-nowrap fs-14px h-100 {{ request()->query('filter_kategori') != '' || request()->query('filter_supplier') != '' ? 'btn-info text-white fw-semibold' : '' }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,10">Filter Data <i class="fa-regular fa-angle-down"></i></button>
                            <div class="dropdown-menu p-3 py-2 shadow-sm dropdown-menu-end" style="width: 250px">
                                <div class="mb-2">
                                    <label for="filter_kategori" class="form-label fs-14px">Kategori</label>
                                    <select class="form-select form-select-sm" name="filter_kategori" id="filter_kategori">
                                        <option value="" {{ request()->query('filter_kategori') == '' ? 'selected' : '' }}>Pilih Kategori
                                        </option>
                                        @foreach ($kategori as $item)
                                            <option value="{{ $item->id }}" {{ request()->query('filter_kategori') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="filter_supplier" class="form-label fs-14px">Supplier</label>
                                    <select class="form-select form-select-sm" name="filter_supplier" id="filter_supplier">
                                        <option value="" {{ request()->query('filter_supplier') == '' ? 'selected' : '' }}>Pilih Supplier
                                        </option>
                                        @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}" {{ request()->query('filter_supplier') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <select class="form-select fs-14px w-auto h-100" name="sorted_by" style="line-height: 1.7">
                            <option value="" {{ request()->query('sorted_by') == '' ? 'selected' : '' }}>Urutkan berdasarkan</option>
                            <option value="id" {{ request()->query('sorted_by') == 'id' ? 'selected' : '' }}>Nomor Urut</option>
                            <option value="nama" {{ request()->query('sorted_by') == 'nama' ? 'selected' : '' }}>Nama (Default)</option>
                            <option value="id_supplier" {{ request()->query('sorted_by') == 'id_supplier' ? 'selected' : '' }}>Supplier</option>
                            <option value="id_kategori" {{ request()->query('sorted_by') == 'id_kategori' ? 'selected' : '' }}>Kategori</option>
                            <option value="harga_jual_ecer" {{ request()->query('sorted_by') == 'harga_jual_ecer' ? 'selected' : '' }}>Harga Jual ecer</option>
                            <option value="harga_jual_grosir" {{ request()->query('sorted_by') == 'harga_jual_grosir' ? 'selected' : '' }}>Harga Jual Grosir</option>
                        </select>
                        <label for="desc" class="btn btn border {{ request()->query('ordered_by') == 'desc' ? 'd-none' : '' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Dari atas ke bawah"><i class="fa-solid fa-arrow-down-short-wide"></i></label>
                        <input type="radio" name="ordered_by" {{ request()->query('ordered_by') == 'desc' ? 'selected' : '' }} value="desc" id="desc" hidden>
                        <label for="asc" class="btn btn border {{ request()->query('ordered_by') == '' || request()->query('ordered_by') == 'asc' ? 'd-none' : '' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Dari bawah ke atas"><i class="fa-solid fa-arrow-up-wide-short"></i></label>
                        <input type="radio" name="ordered_by" {{ request()->query('ordered_by') == '' || request()->query('ordered_by') == 'asc' ? 'selected' : '' }} value="asc" id="asc" hidden>
                    </div>
                    @if (request()->query('showing') == 'all')
                        <div class="d-flex justify-content-between mb-2">
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
                        </div>
                    @endif
                </div>
                <div class="table-responsive-sm">
                    <table class="table table-sm table-hover fs-13px">
                        <thead class="position-sticky align-middle" style="z-index: 5; top: {{ request()->query('showing') == 'all' ? '170px' : '130px' }};">
                            <tr>
                                <th class="fit" scope="col">#</th>
                                <th class="fit" scope="col">Barcode</th>
                                <th scope="col">Nama</th>
                                <th class="fit" scope="col">Supplier</th>
                                <th class="fit" scope="col">Kategori</th>
                                <th class="fit" scope="col">Harga Jual <br> grosir</th>
                                <th class="fit" scope="col">Harga Jual <br> Ecer</th>
                                @if (Auth::user()->hasPermission('edit_barang', 'hapus_barang'))
                                    <th class="text-center" scope="col">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($data as $item)
                                <tr style="min-height: 38.5px">
                                    <th class="fit" scope="row">{{ $loop->iteration }}</th>
                                    <td class="fit" style="min-width: 140px; max-width: 140px">
                                        {{ $item->barcode ? $item->barcode : '-' }}
                                    </td>
                                    <td>
                                        <p class="m-0 wrap-text">{{ $item->nama }}</p>
                                    </td>
                                    <td class="fit" style="min-width: 140px; max-width: 140px">
                                        <p class="m-0 wrap-text">{{ $item->supplier->nama }}</p>
                                    </td>
                                    <td class="fit" style="min-width: 140px; max-width: 140px">
                                        <p class="m-0 wrap-text">{{ $item->kategori->nama }}</p>
                                    </td>
                                    <td class="fit" style="min-width: 120px; max-width: 120px">
                                        {{ 'Rp. ' . number_format($item->harga_jual_grosir, '0', ',', '.') }}
                                    </td>
                                    <td class="fit" style="min-width: 120px; max-width: 120px">
                                        {{ 'Rp. ' . number_format($item->harga_jual_ecer, '0', ',', '.') }}
                                    </td>
                                    @if (Auth::user()->hasPermission('edit_barang', 'hapus_barang'))
                                        <td class="fit">
                                            <div class="row g-0 justify-content-end flex-nowrap gap-1">
                                                @if (Auth::user()->hasPermission('edit_barang'))
                                                    <button type="button" style="width: 45px" class="col-5 btn btn-sm btn-warning text-white fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modaledit" data-bs-id="{{ $item->id }}">Edit</button>
                                                @endif
                                                @if (Auth::user()->hasPermission('hapus_barang'))
                                                    <button type="button" class="col-auto btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="{{ $item->id }}" data-bs-nama="{{ $item->nama }}">Hapus</button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            <script>
                                function rowData(data, nourut) {
                                    const tr = document.createElement("tr");
                                    tr.style.minHeight = "38.5px";
                                    tr.innerHTML = `
                                        <th class="fit" scope="row">${nourut}</th>
                                        <td class="fit" style="min-width: 140px; max-width: 140px">
                                            ${data.barcode ? data.barcode : "-"}
                                        </td>
                                        <td>
                                            <p class="m-0 wrap-text">${data.nama}</p>
                                        </td>
                                        <td class="fit" style="min-width: 140px; max-width: 140px">
                                            <p class="m-0 wrap-text">${data.supplier.nama}</p>
                                        </td>
                                        <td class="fit" style="min-width: 140px; max-width: 140px">
                                            <p class="m-0 wrap-text">${data.kategori.nama}</p>
                                        </td>
                                        <td class="fit" style="min-width: 120px; max-width: 120px">
                                            ${formatRupiah(data.harga_jual_grosir)}
                                        </td>
                                        <td class="fit" style="min-width: 120px; max-width: 120px">
                                            ${formatRupiah(data.harga_jual_ecer)}
                                        </td>
                                        @if (Auth::user()->hasPermission('edit_barang', 'hapus_barang'))        
                                            <td class="fit">
                                                <div class="row g-0 justify-content-end flex-nowrap gap-1">

                                                    @if (Auth::user()->hasPermission('edit_barang'))
                                                        <button type="button" class="col-5 btn btn-sm btn-warning text-white fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modaledit" data-bs-id="${data.id}">Edit</button>
                                                    @endif
                                                    @if (Auth::user()->hasPermission('hapus_barang'))
                                                        <button type="button" class="col-auto btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="${data.id}" data-bs-nama="${data.nama}">Hapus</button>
                                                    @endif
                                                    
                                                </div>
                                            </td>
                                        @endif
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
                                <option {{ request()->query('showing') == 'all' ? 'selected' : '' }} value="all">Semua
                                </option>
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

    {{-- <!-- Modal Edit--> --}}
    @if (Auth::user()->hasPermission('edit_barang'))
        <div class="modal modal-lg fade" id="modaledit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form method="POST" class="modal-content col-3 detail-pane" base-action="{{ url()->current() }}/" action="{{ Session::get('old_action') }}" action="{{ old('old_action') }}">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title m-0" id="staticBackdropLabel">Edit Data</h5>
                            <p class="m-0 fs-13px">Kolom dengan <span class="text-danger">*</span> wajib di isi</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3 row g-0 gap-3">
                        @csrf
                        @method('PUT')
                        <div class="col-5">
                            <div class="mb-2">
                                <label for="nama-edit" class="form-label fs-14px mustFilled">Nama Barang</label>
                                <input type="text" class="form-control form-control-sm @error('nama-edit') is-invalid @enderror" id="nama-edit" name="nama-edit" value="{{ old('nama-edit') }}">
                                <div class="invalid-feedback fs-13px">
                                    @error('nama-edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="barcode-edit" class="form-label fs-14px">Barcode</label>
                                <input type="text" class="form-control form-control-sm @error('barcode-edit') is-invalid @enderror" id="barcode-edit" name="barcode-edit" value="{{ old('barcode-edit') }}">
                                <div class="invalid-feedback fs-13px">
                                    @error('barcode-edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="kategori-edit" class="form-label fs-14px mustFilled">Kategori</label>
                                <select name="kategori-edit" id="kategori-edit" class="livesearch fs-14px @error('kategori-edit') is-invalid @enderror" data-placeholder="Pilih Kategori" data-ajax--url="/barang/kategori/cari">
                                    @if (Session::has('kategori-edit'))
                                        <option value="{{ Session::get('kategori-edit')->id }}" selected>{{ Session::get('kategori-edit')->nama }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback fs-13px">
                                    @error('kategori-edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="supplier-edit" class="form-label fs-14px mustFilled">Supplier</label>
                                <select name="supplier-edit" id="supplier-edit" class="livesearch fs-14px form-select-sm @error('supplier-edit') is-invalid @enderror" data-placeholder="Pilih Supplier" data-ajax--url="/barang/supplier/cari">
                                    @if (Session::has('supplier-edit'))
                                        <option value="{{ Session::get('supplier-edit')->id }}" selected>{{ Session::get('supplier-edit')->nama }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback fs-13px">
                                    @error('supplier-edit')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row g-0 gap-1 border p-2 pt-3 rounded floating-label" floating-title="Data Penjualan Grosir">
                                <div class="col">
                                    <label for="harga_beli_grosir-edit" class="form-label fs-14px mustFilled">Harga Beli Grosir</label>
                                    <input class="form-control form-control-sm" type="number" value="{{ old('harga_beli_grosir-edit', 0) }}" name="harga_beli_grosir-edit" hidden>
                                    <input class="form-control form-control-sm input-number-to-rupiah @error('harga_beli_grosir-edit') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_beli_grosir-edit'), 0, ',', '.') }}" id="harga_beli_grosir-edit">
                                </div>
                                <div class="col">
                                    <label for="harga_jual_grosir-edit" class="form-label fs-14px mustFilled">Harga Jual Grosir</label>
                                    <input class="form-control form-control-sm" type="number" value="{{ old('harga_jual_grosir-edit', 0) }}" name="harga_jual_grosir-edit" hidden>
                                    <input class="form-control form-control-sm input-number-to-rupiah @error('harga_jual_grosir-edit') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_jual_grosir-edit'), 0, ',', '.') }}" id="harga_jual_grosir-edit">
                                </div>
                                <div class="col">
                                    <label for="satuan_grosir-edit" class="form-label fs-14px mustFilled">Satuan</label>
                                    <input type="text" class="form-control form-control-sm @error('satuan_grosir-edit') is-invalid @enderror" value="{{ old('satuan_grosir-edit') }}" name="satuan_grosir-edit" id="satuan_grosir-edit">
                                </div>
                                <div class="invalid-feedback @error('harga_beli_grosir-edit') d-block @enderror @error('harga_jual_grosir-edit') d-block @enderror @error('satuan') d-block @enderror d-block fs-13px">
                                    <ul class="m-0">
                                        @error('harga_beli_grosir-edit')
                                            <li>
                                                {{ $message }}
                                            </li>
                                        @enderror
                                        @error('harga_jual_grosir-edit')
                                            <li>
                                                {{ $message }}
                                            </li>
                                        @enderror
                                        @error('satuan_grosir-edit')
                                            <li>
                                                {{ $message }}
                                            </li>
                                        @enderror
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check fs-14px">
                                    <input class="form-check-input" type="checkbox" name="with_eceran-edit" id="with_eceran-edit" {{ old('with_eceran-edit') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="with_eceran-edit">
                                        Jual Barang Secara Eceran?
                                    </label>
                                </div>
                            </div>

                            <fieldset id="fieldst-eceran-edit" class="border p-2 pt-3 rounded floating-label" floating-title="Data Penjualan Ecer" {{ old('with_eceran-edit') ? '' : 'disabled' }}>
                                <div class="row g-0 col gap-1 mb-3">
                                    <div class="col">
                                        <label for="isi_barang-edit" class="form-label fs-14px">Isi Barang Grosir</label>
                                        <div class="input-group input-group-sm has-validation">
                                            <p class="input-group-text m-0" id="isi_satuan_grosir-edit">1 {{ old('satuan_grosir-edit') }} =</p>
                                            <input type="hidden" value="{{ old('isi_barang-edit', 0) }}" name="isi_barang-edit">
                                            <input type="text" class="form-control input-number" placeholder="Masukan jumlah isi barang dari 1 satuan grosir" value="{{ number_format(old('isi_barang-edit'), 0, ',', '.') }}" id="isi_barang-edit">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label for="satuan_ecer-edit" class="form-label fs-14px">Satuan Ecer</label>
                                        <input type="text" class="form-control form-control-sm @error('satuan_ecer-edit') is-invalid @enderror" value="{{ old('satuan_ecer-edit') }}" name="satuan_ecer-edit" id="satuan_ecer-edit">
                                    </div>
                                    <div class="invalid-feedback @error('isi_barang-edit') d-block @enderror @error('satuan_ecer-edit') d-block @enderror fs-13px">
                                        <ul class="m-0">
                                            @error('isi_barang-edit')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                            @error('satuan_ecer-edit')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                        </ul>
                                    </div>
                                </div>
                                <div class="row g-0 gap-1">
                                    <div class="col">
                                        <label for="harga_beli_ecer-edit" class="form-label fs-14px">Harga Beli Ecer</label>
                                        <input class="form-control form-control-sm" type="number" value="{{ old('harga_beli_ecer-edit', 0) }}" name="harga_beli_ecer-edit" hidden>
                                        <input class="form-control form-control-sm input-number-to-rupiah @error('harga_beli_ecer-edit') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_beli_ecer-edit'), 0, ',', '.') }}" id="harga_beli_ecer-edit" disabled>
                                    </div>
                                    <div class="col">
                                        <label for="harga_jual_ecer-edit" class="form-label fs-14px">Harga Jual Ecer</label>
                                        <input class="form-control form-control-sm" type="number" value="{{ old('harga_jual_ecer-edit', 0) }}" name="harga_jual_ecer-edit" hidden>
                                        <input class="form-control form-control-sm input-number-to-rupiah @error('harga_jual_ecer-edit') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_jual_ecer-edit', 0), 0, ',', '.') }}" id="harga_jual_ecer-edit">
                                    </div>
                                    <div class="invalid-feedback @error('harga_beli_ecer-edit') d-block @enderror @error('harga_jual_ecer-edit') d-block @enderror fs-13px">
                                        <ul class="m-0">
                                            @error('harga_beli_ecer-edit')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                            @error('harga_jual_ecer-edit')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                        </ul>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                        <button type="submit" class="fw-semibold btn btn-sm btn-success">Tambah Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- <!-- Modal Tambah--> --}}
    @if (Auth::user()->hasPermission('tambah_barang'))
        <div class="modal modal-lg fade" id="modaltambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form method="POST" class="modal-content col-3 detail-pane" base-action="{{ url()->current() }}/" action="{{ Session::get('old_action') }}">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title m-0" id="staticBackdropLabel">Tambah Data</h5>
                            <p class="m-0 fs-13px">Kolom dengan <span class="text-danger">*</span> wajib di isi</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3 row g-0 gap-3">
                        @csrf
                        <div class="col-5">
                            <div class="mb-2">
                                <label for="nama" class="form-label fs-14px mustFilled">Nama Barang</label>
                                <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}">
                                <div class="invalid-feedback fs-13px">
                                    @error('nama')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="barcode" class="form-label fs-14px">Barcode</label>
                                <input type="text" class="form-control form-control-sm @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode') }}">
                                <div class="invalid-feedback fs-13px">
                                    @error('barcode')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="kategori" class="form-label fs-14px mustFilled">Kategori</label>
                                <select name="kategori" id="kategori" class="livesearch fs-14px @error('kategori') is-invalid @enderror" data-placeholder="Pilih Kategori" data-dropdownParent="#modaltambah" data-ajax--url="/barang/kategori/cari">
                                    @if (Session::has('kategori'))
                                        <option value="{{ Session::get('kategori')->id }}" selected>{{ Session::get('kategori')->nama }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback fs-13px">
                                    @error('kategori')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="supplier" class="form-label fs-14px mustFilled">Supplier</label>
                                <select name="supplier" id="supplier" class="livesearch fs-14px form-select-sm @error('supplier') is-invalid @enderror" data-placeholder="Pilih Supplier" data-dropdownParent="#modaltambah" data-ajax--url="/barang/supplier/cari">
                                    @if (Session::has('supplier'))
                                        <option value="{{ Session::get('supplier')->id }}" selected>{{ Session::get('supplier')->nama }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback fs-13px">
                                    @error('supplier')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row g-0 gap-1 border p-2 pt-3 rounded floating-label" floating-title="Data Penjualan Grosir">
                                <div class="col">
                                    <label for="harga_beli_grosir" class="form-label fs-14px mustFilled">Harga Beli Grosir</label>
                                    <input class="form-control form-control-sm" type="number" value="{{ old('harga_beli_grosir', 0) }}" name="harga_beli_grosir" hidden>
                                    <input class="form-control form-control-sm input-number-to-rupiah @error('harga_beli_grosir') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_beli_grosir'), 0, ',', '.') }}" id="harga_beli_grosir">
                                </div>
                                <div class="col">
                                    <label for="harga_jual_grosir" class="form-label fs-14px mustFilled">Harga Jual Grosir</label>
                                    <input class="form-control form-control-sm" type="number" value="{{ old('harga_jual_grosir', 0) }}" name="harga_jual_grosir" hidden>
                                    <input class="form-control form-control-sm input-number-to-rupiah @error('harga_jual_grosir') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_jual_grosir'), 0, ',', '.') }}" id="harga_jual_grosir">
                                </div>
                                <div class="col">
                                    <label for="satuan_grosir" class="form-label fs-14px mustFilled">Satuan</label>
                                    <input type="text" class="form-control form-control-sm @error('satuan_grosir') is-invalid @enderror" value="{{ old('satuan_grosir') }}" name="satuan_grosir" id="satuan_grosir">

                                </div>
                                <div class="invalid-feedback @error('harga_beli') d-block @enderror @error('harga_jual_grosir') d-block @enderror @error('satuan') d-block @enderror d-block fs-13px">
                                    <ul class="m-0">
                                        @error('harga_beli')
                                            <li>
                                                {{ $message }}
                                            </li>
                                        @enderror
                                        @error('harga_jual_grosir')
                                            <li>
                                                {{ $message }}
                                            </li>
                                        @enderror
                                        @error('satuan_grosir')
                                            <li>
                                                {{ $message }}
                                            </li>
                                        @enderror
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check fs-14px">
                                    <input class="form-check-input" type="checkbox" name="with_eceran" id="with_eceran" {{ old('with_eceran') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="checkbox-eceran">
                                        Jual Barang Secara Eceran?
                                    </label>
                                </div>
                            </div>

                            <fieldset id="fieldst-eceran" class="border p-2 pt-3 rounded floating-label" floating-title="Data Penjualan Ecer" {{ old('with_eceran') ? '' : 'disabled' }}>
                                <div class="row g-0 col gap-1 mb-3">
                                    <div class="col">
                                        <label for="isi_barang" class="form-label fs-14px">Isi Barang Grosir</label>
                                        <div class="input-group input-group-sm has-validation">
                                            <p class="input-group-text m-0" id="isi_satuan_grosir">1 {{ old('satuan_grosir') }} =</p>
                                            <input type="hidden" value="{{ old('isi_barang', 0) }}" name="isi_barang">
                                            <input type="text" class="form-control input-number @error('isi_barang') is-invalid @enderror" placeholder="Masukan jumlah isi barang dari 1 satuan grosir" value="{{ number_format(old('isi_barang'), 0, ',', '.') }}" id="isi_barang">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label for="satuan_ecer" class="form-label fs-14px">Satuan Ecer</label>
                                        <input type="text" class="form-control form-control-sm @error('satuan_ecer') is-invalid @enderror" value="{{ old('satuan_ecer') }}" name="satuan_ecer" id="satuan_ecer">
                                    </div>
                                    <div class="invalid-feedback @error('isi_barang') d-block @enderror @error('satuan_ecer') d-block @enderror fs-13px">
                                        <ul class="m-0">
                                            @error('isi_barang')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                            @error('satuan_ecer')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                        </ul>
                                    </div>
                                </div>
                                <div class="row g-0 gap-1">
                                    <div class="col">
                                        <label for="harga_beli_ecer" class="form-label fs-14px">Harga Beli Ecer</label>
                                        <input class="form-control form-control-sm" type="number" value="{{ old('harga_beli_ecer', 0) }}" name="harga_beli_ecer" hidden>
                                        <input class="form-control form-control-sm input-number-to-rupiah @error('harga_beli_ecer') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_beli_ecer'), 0, ',', '.') }}" id="harga_beli_ecer" disabled>
                                    </div>
                                    <div class="col">
                                        <label for="harga_jual_ecer" class="form-label fs-14px">Harga Jual Ecer</label>
                                        <input class="form-control form-control-sm" type="number" value="{{ old('harga_jual_ecer', 0) }}" name="harga_jual_ecer" hidden>
                                        <input class="form-control form-control-sm input-number-to-rupiah @error('harga_jual_ecer') is-invalid @enderror" type="text" value="{{ 'Rp ' . number_format(old('harga_jual_ecer', 0), 0, ',', '.') }}" id="harga_jual_ecer">
                                    </div>
                                    <div class="invalid-feedback @error('harga_beli_ecer') d-block @enderror @error('harga_jual_ecer') d-block @enderror fs-13px">
                                        <ul class="m-0">
                                            @error('harga_beli_ecer')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                            @error('harga_jual_ecer')
                                                <li>
                                                    {{ $message }}
                                                </li>
                                            @enderror
                                        </ul>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                        <button type="submit" class="fw-semibold btn btn-sm btn-success">Tambah Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Hapus --}}
    @if (Auth::user()->hasPermission('hapus_barang'))
        <div class="modal fade" id="modalhapus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" base-action="/barang/">
                    @csrf
                    @method('delete')
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="m-0">Apakah anda yakin ingin menghapus barang "<strong class="nama"></strong>" ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                        <button type="submit" class="fw-semibold btn btn-sm btn-danger">Hapus Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        const modaledit = document.getElementById('modaledit');
        if (modaledit) {
            modaledit.addEventListener('hidden.bs.modal', event => {
                modaledit.querySelector('form').reset();
                modaledit.querySelectorAll('.is-invalid').forEach((el) => {
                    el.classList.remove('is-invalid');
                });
                modaledit.querySelectorAll('.invalid-feedback.d-block').forEach((el) => {
                    el.classList.remove('d-block');
                });
                modaledit.querySelector("#with_eceran-edit").checked = false;
                document.getElementById("fieldst-eceran-edit").setAttribute("disabled", true);
            });

            modaledit.addEventListener('show.bs.modal', event => {
                try {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-bs-id');
                    const textButton = button.innerHTML;
                    button.innerHTML = '<i class="fa-regular fa-spinner-third fa-spin"></i>';
                    event.preventDefault();

                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', currenturl + "/" + id);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                let datas = JSON.parse(xhr.responseText);
                                console.log(datas);
                                for (const key in datas) {
                                    const input = modaledit.querySelector('#' + key + "-edit");

                                    if (key == 'id') {
                                        modaledit.querySelector('form').setAttribute('action', modaledit.querySelector('form').getAttribute('base-action') + datas[key])
                                    } else if (key == 'supplier' || key == 'kategori') {
                                        input.innerHTML = `<option value="${ datas[key].id }">${ datas[key].nama }</option>`
                                    } else if (key == 'with_eceran' && datas['with_eceran'] == 'on') {
                                        input.checked = true;
                                        document.getElementById("fieldst-eceran-edit").removeAttribute("disabled");
                                    } else if (input && key != 'with_eceran') {
                                        if (input.classList.contains('input-number-to-rupiah')) {
                                            input.value = formatRupiah(datas[key]);
                                            input.previousElementSibling.value = datas[key];
                                        } else if (input.classList.contains('input-number')) {
                                            input.value = numberFormat(datas[key]);
                                            input.previousElementSibling.value = datas[key];
                                        } else {
                                            input.value = datas[key];
                                        }
                                    }
                                }
                                const modal = new bootstrap.Modal(modaledit);
                                modal.show();
                                button.innerHTML = textButton;
                            } else {
                                console.error('Error:', xhr.statusText);
                            }
                        }
                    };

                    xhr.send();
                } catch (error) {

                }
            });
        }
    </script>
@endsection
