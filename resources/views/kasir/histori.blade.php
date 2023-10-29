@extends('kasir.layout')

@section('title', 'Histori Penjualan | ' . nama_aplikasi())
@section('content')

    <span class="d-none urlDataTable">{{ route('penjualan.getDataHistori') }}</span>
    <span class="d-none typeDataTable">barang</span>

    <section class="row g-0 h-100 p-3 gap-4 flex-nowrap">
        <div class="col-5 h-100">
            <div class="rounded-3 bg-white overflow-hidden position-relative h-100">

                {{-- loading --}}
                <div class="position-absolute w-100 d-flex justify-content-center align-items-center" id="table-loader" style=" z-index: 1021; height: calc(100% - 52px);">
                    <div class="p-3 rounded-3 fw-semibold text d-flex gap-2 justify-content-center align-items-center" style="background-color: #e5fbef; color: #007a33;">
                        <i class="fa-regular fa-spinner-third fa-spin" style="font-size: 20px"></i>
                    </div>
                </div>

                <div class="overflow-y-auto position-relative" style="height: calc(100% - 50px)">
                    <table class="table table-hover fs-14px">
                        <thead class="sticky-top">
                            <th class="ps-3">#</th>
                            <th class="ps-3">Nomor Transaksi</th>
                            <th class="fit text-center">Tanggal</th>
                            <th class="fit text-center pe-3">Total</th>
                        </thead>
                        <tbody id="table_data"></tbody>
                    </table>
                </div>
                <div class="p-2 border-top fs-13px d-flex justify-content-center pagination">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item">
                                <p class="page-link" style="font-size: 13px" href="#">Previous</p>
                            </li>
                            <li class="page-item"><a class="page-link disabled" style="font-size: 13px" href="#">...</a></li>
                            <li class="page-item">
                                <p class="page-link " style="font-size: 13px" href="#">Next</p>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <form class="col rounded-3 bg-white fs-14px form-histori">
            <div class="p-3 py-2 row g-0 gap-1 align-items-center border-bottom pb-2 justify-content-between">
                <span class="col m-0 fw-semibold">Detail Penjualan</span>
                <div class="col-auto wrapper-btn-aksi">
                    <div class="col-auto btn-group-edit-cetak">
                        {{-- <button type="button" id="btn_edit" disabled class="btn btn-sm btn-warning text-white fw-semibold"><i class="fa-regular fa-pen-to-square"></i> Edit</button> --}}
                        <button type="button" id="btn_cetak_ulang" disabled class="btn btn-sm btn-primary fw-semibold"><i class="fa-solid fa-print"></i> Cetak Ulang Struk <i class="fa-duotone fa-spinner-third fa-spin"></i></button>
                    </div>
                </div>
            </div>
            <div class="detail-penjualan-container info position-relative" style="height: calc(100% - 55px)">

                {{-- loading --}}
                <div class="d-flex position-absolute w-100 justify-content-center align-items-center" id="loading" style=" z-index: 1021; height: calc(100% - 52px);">
                    <div class="p-3 rounded-3 fw-semibold text d-flex gap-2 justify-content-center align-items-center" style="background-color: #e5fbef; color: #007a33;">
                        <i class="fa-regular fa-spinner-third fa-spin" style="font-size: 20px"></i>
                    </div>
                </div>

                <div class="p-3 overflow-y-auto h-100" id="scroller">

                    <div class="flex-column justify-content-center info align-items-center w-100 h-100">
                        <div class="alert alert-success d-flex align-items-center gap-2">
                            <i class="fa-regular fa-circle-info fs-3" style="color: #007a33"></i>
                            <span class="m-0 fw-medium">Klik Data Pada Tabel di Kiri Untuk Melihat Detail, Mengedit Atau Mencetak Ulang</span>
                        </div>
                    </div>

                    <div class="detail-penjualan-wrapper">
                        <table class="text-secondary">
                            <tr>
                                <th>Nomor Transaksi</th>
                                <td class="px-2">:</td>
                                <td class="nomor-transaksi">${data.no_trans}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td class="px-2">:</td>
                                <td class="tanggal">${dateformat}</td>
                            </tr>
                            <tr>
                                <th>Kasir</th>
                                <td class="px-2">:</td>
                                <td class="nama-kasir">${data.user.has_petugas.nama}</td>
                            </tr>
                        </table>
                        <hr class="mb-0">
                        <div class="table-responsive tabel-histori-detail-penjualan">

                            <table class="table" id="tdetaildata"></table>

                        </div>
                        <div style="height: fit-content;">
                            <table class="table m-0">
                                <tr class="fw-semibold">
                                    <td>Total Bayar</td>
                                    <td class="text-end text-total-bayar">Rp 0</td>
                                </tr>
                                <tr>
                                    <td>Total Potongan</td>
                                    <td class="text-end text-total-potongan">Rp 0</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td class="text-success">Total</td>
                                    <td class="text-end text-total text-success">Rp 0</td>
                                </tr>
                                <tr class="border-white fw-bold fs-15px">
                                    <td class="text-primary">Bayar</td>
                                    <td class="text-end text-bayar text-primary">Rp 0</td>
                                </tr>
                            </table>
                        </div>
                        <hr class="my-2 border-dark" style="opacity: 1">
                        <table class="table m-0">
                            <tr class="fw-semibold fs-15px" style="border-color: transparent">
                                <td>Kembali</td>
                                <td class="text-end text-kembali">Rp 0</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <div class="modal fade" id="modal_cetak" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Transaksi Berhasil</h1>
                </div>
                <div class="modal-body row g-0 align-items-center">
                    <div class="col-auto">
                        <h5 class="m-0">Kembalian</h5>
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <h3 class="m-0" id="kembalian">Rp 0</h3>
                    </div>
                    <button class="btn btn-sm btn-primary fw-semibold flex-grow-1 mt-3" id="btn_cetak_struk" onclick="cetakStruk(this.getAttribute('data-id-transaksi'))"><i class="fa-solid fa-print"></i> Cetak Struk <i class="fa-duotone fa-spinner-third fa-spin"></i></button>
                </div>
                <div class="modal-footer justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div>
                            <input type="checkbox" class="form-checkbox" name="selalu-cetak" id="selalu_cetak" onchange="setSelaluCetakStruk(this)">
                            <i style="display: none" class="fa-duotone fa-spinner-third fa-spin"></i>
                        </div>
                        <label for="selalu_cetak" class="small">Selalu Cetak Struk Setelah Transaksi Berhasil?</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-success fw-semibold" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>


@endsection
