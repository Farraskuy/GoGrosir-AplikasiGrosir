@extends('layout')

@section('title', 'Data Penjualan | ' . nama_aplikasi())

@section('content')
    <section class="p-3">
        <h4 class="fw-semibold">Data Penjualan</h4>
        <a href="{{ url('penjualan/tambah') }}" class="btn btn-sm btn-success fw-semibold mb-3">Tambah Data</a>
        <div class="row g-0 gap-3">
            <form method="get" id="form_filter" onchange="filterData(this)" class="col rounded-3 bg-white p-3 pt-0" style="height: fit-content">
                <div class="alert-container"></div>
                <div class="bg-white position-sticky pt-3 pb-2" style="top: 61px">
                    <div class="d-flex gap-2 justify-content-end mb-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari" value="{{ request()->query('keyword', '') }}" name="keyword" oninput="searchData(this)">
                        <div class="dropdown">
                            <button type="button" class="btn border text-nowrap fs-14px h-100 d-flex gap-2 align-items-center 
                                {{ request()->query('tanggal_awal') != '' || request()->query('tanggal_akhir') != '' ? 'btn-info text-white fw-semibold' : '' }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,10" data-bs-auto-close="outside">
                                Tanggal Transaksi
                                <i class="fa-regular fa-angle-down"></i>
                            </button>
                            <div class="dropdown-menu p-3 py-2 shadow-sm dropdown-menu-end">
                                <div class="d-flex gap-2">
                                    <div class="mb-2">
                                        <label for="tanggal_awal" class="form-label fs-14px">Tanggal Awal</label>
                                        <div class="d-flex gap-1">
                                            <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ request()->query('tanggal_awal', '') }}" class="form-control fs-14px">
                                            @if (request()->query('tanggal_awal') != '' )
                                                <button type="button" onclick="clearFilterTanggal(this)" class="btn btn-outline-danger fs-14px"><i class="fa-regular fa-xmark"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="tanggal_akhir" class="form-label fs-14px">Tanggal Akhir</label>
                                        <div class="d-flex gap-1">
                                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request()->query('tanggal_akhir', '') }}" class="form-control fs-14px">
                                            @if (request()->query('tanggal_akhir') != '' )
                                                <button type="button" onclick="clearFilterTanggal(this)" class="btn btn-outline-danger fs-14px"><i class="fa-regular fa-xmark"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <select class="form-select fs-14px h-100 w-auto" style="line-height: 1.7" name="filtered_by">
                            <option value="" {{ request()->query('filtered_by') == '' ? 'selected' : '' }}>Urutkan berdasarkan</option>
                            <option value="created_at" {{ request()->query('filtered_by') == 'created_at' ? 'selected' : '' }}>Tanggal Transaksi</option>
                            <option value="id_petugas" {{ request()->query('filtered_by') == 'id_petugas' ? 'selected' : '' }}>Petugas Kasir Yang Melayani</option>
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
                                    <option {{ request()->query('showing') == 'all' ? 'selected' : '' }} value="all">Semua</option>
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
                                <th scope="col" class="fit">Nomor Transaksi</th>
                                <th scope="col" class="px-3">Kasir</th>
                                <th scope="col" class="fit">Tanggal</th>
                                <th scope="col" class="fit">Total Bayar</th>
                                <th scope="col" class="fit">Total Potongan</th>
                                <th scope="col" class="fit">Total</th>
                                <th class="text-center" scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($data as $item)
                                <tr>
                                    <th class="fit" scope="row">{{ $nourut++ }}</th>
                                    <td>{{ $item->no_trans }}</td>
                                    <td class="px-3">{{ $item->user->nama }}</td>
                                    <td class="fit">{{ format_tanggal($item->created_at) }}</td>
                                    <td class="fit text-end">{{ 'Rp' . number_format($item->total_bayar, 0, ',', '.') }}</td>
                                    <td class="fit text-end">{{ 'Rp' . number_format($item->total_potongan, 0, ',', '.') }}</td>
                                    <td class="fit text-end">{{ 'Rp' . number_format($item->total_bayar - $item->total_potongan, 0, ',', '.') }}</td>
                                    <td class="fit"><button type="button" class="btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="{{ $item->no_trans }}" data-bs-nama="{{ $item->no_trans }}">Hapus</button></td>
                                </tr>
                            @endforeach

                            <script>
                                function rowData(data, nourut) {
                                    const tr = document.createElement("tr");
                                    // const date =;
                                    const dateformat = new Intl.DateTimeFormat("id", {
                                        day: "2-digit",
                                        month: "long",
                                        year: "numeric",
                                    }).format(new Date(data.created_at));
                                    tr.innerHTML = `
                                        <th class="fit" scope="row">${ nourut }</th>
                                        <td>${ data.no_trans }</td>
                                        <td class="px-3">${ data.user.nama }</td>
                                        <td class="fit">${ dateformat }</td>
                                        <td class="fit text-end">${ formatRupiah(data.total_bayar) }</td>
                                        <td class="fit text-end">${ formatRupiah(data.total_potongan) }</td>
                                        <td class="fit text-end">${ formatRupiah(data.total_bayar - data.total_potongan) }</td>
                                        <td class="fit"><button type="button" class="btn btn-sm btn-danger fw-semibold fs-13px" data-bs-toggle="modal" data-bs-target="#modalhapus" data-bs-id="${ data.no_trans }" data-bs-nama="${ data.no_trans }">Hapus</button></td>
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

    <div class="modal fade" id="modalhapus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" base-action="/barang/penjualan/">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="m-0">Apakah anda yakin ingin menghapus penjualan "<strong class="nama"></strong>" ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">batal</button>
                    <button type="submit" class="fw-semibold btn btn-sm btn-danger">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection
