const appSettings = JSON.parse(document.querySelector('.appSettings').innerHTML);
const selaluCetak = document.getElementById('selalu_cetak');
window.addEventListener('load', () => {
    console.log(appSettings);
    selaluCetak.checked = appSettings.selalu_cetak_struk == "true" ? true : false;
});


const searchElement = document.getElementById("search");
document.addEventListener("keydown", (e) => {
    console.log(e);
    if (e.keyCode === 32 && e.ctrlKey) {
        searchElement.focus();
    }
});

const debounce = (func, delay = 500) => {
    let debounceTimer;
    return function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.call(), delay);
    };
};
let urlBarang = "";
if (document.querySelector(".urlDataTable")) {
    urlBarang = document.querySelector(".urlDataTable").innerHTML;
}

if (searchElement) {
    searchElement.addEventListener(
        "input",
        debounce(() =>
            fetchingDataBarang(urlBarang + "?search=" + searchElement.value)
        )
    );
}

function loadDataBarang(event, element) {
    event.preventDefault();
    fetchingDataBarang(element.href);
}

function fetchingDataBarang(url) {
    const tableBody = document.getElementById("tbody-barang");

    document.getElementById("table-loader").classList.remove("hide");

    fetch(url, {
        method: "get",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((datas) => {
            console.log(datas.data);
            tableBody.innerHTML = "";

            datas.data.data.forEach((row) => {
                const rowData = document.createElement("tr");
                rowData.style.cursor = "pointer";
                rowData.addEventListener("click", () => {
                    addItem(rowData);
                });

                rowData.innerHTML = `
                    <td class="row-barang  position-relative ps-3" style="cursor: pointer">
                        <div class="wrap-text" style="width: calc(100% - 70px);">${row.nama}</div>
                        <span class="detail-barang d-none" data-type-barang="grosir">${JSON.stringify(row)}</span>
                        <span class="badge text-bg-primary position-absolute fs-12px" style="top: 50%; right: 10px; transform: translateY(-50%)">Grosir</span>
                    </td>
                    <td class="fit text-end">${formatRupiah(row.harga_jual_grosir)}</td>
                `;
                tableBody.append(rowData);

                if (row.with_eceran == "on") {
                    const rowDataEcer = document.createElement("tr");
                    rowDataEcer.style.cursor = "pointer";
                    rowDataEcer.addEventListener("click", () => {
                        addItem(rowDataEcer);
                    });

                    rowDataEcer.innerHTML = `
                        <td class="row-barang position-relative ps-3" style="cursor: pointer">
                            <div class="wrap-text" style="width: calc(100% - 70px);">${row.nama}</div>
                            <span class="detail-barang d-none" data-type-barang="ecer">${JSON.stringify(row)}</span>
                            <span class="badge text-bg-secondary position-absolute fs-12px" style="top: 50%; right: 10px; transform: translateY(-50%)">Eceran</span>
                        </td>
                        <td class="fit text-end">${formatRupiah(row.harga_jual_ecer)}</td>
                    `;
                    tableBody.append(rowDataEcer);
                }

                if (datas.data.total === 1 && !isNaN(searchElement.value) && row.with_eceran != "on") {
                    addItem(rowData);
                    searchElement.value = "";
                    fetchingDataBarang(urlBarang);
                    return false;
                }
            });

            document.getElementById("table-loader").classList.add("hide");
            document.querySelector(".pagination").innerHTML = datas.pagination;
        })
        .catch((err) => {
            console.log("Error : " + err);
        });
}

let setIntervalTanggal;
function setupHeaderKasir() {
    const noTransWrapper = document.querySelector(".nomor-transaksi");
    const tanggalWrapper = document.querySelector(".tanggal");

    noTransWrapper.innerHTML =
        ' <span class="placeholder w-100 rounded-5"></span>';
    tanggalWrapper.innerHTML =
        ' <span class="placeholder w-100 rounded-5"></span>';
    fetch(baseurl + "/kasir/getnotrans", {
        method: "get",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((data) => {
            noTransWrapper.classList.remove("placeholder-wave");
            noTransWrapper.innerHTML = `<strong>${data}</strong>`;
            setTime();
        })
        .catch((err) => {
            console.error(err);
        });
    clearInterval(setIntervalTanggal);
    setIntervalTanggal = setInterval(() => {
        setTime();
    }, 1000);

    function setTime() {
        const date = new Date();
        const dateformat = new Intl.DateTimeFormat("id", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            day: "2-digit",
            month: "long",
            year: "numeric",
        })
            .format(date)
            .split("pukul");
        tanggalWrapper.classList.remove("placeholder-wave");
        tanggalWrapper.innerHTML = `${
            dateformat[0]
        }, <strong>${dateformat[1].replaceAll(".", ":")}</strong>`;
    }
}

if (document.querySelector(".typeDataTable")) {
    if (document.querySelector(".typeDataTable").innerHTML == "barang") {
        fetchingDataBarang(urlBarang);
        setupHeaderKasir();
    }
}

const tabelKeranjang = document.querySelector(".table-keranjang");
function addItem(element) {
    const elementDetailBarang = element.querySelector(".detail-barang");
    const detailBarang = JSON.parse(elementDetailBarang.innerHTML);
    const typeBarang = elementDetailBarang.getAttribute("data-type-barang");

    let audio = new Audio(
        baseurl + "/assets/audio/Barcode-scanner-beep-sound.mp3"
    );
    audio.play();

    const itemKeranjang = document.querySelector(".table-keranjang").querySelector(`tbody[data-id-item-keranjang="${detailBarang.id}"][data-type-item="${typeBarang}"]`);
    if (itemKeranjang) {
        const inputJumlahBeli = itemKeranjang.querySelector(
            'input[name="jumlah_beli[]"]'
        );
        inputJumlahBeli.value = parseInt(inputJumlahBeli.value) + 1;
        inputJumlahBeli.nextElementSibling.value = numberFormat(
            inputJumlahBeli.value
        );

        hitungSubtotal(itemKeranjang);

        return false;
    }

    let hargaJual = detailBarang.harga_jual_grosir;
    let hargaBeli = detailBarang.harga_beli_grosir;
    let satuan = detailBarang.satuan_grosir;
    if (typeBarang === "ecer") {
        hargaJual = detailBarang.harga_jual_ecer;
        hargaBeli = detailBarang.harga_beli_ecer;
        satuan = detailBarang.satuan_ecer;
    }

    const templateKeranjang = `
        <tr class="border-white">
            <td class="pt-3 pb-0">
                <div>${detailBarang.nama}</div>
                <div>
                    <input type="hidden" name="id_barang[]" value="${detailBarang.id}">
                    <input type="hidden" name="harga_beli[]" value="${hargaBeli}">
                    <input type="hidden" name="harga_jual[]" value="${hargaJual}">
                    <input type="hidden" name="satuan[]" value="${satuan}">
                </div>
                <div class="detail-barang-keranjang d-none">${JSON.stringify(detailBarang)}</div>
                <div>
                    <strong class="fs-16px">${formatRupiah(hargaJual)}</strong><span>/${satuan}</span>
                </div>
                <div><strong class="text-success">Potongan</strong></div>
            </td>
            <td class="fit align-middle pt-3 pb-0">
                <div class="d-flex gap-2 mb-2">
                    <button style="width: 32px; height: 32px;" type="button" class="btn btn-sm btn-success btn-kurang-jumlah-beli">-</button>
                    <input type="hidden" name="jumlah_beli[]" value="1">
                    <input style="width: 50px; height: 32px;" type="text" class="form-control form-control-sm text-end input-number input-jumlah-beli" value="1">
                    <button style="width: 32px; height: 32px;" type="button" class="btn btn-sm btn-success btn-tambah-jumlah-beli">+</button>
                </div>
                <div class="d-flex gap-2">
                    <input type="hidden" name="potongan[]" value="0">
                    <input style="width: 130px; height: 32px;" type="text" class="form-control form-control-sm input-number text-end input-potongan" value="0">
                </div>
            </td>
            <td class="fit pt-3 pb-0">${satuan}</td>
            <td class="fit pt-3 pb-0">
                <div class="d-flex flex-column align-items-end">
                    <div class="fw-bold mb-2 d-flex align-items-center text-subtotal" style="height: 32px">
                        ${formatRupiah(hargaJual)}
                    </div>
                    <input type="hidden" class="input-subtotal">
                    <div class="fw-bold text-secondary d-flex align-items-center teks-potongan" style="height: 32px">- Rp 0</div>
                </div>
            </td>
            <td class="fit align-middle text-center pb-0"><button style="width: 32px; height: 32px;" type="button" class="btn btn-sm btn-danger btn-hapus"><i class="fa-regular fa-trash-can"></i></button></td>
        </tr>
        <tr class="border-0">
            <td colspan="6" class="text-end">
                <strong class="d-flex gap-2 justify-content-end" style="padding-right: 64px">
                    Subtotal 
                    <span class="text-success text-netsubtotal">
                        ${formatRupiah(hargaJual)}
                    </span>
                    <input type="hidden" class="input-netsubtotal">
                </strong>
            </td>
        </tr>
    `;

    const tbody = document.createElement("tbody");
    tbody.setAttribute("data-id-item-keranjang", detailBarang.id);
    tbody.setAttribute("data-type-item", typeBarang);
    tbody.innerHTML = templateKeranjang;

    // menambahkan aksi menambah jumlah beli
    tbody
        .querySelector(".btn-tambah-jumlah-beli")
        .addEventListener("click", () => {
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
    tbody
        .querySelector(".btn-kurang-jumlah-beli")
        .addEventListener("click", () => {
            kurangJumlah(tbody.querySelector(".btn-kurang-jumlah-beli"));
            hitungPotongan(tbody);
            hitungSubtotal(tbody);
        });

    // menambahkan aksi enghitungan subtotal sesuai perubahan input potongan
    const inputPotongan = tbody.querySelector(".input-potongan");
    inputPotongan.addEventListener("input", () => {
        hitungPotongan(tbody, hargaJual);
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

    // menambahkan element tbody ke tabel keranjang
    tabelKeranjang.append(tbody);
    hitungSubtotal(tbody);
    hitungTotal();

    document.querySelector(".btn-pembayaran").removeAttribute("disabled");
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
    const detailBarang = JSON.parse(
        tbody.querySelector(".detail-barang-keranjang").innerHTML
    );
    const inputJumlahBeli = tbody.querySelector(".input-jumlah-beli");
    const inputPotongan = tbody.querySelector(".input-potongan");

    let hargaJual =
        tbody.getAttribute("data-type-item") == "ecer"
            ? detailBarang.harga_jual_ecer
            : detailBarang.harga_jual_grosir;
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
    const typeBarang = tableRowElement.getAttribute("data-type-item");
    const detailBarang = JSON.parse(
        tableRowElement.querySelector(".detail-barang-keranjang").innerHTML
    );

    const potongan =
        tableRowElement.querySelector(".input-potongan").previousElementSibling
            .value;
    const jumlah_beli =
        tableRowElement.querySelector(".input-jumlah-beli")
            .previousElementSibling.value;

    let hargaJual =
        typeBarang === "ecer"
            ? detailBarang.harga_jual_ecer
            : detailBarang.harga_jual_grosir;

    let subtotal = hargaJual * jumlah_beli;
    let netsubtotal = subtotal - potongan;

    tableRowElement.querySelector(".text-subtotal").innerHTML =
        formatRupiah(subtotal);
    tableRowElement.querySelector(".text-netsubtotal").innerHTML =
        formatRupiah(netsubtotal);
    tableRowElement.querySelector(".input-subtotal").value = subtotal;
    tableRowElement.querySelector(".input-netsubtotal").value = netsubtotal;
    hitungTotal();
}

function hitungTotal() {
    const elInputPotongan = document.querySelectorAll(
        ".table-keranjang .input-potongan"
    );
    const elInputTotalSubtotal = document.querySelectorAll(
        ".table-keranjang .input-subtotal"
    );

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

    document.getElementById("totalBayar").value = formatRupiah(
        total - totalpotongan
    );
    document.getElementById("bayar").value = formatRupiah(
        total - totalpotongan
    );
    document.getElementById("bayar").previousElementSibling.value =
        total - totalpotongan;

    document.getElementById("kembali").value = formatRupiah(0);

    console.log("Total :" + total);
    console.log("Total Potongan :" + totalpotongan);
}

const keterangan = document.querySelector(".keterangan-pembayaran");
const inputTotalBayar = document.getElementById("totalBayar");
const inputBayar = document.getElementById("bayar");
const btnBayar = document.getElementById("btn_bayar");
inputBayar.addEventListener("input", () => {
    inputBayar.value = formatRupiah(inputBayar.value); //set display input value

    const totalBayar = inputTotalBayar.value.replace(/[^0-9]/g, "");
    const bayar = inputBayar.value.replace(/[^0-9]/g, "");
    const kembali = totalBayar - bayar;

    inputBayar.previousElementSibling.value = bayar; //set form input value

    if (kembali <= 0) {
        if (!btnBayar.classList.contains("load")) {
            btnBayar.removeAttribute("disabled");
        }
        document.getElementById("kembali").value = formatRupiah(kembali);
        keterangan.innerHTML = "Lunas";
        keterangan.classList.remove("text-bg-danger");
        keterangan.classList.add("text-bg-primary");
    } else {
        btnBayar.setAttribute("disabled", true);
        keterangan.innerHTML = "Kurang " + formatRupiah(kembali);
        keterangan.classList.remove("text-bg-primary");
        keterangan.classList.add("text-bg-danger");
        document.getElementById("kembali").value = formatRupiah(0);
    }
});

const formKeranjang = document.getElementById("form_keranjang");
const modalPembayaran = new bootstrap.Modal("#modal_pembayaran");
const modalCetak = new bootstrap.Modal("#modal_cetak", { keyboard: false });

const btnPrint = document.getElementById("btn_cetak_struk");
const modalCetakEl = document.getElementById("modal_cetak");
modalCetakEl.addEventListener("hide.bs.modal", () => {
    clearKeranjang();
    btnPrint.classList.remove("loading");
    btnPrint.removeAttribute("disabled");
});

formKeranjang.addEventListener("submit", (formevent) => {
    formevent.preventDefault();

    btnBayar.setAttribute("disabled", true);
    btnBayar.classList.add("load");

    const itemKeranjang = document.querySelectorAll(".table-keranjang tbody");
    let data = [];
    itemKeranjang.forEach((element) => {
        const elinputs = element.querySelectorAll("input[name]");
        elinputs.forEach((input) => {
            const name = input.getAttribute("name").replace("[]", "");
            if (!data[name]) {
                data[name] = [input.value];
            } else {
                data[name].push(input.value);
            }
        });
    });

    data = Object.assign({}, data);
    data.bayar = document.getElementById("bayar").previousElementSibling.value;

    console.log(data, JSON.stringify(data));

    fetch(baseurl + "/penjualan-tambah", {
        method: "post",
        body: JSON.stringify(data),
        headers: {
            "X-CSRF-TOKEN": csrf,
            "Content-type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((data) => {
            btnBayar.removeAttribute("disabled");
            btnBayar.classList.remove("load");
            console.log(data);
            if (data.message_type != "success") {
                return false;
            }

            modalPembayaran.hide();

            modalCetakEl.addEventListener("show.bs.modal", () => {
                modalCetakEl
                    .querySelector("#btn_cetak_struk")
                    .setAttribute(
                        "data-id-transaksi",
                        data.no_trans.replaceAll("/", "-")
                    );
                modalCetakEl.querySelector("#kembalian").innerHTML =
                    formatRupiah(data.kembalian);
            });

            modalCetak.show();

            if (data.selalu_cetak == "true") {
                cetakStruk(data.no_trans.replaceAll("/", "-"));
            }
        })
        .catch((err) => {
            console.log(err);
        });
});


selaluCetak.addEventListener('change' , () => {
    const isChecked = selaluCetak.checked;
    selaluCetak.style.display = "none";
    selaluCetak.nextElementSibling.style.display = "inline-block";

    fetch(baseurl + "/app-setting/selalu-cetak", {
        method: "post",
        body: JSON.stringify({
            value: "" + isChecked,
        }),
        headers: {
            "X-CSRF-TOKEN": csrf,
            "Content-type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((data) => {
            selaluCetak.checked = data.selalu_cetak_struk == "true" ? true : false;
            selaluCetak.style.display = "inline-block";
            selaluCetak.nextElementSibling.style.display = "none";
        })
        .catch((err) => {
            console.log(err);
        });
});

function cetakStruk(id) {
    const url = baseurl + "/kasir/" + id;
    const popup = window.open(url, "_blank", "popup");
    popup.print();
    popup.onbeforeprint = () => {
        btnPrint.setAttribute("disabled", true);
        btnPrint.classList.add("loading");
    };
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
        btnPrint.classList.remove("loading");
        btnPrint.removeAttribute("disabled");
    }
}

const btnClearKeranjang = document.getElementById("btn_clear_keranjang");
btnClearKeranjang.addEventListener("click", () => {
    clearKeranjang();
});

function clearKeranjang() {
    tabelKeranjang.querySelectorAll("tbody").forEach((el) => {
        el.remove();
    });
    document.querySelector(".btn-pembayaran").setAttribute("disabled", true);
    setupHeaderKasir();
    hitungTotal();
}
