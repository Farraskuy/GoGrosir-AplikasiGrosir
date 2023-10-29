@extends('kasir.layout')

@section('title', 'Kasir | ' . nama_aplikasi())
@section('content')

    <span class="d-none urlDataTable">{{ route('kasir.getDataBarang') }}</span>
    <span class="d-none typeDataTable">barang</span>
    <span class="d-none appSettings">{{ $appSettings }}</span>

    <section class="row g-0 h-100 p-3 gap-4">
        <div class="col-5" style="height: calc(100% - 31px)">
            <div class="col-auto position-relative mb-3">
                <i class="fa-regular fa-search position-absolute" style="top: 50%; left: 17px; transform: translateY(-55%)"></i>
                <input type="text" name="search" id="search" class="form-control form-control-lg fs-14px ps-5" placeholder="Cari Barang...">
                <span class="badge text-bg-secondary position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%)">Ctrl + Space</span>
            </div>
            <div class="rounded-3 bg-white overflow-hidden position-relative" style="height: calc(100% - 1.5rem)">

                {{-- loading --}}
                <div class="position-absolute w-100 d-flex justify-content-center align-items-center" id="table-loader" style=" z-index: 1021; height: calc(100% - 52px);">
                    <div class="p-3 rounded-3 fw-semibold text d-flex gap-2 justify-content-center align-items-center" style="background-color: #e5fbef; color: #007a33;">
                        <i class="fa-regular fa-spinner-third fa-spin" style="font-size: 20px"></i>
                    </div>
                </div>

                <div class="overflow-y-scroll position-relative" style="height: calc(100% - 50px)">

                    <table class="table table-hover fs-14px">
                        <thead class="sticky-top">
                            <th class="ps-3">Nama Barang</th>
                            <th class="fit text-end">Harga Jual</th>
                        </thead>
                        <tbody id="tbody-barang"></tbody>
                    </table>
                </div>
                <div class="p-2 border-top fs-13px d-flex justify-content-center pagination">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item">
                                <p class="page-link " style="font-size: 13px" href="#">Previous</p>
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
        <form id="form_keranjang" method="post" class="col rounded-3 bg-white fs-14px">
            @csrf
            <div class="p-3 pt-2 pb-0 overflow-y-auto" style="height: calc(100% - 34px)">
                <div class="row g-0 border-bottom align-items-center flex-nowrap pb-2">
                    <div class="col" style="width: 350px">
                        <table>
                            <tr>
                                <td style="white-space: nowrap">Nomor Transaksi</td>
                                <td class="px-2">:</td>
                                <td class="placeholder-wave w-100 nomor-transaksi">
                                    <span class="placeholder w-100 rounded-5"></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-auto " style="width: 290px">
                        <table>
                            <tr>
                                <td>Tanggal</td>
                                <td class="px-2">:</td>
                                <td class="placeholder-wave w-100 tanggal">
                                    <span class="placeholder w-100 rounded-5"></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-auto ps-2 me-2">
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa-regular fa-trash" id="btn_clear_keranjang"></i></button>
                    </div>
                </div>

                <div class="position-relative" style="height: calc(127px * 2 + 58px); overflow-y: auto">
                    <table class="table table-keranjang m-0">
                        <thead class="sticky-top bg-light">
                            <th>Nama Barang</th>
                            <th class="fit text-center">Jumlah Beli</th>
                            <th class="fit text-center">Satuan</th>
                            <th class="fit">Subtotal</th>
                            <th class="fit">Aksi</th>
                        </thead>
                    </table>
                </div>

                <div class="border-top border-dark" style="height: fit-content;">
                    <table class="table m-0">
                        <tr class="fw-semibold">
                            <td>Total Bayar</td>
                            <td class="text-end text-total-bayar">Rp 0</td>
                        </tr>
                        <tr>
                            <td>Potongan</td>
                            <td class="text-end text-total-potongan">Rp 0</td>
                        </tr>
                        <tr class="border-white fw-bold">
                            <td class="text-success">Total</td>
                            <td class="text-end text-total text-success">Rp 0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <button disabled type="button" data-bs-toggle="modal" data-bs-target="#modal_pembayaran" class="btn btn-sm w-100 btn-success fw-semibold text-white btn-pembayaran" style="height: 34px">Bayar</button>

            <div class="modal fade" id="modal_pembayaran" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Pembayaran</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="total_bayar" class="form-label">Total Bayar</label>
                                <input type="text" class="form-control form-control-sm" id="totalBayar" disabled>
                            </div>
                            <div class="mb-2">
                                <label for="bayar" class="form-label">Bayar</label>
                                <input type="hidden" name="bayar">
                                <input type="text" class="form-control form-control-sm" id="bayar">
                            </div>
                            <div class="mb-2">
                                <label for="kembali" class="form-label">Kembali</label>
                                <input type="text" class="form-control form-control-sm" id="kembali" value="Rp 0" disabled>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <span class="badge fs-14px text-bg-primary keterangan-pembayaran">Lunas</span>
                            <div>
                                <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-success fw-semibold" id="btn_bayar"><i class="fa-duotone fa-spinner-third fa-spin"></i> Bayar</button>
                            </div>
                        </div>
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
                            <input type="checkbox" class="form-checkbox" name="selalu-cetak" id="selalu_cetak">
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
