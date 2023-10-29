@extends('layout')

@section('title', 'Transaksi Penjualan | ' . nama_aplikasi())
@section('content')
    <section class="p-3">
        <div class="row g-0">
            <form method="POST" onsubmit="(event) => event.preventDefault()" class="form-keranjang col rounded-3 bg-white p-3 py-2" style="height: fit-content">
                @csrf
                <div class="border-bottom p-2">
                    <div class="d-flex align-items-center gap-3 border-bottom mb-2 pb-3">
                        <a href="{{ route('penjualan') }}" class="btn btn-sm btn-outline-secondary fw-semibold m-0 d-flex gap-2 align-items-center">
                            <i class="fa-regular fa-arrow-left"></i>
                            <span class="fs-13px">Kembali</span>
                        </a>
                        <h5 class="fw-semibold m-0">Transaksi Penjualan</h5>
                    </div>
                    <table class="table table-borderless my-3 fs-14px">
                        <tr>
                            <th class="fit">Nomor Transaksi</th>
                            <td class="fit">:</td>
                            <td class="nomor-transaksi placeholder-wave"><span class="placeholder rounded-5" style="width: 200px"></span></td>
                        </tr>
                        <tr>
                            <th class="fit">Tanggal, Waktu</th>
                            <td class="fit">:</td>
                            <td class="tanggal placeholder-wave"><span class="placeholder rounded-5" style="width: 200px"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="py-2">
                    <div class="d-flex gap-2 mb-3 mt-1 col-6 position-relative">
                        <div class="input-group input-group-sm ">
                            <span class="input-group-text"><i class="fa-regular fa-magnifying-glass"></i></span>
                            <input type="search" class="form-control search-barang" placeholder="Cari Barcode Barang">
                        </div>
                        <div class="dropdown-search-barang hide position-absolute bg-white shadow-lg rounded-3 border d-flex flex-column p-2" style="z-index: 1; left: 0; right: 0; top: 40px">
                            <small class="fw-semibold text-secondary p-2 pt-1 border-bottom">Pilih Barang</small>
                            <div class="overflow-y-auto" style="max-height: 250px">
                                <table class="table fs-14px table-hover">
                                    <thead class="sticky-top">
                                        <th class="ps-3">Nama Barang</th>
                                        <th class="fit text-end">Harga Jual</th>
                                    </thead>
                                    <tbody class="tbody-dropdown-search">
                                        <td colspan="3" class="text-center py-2 loading">
                                            Memuat Data <i class="fa-regular fa-spinner-third fa-spin"></i>
                                        </td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success fw-semibold text-nowrap" data-bs-toggle="modal" data-bs-target="#modalbarang"><i class="fa-regular fa-plus"></i> Tambah Barang</button>
                    </div>
                    <table class="table table-bordered fs-13px align-middle m-0">
                        <thead class="table-secondary">
                            <th class="fit">#</th>
                            <th>Nama Barang</th>
                            <th class="fit">Harga Jual</th>
                            <th class="fit">Satuan</th>
                            <th class="fit">Jumlah Beli</th>
                            <th class="fit">Subtotal</th>
                            <th class="fit">Potongan</th>
                            <th class="fit">Total</th>
                            <th class="fit"></th>
                        </thead>
                        <tbody class="table-keranjang fs-14px">
                            <tr class="intro">
                                <td colspan="9" class="text-secondary">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="alert alert-success my-2" style="width: fit-content; z-index: 0;">
                                            <small class="fw-semibold"><i class="fa-regular fa-lightbulb me-2"></i>Informasi</small>
                                            <hr class="my-2">
                                            <ul class="fs-14px m-0">
                                                <li>Gunakan cari barcode untuk mencari "Nama" atau "Barcode" Barang</li>
                                                <li>Gunakan tombol "Tambah Barang" untuk memilih banyak barang</li>
                                                <li>Jika mencari barang menggunakan "Barcode", Akan langsung menambahkan barangnya</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-top d-flex flex-column align-items-end p-2 pe-4">
                    <table class="fs-14px">
                        <tr>
                            <th class="px-2 py-1">Subtotal Bayar</th>
                            <td class="px-2 py-1"><input type="text" class="form-control form-control-sm text-end input-total-bayar" value="Rp 0" disabled></td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1">Total Potongan</th>
                            <td class="px-2 py-1"><input type="text" class="form-control form-control-sm text-end input-total-potongan" value="Rp 0" disabled></td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1">Total Bayar</th>
                            <td class="px-2 py-1"><input type="text" class="form-control form-control-sm text-end input-total" value="Rp 0" disabled></td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 pb-3">Bayar</th>
                            <td class="px-2 py-1 pb-3">
                                <input name="bayar" type="hidden" class="form-control form-control-sm text-end" value="0">
                                <input id="bayar" type="text" class="form-control form-control-sm text-end input-number-to-rupiah" value="Rp 0">
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="px-2 py-1 pt-3">Kembalian</th>
                            <td class="px-2 py-1 pt-3"><input id="kembali" type="text" class="form-control form-control-sm text-end" value="Rp 0" disabled></td>
                        </tr>
                        <tr>
                            <td class="px-2 py-1 pt-2" colspan="2">
                                <button type="submit" class="btn-simpan-transaksi btn btn-sm btn-success fw-semibold btn-pembayaran w-100 d-flex gap-3 justify-content-center align-items-center" disabled><i class="fa-duotone fa-spinner-third fa-spin"></i> Simpan Transaksi</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-2 py-1 pt-2" colspan="2">
                                <span class="badge fs-14px text-bg-primary keterangan-pembayaran">Lunas</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </section>

    <div class="modal modal-lg fade" id="modalbarang" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content h-100">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Daftar Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white loading" style="z-index: 1; top: 47.5px; bottom: 0; right: 0; left: 0;">
                        <div class="p-3 fs-1"><i class="fa-duotone fa-spinner-third fa-spin"></i></div>
                    </div>
                    <div class="p-2 border-bottom d-flex justify-content-between">
                        <div class="input-group input-group-sm" style="max-width: 50%">
                            <span class="input-group-text"><i class="fa-regular fa-magnifying-glass"></i></span>
                            <input type="search" class="form-control search" placeholder="Cari Barang">
                        </div>
                        <div class="d-flex fs-14px align-items-center gap-1">
                            Menampilkan
                            <select class="form-select form-select-sm w-auto jumlah-tampil">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            Data
                        </div>
                    </div>
                    <table class="table table-hover align-middle fs-14px">
                        <thead class="sticky-top">
                            <th class="ps-3">Nama Barang</th>
                            <th class="fit">Satuan</th>
                            <th class="fit text-end">Harga Jual</th>
                            <th class="fit text-center">Aksi</th>
                        </thead>
                        <tbody id="tbody-barang-modal-terpilih"></tbody>
                        <tbody id="tbody-barang-modal"></tbody>
                    </table>
                </div>
                <div class="modal-footer pagination-wrapper">
                    <ul class="pagination m-0">
                        <li class="page-item">
                            <p class="page-link m-0" style="font-size: 13px">Previous</p>
                        </li>
                        <li class="page-item"><a class="page-link disabled" style="font-size: 13px">...</a></li>
                        <li class="page-item">
                            <p class="page-link m-0" style="font-size: 13px">Next</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

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
                            <input type="checkbox" class="form-checkbox" name="selalu-cetak" id="selalu_cetak" {{ appSettings()->selalu_cetak_struk == 'true' ? 'checked' : '' }}>
                            <i style="display: none" class="fa-duotone fa-spinner-third fa-spin"></i>
                        </div>
                        <label for="selalu_cetak" class="small">Selalu Cetak Struk Setelah Transaksi Berhasil?</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-success fw-semibold" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js') }}/script-penjualan.js"></script>
@endsection
