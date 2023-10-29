@extends('layout')

@section('title', 'Kategori | ' . nama_aplikasi())

@section('content')
    <section class="p-3">
        @if (Auth::user()->hasPermission('tambah_kategori'))
            <h4 class="fw-semibold">Daftar Kategori</h4>
            <button class="btn btn-sm btn-success fw-semibold mb-3" data-bs-toggle="modal" data-bs-target="#modaltambah">Tambah Data</button>
        @else
            <h4 class="fw-semibold mb-3">Daftar Kategori</h4>
        @endif

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
                                <th scope="col">Nama</th>
                                <th scope="col">Keterangan</th>
                                @if (Auth::user()->hasPermission('hapus_kategori'))
                                    <th class="text-center" scope="col">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($data as $item)
                                <tr onclick="getDetail('/barang/kategori/{{ $item->id }}')">
                                    <th class="fit" scope="row">{{ $nourut++ }}</th>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    @if (Auth::user()->hasPermission('hapus_kategori'))
                                        <td class="fit">
                                            <button type="button" class="btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="{{ $item->id }}" data-bs-nama="{{ $item->nama }}">Hapus</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            <script>
                                function rowData(data, nourut) {
                                    const tr = document.createElement("tr");
                                    @if (Auth::user()->hasPermission('edit_kategori'))
                                        tr.onclick = () => {
                                            getDetail(`/barang/kategori/${ data.id }`)
                                        };
                                    @endif

                                    tr.innerHTML = `
                                        <th class="fit" scope="row">${ nourut }</th>
                                        <td>${ data.nama }</td>
                                        <td>${ data.keterangan }</td>
                                        @if (Auth::user()->hasPermission('hapus_kategori'))        
                                            <td class="fit">
                                                <button type="button" class="btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="${ data.id }" data-bs-nama="${ data.nama }">Hapus</button>
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

            @if (Auth::user()->hasPermission('edit_kategori'))
                <form method="POST" class="col-3 detail-pane is-invalid" base-action="{{ url()->current() }}/" action="{{ Session::get('old_action') }}">
                    <div class="rounded-3 bg-white p-3 position-sticky" style="top: 75px">
                        @csrf
                        @method('put')
                        <h5 class="mb-0 fs-16px">Detail</h5>
                        <small class="text-secondary fw-semibold fs-13px">Klik Tabel untuk melihat detail/megubah</small>
                        <hr class="my-2">
                        <div class="mb-2">
                            <label for="nama-edit" class="form-label fs-14px">Nama Kategori</label>
                            <input type="text" class="form-control form-control-sm @error('nama-edit') is-invalid @enderror" id="nama-edit" name="nama-edit" value="{{ old('nama-edit') }}">
                            <div class="invalid-feedback fs-13px">
                                @error('nama-edit')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="keterangan-edit" class="form-label fs-14px">Keterangan</label>
                            <textarea type="text" class="form-control form-control-sm @error('keterangan-edit') is-invalid @enderror" id="keterangan-edit" name="keterangan-edit">{{ old('keterangan-edit') }}</textarea>
                            <div class="invalid-feedback fs-13px">
                                @error('keterangan-edit')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="row g-0 gap-2">
                            <button type="button" class="col btn btn-sm btn-warning fw-semibold text-white" data-bs-toggle="modal" data-bs-target="#confirm">Ubah data</button>
                            <button type="reset" class="col-3 btn btn-sm btn-secondary fw-semibold text-white">Batal</button>
                        </div>
                        <p class="mb-0 mt-1 text-danger" id="invalid-feedback"></p>
                    </div>

                    <div class="modal fade" id="confirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Edit</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="m-0">Apakah anda yakin ingin mengganti kategori ini ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                                    <button type="submit" class="fw-semibold btn btn-sm btn-warning text-white">Edit
                                        Data</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

        </div>
    </section>

    @if (Auth::user()->hasPermission('tambah_kategori'))
        {{-- <!-- Modal Tambah--> --}}
        <div class="modal fade" id="modaltambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="nama" class="form-label fs-14px">Nama Kategori</label>
                            <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}">
                            <div class="invalid-feedback">
                                @error('nama')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="keterangan" class="form-label fs-14px">Keterangan</label>
                            <textarea type="text" class="form-control form-control-sm @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan">{{ old('keterangan') }}</textarea>
                            <div class="invalid-feedback">
                                @error('keterangan')
                                    {{ $message }}
                                @enderror
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
    @endif

    @if (Auth::user()->hasPermission('hapus_kategori'))
        {{-- Modal Hapus --}}
        <div class="modal fade" id="modalhapus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" base-action="/barang/kategori/">
                    @csrf
                    @method('delete')
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="m-0">Apakah anda yakin ingin menghapus kategori "<strong class="nama"></strong>" ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                        <button type="submit" class="fw-semibold btn btn-sm btn-danger">Hapus Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection
