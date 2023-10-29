let fetchUrl = "";
if (document.querySelector(".urlDataTable")) {
    fetchUrl = document.querySelector(".urlDataTable").innerHTML;
}

function loadData(event, element) {
    event.preventDefault();
    fetchingDataTable(element.href);
}

function fetchingDataTable(url) {
    const tableData = document.getElementById("table_data");

    document.getElementById("table-loader").classList.remove("hide");

    fetch(url, {
        method: "get",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((datas) => {
            tableData.innerHTML = "";
            console.log(datas);
            let nourut = datas.data.from;
            datas.data.data.forEach((data) => {
                const dateformat = new Intl.DateTimeFormat("id", {
                    day: "2-digit",
                    month: "long",
                    year: "numeric",
                }).format(new Date(data.created_at));

                const tr = document.createElement("tr");
                tr.addEventListener("click", () => {
                    getDetail(data.no_trans, "detail");
                });

                tr.style.userSelect = "none";
                tr.style.cursor = "pointer";
                tr.innerHTML = `
                    <td class="fit">${nourut++}</td>
                    <td class="fit">${data.no_trans}</td>
                    <td class="fit text-center">${dateformat}</td>
                    <td class="fit text-end">${formatRupiah(
                        data.total_bayar - data.total_potongan
                    )}</td>
                `;

                tableData.append(tr);
            });

            document.getElementById("table-loader").classList.add("hide");
            document.querySelector(".pagination").innerHTML = datas.pagination;
        })
        .catch((err) => {
            console.log("Error : " + err);
        });
}

fetchingDataTable(fetchUrl);

function getDetail(nomorTransaksi, aksi) {
    const detailContainer = document.querySelector('.detail-penjualan-container');
    const btnCetakUlang = document.getElementById('btn_cetak_ulang');
    btnCetakUlang.setAttribute('disabled', true);

    detailContainer.querySelector('#scroller').classList.remove('overflow-y-auto');
    detailContainer.querySelector('#scroller').classList.add('overflow-y-hidden');
    detailContainer.querySelector('#scroller').scrollTo({top: 0});

    detailContainer.classList.remove('info');
    detailContainer.classList.remove('show');
    detailContainer.classList.add('loading');
    nomorTransaksi = nomorTransaksi.replaceAll("/", "-");
    fetch(fetchUrl + "/" + nomorTransaksi, {
        method: "get",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            
            console.log(data);

            const dateformat = new Intl.DateTimeFormat("id", {
                day: "2-digit",
                month: "long",
                year: "numeric",
                second: "2-digit",
                minute: "2-digit",
                hour: "2-digit",
            }).format(new Date(data.created_at)).replaceAll('.', ':').replace('pukul', ' ');

            const mainDataTabel = document.querySelector('.detail-penjualan-wrapper');
            mainDataTabel.querySelector('.nomor-transaksi').innerHTML = data.no_trans;
            mainDataTabel.querySelector('.tanggal').innerHTML = dateformat;
            mainDataTabel.querySelector('.nama-kasir').innerHTML = data.user.nama;

            mainDataTabel.querySelector('.text-total-bayar').innerHTML = formatRupiah(data.total_bayar);
            mainDataTabel.querySelector('.text-total-potongan').innerHTML = formatRupiah(data.total_potongan);
            mainDataTabel.querySelector('.text-total').innerHTML = formatRupiah(data.total_bayar - data.total_potongan);
            mainDataTabel.querySelector('.text-bayar').innerHTML = formatRupiah(data.bayar);
            mainDataTabel.querySelector('.text-kembali').innerHTML = formatRupiah(data.kembalian);


            const tableDetail = document.getElementById('tdetaildata');
            tableDetail.innerHTML = "";
            data.detail_penjualan.forEach((item) => {
                const tbody = document.createElement('tbody');
                tbody.innerHTML = `
                    <tr class="border-white">
                        <td class="pt-3 pb-0">
                            <div>${item.barang.nama}</div>
                            <div>
                                <input type="hidden" name="id_barang[]" value="${item.id}">
                                <input type="hidden" name="harga_beli[]" value="${item.harga_beli}">
                                <input type="hidden" name="harga_jual[]" value="${item.harga_jual}">
                                <input type="hidden" name="satuan[]" value="${item.satuan_beli}">
                            </div>
                            <div class="detail-barang-keranjang d-none">${JSON.stringify(item)}</div><div>
                                <strong class="fs-16px">${formatRupiah(item.harga_jual)}</strong><span>/${item.satuan_beli}</span>
                            </div>
                            <div><strong class="text-success">Potongan</strong></div>
                        </td>

                        <td class="fit align-middle pt-3 pb-0 ${aksi == "edit" ? '' : 'd-none'}">
                            <div class="d-flex gap-2 mb-2">
                                <button style="min-width: 32px; height: 32px;" type="button" class="btn btn-sm btn-success btn-kurang-jumlah-beli">-</button>
                                <input type="hidden" name="jumlah_beli[]" value="${item.jumlah_beli}">
                                <input style="height: 32px;" type="text" class="form-control form-control-sm text-end input-number input-jumlah-beli" value="${numberFormat(item.jumlah_beli)}">
                                <button style="min-width: 32px; height: 32px;" type="button" class="btn btn-sm btn-success btn-tambah-jumlah-beli">+</button>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="hidden" name="potongan[]" value="${item.potongan}">
                                <input style="width: 130px; height: 32px;" type="text" class="form-control form-control-sm input-number text-end input-potongan" value="${numberFormat(item.potongan)}">
                            </div>
                        </td>

                        <td class="fit pt-3 pb-0 ${aksi == "edit" ? 'd-none' : ''}">
                            <div class="d-flex gap-2 mb-2">
                                <p>x <span class="fw-semibold fs-6">${numberFormat(item.jumlah_beli)}</span></p>
                            </div>
                        </td>

                        <td class="fit pt-3 pb-0">${item.satuan_beli}</td>
                        <td class="fit pt-3 pb-0">
                            <div class="d-flex flex-column align-items-end">
                                <div class="fw-bold mb-2 text-subtotal" style="height: 32px">
                                    ${formatRupiah(item.subtotal + item.potongan)}
                                </div>
                                <input type="hidden" class="input-subtotal" value="${item.subtotal + item.potongan}">
                                <div class="fw-bold text-secondary d-flex align-items-center teks-potongan" style="height: 32px">- ${formatRupiah(item.potongan)}</div>
                            </div>
                        </td>
                        <td class="fit align-middle text-center pb-0 ${aksi == "edit" ? '' : 'd-none'}"><button style="width: 32px; height: 32px;" type="button" class="btn btn-sm btn-danger btn-hapus"><i class="fa-regular fa-trash-can"></i></button></td>
                    </tr>
                    <tr class="border-0">
                        <td colspan="6" class="text-end">
                            <strong class="d-flex gap-2 justify-content-end ${aksi == "edit" ? '' : 'pe-2'}" style="padding-right: 64px">
                                Subtotal
                                <span class="text-success text-netsubtotal">
                                    ${formatRupiah(item.subtotal)}
                                </span>
                                <input type="hidden" class="input-netsubtotal" value="${item.subtotal}">
                            </strong>
                        </td>
                    </tr>
                `;

                tbody.querySelector(".btn-tambah-jumlah-beli").addEventListener("click", () => {
                        tambahJumlah(tbody.querySelector(".btn-tambah-jumlah-beli"));
                        hitungPotongan(tbody);
                        hitungSubtotal(tbody);
                    });

                // menambahkan aksi penghitungan subtotal sesuai perubahan input
                const inputJumlahBeli = tbody.querySelector(".input-jumlah-beli");
                inputJumlahBeli.addEventListener("input", () => {
                    const input = tbody.querySelector(".input-jumlah-beli");
                    const jumlah_beli = input.value.replace(/[^0-9]/g, "") > 1 ? input.value : 1;

                    input.value = numberFormat(jumlah_beli);
                    input.previousElementSibling.value = jumlah_beli;

                    hitungPotongan(tbody);
                    hitungSubtotal(tbody);
                });

                // menambahkan aksi mengurangi jumlah beli
                tbody.querySelector(".btn-kurang-jumlah-beli").addEventListener("click", () => {
                        kurangJumlah(tbody.querySelector(".btn-kurang-jumlah-beli"));
                        hitungPotongan(tbody);
                        hitungSubtotal(tbody);
                    });

                // menambahkan aksi enghitungan subtotal sesuai perubahan input potongan
                const inputPotongan = tbody.querySelector(".input-potongan");
                inputPotongan.addEventListener("input", () => {
                    hitungPotongan(tbody);
                    hitungSubtotal(tbody);
                });

                // menambahkan aksi menghapus item keranjang
                tbody.querySelector(".btn-hapus").addEventListener("click", () => {
                    tbody.remove();
                    hitungSubtotal(tbody);
                    if (document.querySelectorAll(".table-keranjang tbody").length === 0) {
                        document
                            .querySelector(".btn-pembayaran")
                            .setAttribute("disabled", true);
                    }
                });

                tableDetail.append(tbody);
                hitungSubtotal(tbody);
                hitungTotal();
            })

            btnCetakUlang.onclick = () => {
                cetakStruk(nomorTransaksi);
            }

            btnCetakUlang.removeAttribute('disabled');

            detailContainer.classList.add('show');
            detailContainer.classList.remove('loading');
            detailContainer.querySelector('#scroller').classList.remove('overflow-y-hidden')
            detailContainer.querySelector('#scroller').classList.add('overflow-y-auto')
        })
        .catch((err) =>{
            console.log(err);
        });
}

function kurangJumlah(element) {
    const input = element.nextElementSibling;
    const inputDisplayed = element.nextElementSibling.nextElementSibling;
    if (parseInt(input.value) > 1) {
        const value = parseInt(input.value) - 1;
        input.value = value;
        inputDisplayed.value = numberFormat(value);
    }
}

function tambahJumlah(element) {
    const input = element.previousElementSibling.previousElementSibling;
    const inputDisplayed = element.previousElementSibling;
    const value = parseInt(input.value) + 1;
    input.value = value;
    inputDisplayed.value = numberFormat(value);
}

function hitungPotongan(tbody) {
    const dataBarang = JSON.parse(tbody.querySelector(".detail-barang-keranjang").innerHTML);
    const inputJumlahBeli = tbody.querySelector(".input-jumlah-beli");
    const inputPotongan = tbody.querySelector(".input-potongan");

    let hargaJual = dataBarang.harga_jual
    let subtotal = inputJumlahBeli.previousElementSibling.value * hargaJual;
    let maxPotongan =
        inputPotongan.value.replace(/[^0-9]/g, "") >= subtotal
            ? subtotal
            : inputPotongan.value;

    inputPotongan.value = numberFormat(maxPotongan);
    inputPotongan.previousElementSibling.value = inputPotongan.value.replace(
        /[^0-9]/g,
        ""
    );
    tbody.querySelector(".teks-potongan").innerHTML =
        "- " + formatRupiah(inputPotongan.value);
}

function hitungSubtotal(tableRowElement) {
    const detailBarang = JSON.parse(tableRowElement.querySelector(".detail-barang-keranjang").innerHTML);

    const potongan = tableRowElement.querySelector(".input-potongan").previousElementSibling.value;
    const jumlah_beli = tableRowElement.querySelector(".input-jumlah-beli").previousElementSibling.value;

    let hargaJual = detailBarang.harga_jual;

    let subtotal = hargaJual * jumlah_beli;
    let netsubtotal = subtotal - potongan;

    tableRowElement.querySelector(".text-subtotal").innerHTML = formatRupiah(subtotal);
    tableRowElement.querySelector(".text-netsubtotal").innerHTML = formatRupiah(netsubtotal);
    tableRowElement.querySelector(".input-subtotal").value = subtotal;
    tableRowElement.querySelector(".input-netsubtotal").value = netsubtotal;
    hitungTotal();
}

function hitungTotal() {
    const elInputPotongan = document.querySelectorAll(".tabel-histori-detail-penjualan .input-potongan");
    const elInputTotalSubtotal = document.querySelectorAll(".tabel-histori-detail-penjualan .input-subtotal");

    const elTextTotalBayar = document.querySelector(".text-total-bayar");
    const elTextTotalPotongan = document.querySelector(".text-total-potongan");
    const elTextTotal = document.querySelector(".text-total");

    let totalpotongan = 0;
    elInputPotongan.forEach((el) => {
        totalpotongan += parseInt(el.previousElementSibling.value);
    });

    let total = 0;
    elInputTotalSubtotal.forEach((el) => {
        total += parseInt(el.value);
    });

    elTextTotalBayar.innerHTML = formatRupiah(total);
    elTextTotalPotongan.innerHTML = formatRupiah(totalpotongan);
    elTextTotal.innerHTML = formatRupiah(total - totalpotongan);
}

function cetakStruk(notrans) {
    const btnCetakUlang = document.getElementById('btn_cetak_ulang');
    btnCetakUlang.setAttribute("disabled", true);
    btnCetakUlang.classList.add("loading");

    const url = baseurl + "/kasir/" + notrans;
    const popup = window.open(url, "_blank", "popup");
    popup.print();

    popup.onclose = () => {
        resetBtn();
    };
    popup.onafterprint = () => {
        resetBtn();
        popup.close();
    };

    let loop;
    loop = setInterval(() => {
        if (popup.closed) {
            resetBtn();
            clearInterval(loop);
        }
    }, 2000);

    function resetBtn() {
        btnCetakUlang.classList.remove("loading");
        btnCetakUlang.removeAttribute("disabled");
    }
}


