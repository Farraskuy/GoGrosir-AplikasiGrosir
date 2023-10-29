const searchBarang = document.querySelector(".search-barang");
const dropdownSearchBarang = document.querySelector(".dropdown-search-barang");
let timeoutSearcBarang;
let loadingSearchBarang = true;
searchBarang.addEventListener("input", () => {
    if (searchBarang.value) {
        dropdownSearchBarang.classList.remove("hide");
    } else {
        dropdownSearchBarang.classList.add("hide");
    }
    const tbody = dropdownSearchBarang.querySelector(".tbody-dropdown-search");

    loadingSearchBarang = true;
    tbody.innerHTML = `
        <td colspan="3" class="text-center py-2 loading">
            Memuat Data <i class="fa-regular fa-spinner-third fa-spin"></i>
        </td>`;

    clearTimeout(timeoutSearcBarang);
    timeoutSearcBarang = setTimeout(() => {
        const createItem = function (
            dataBarang,
            typeBarang,
            isSelected = false
        ) {
            const tableRow = document.createElement("tr");
            tableRow.style.cursor = "pointer";
            tableRow.classList.add("search-item");
            if (isSelected) {
                tableRow.classList.add("active");
            }
            tableRow.addEventListener("click", () => {
                addItem(tableRow);
                dropdownSearchBarang.classList.add("hide");
                searchBarang.value = "";
            });
            

            let hargaJual = (typeBarang != "ecer") ? dataBarang.harga_jual_grosir : dataBarang.harga_jual_ecer;

            tableRow.innerHTML = `
                <td class="row-barang  position-relative ps-3" style="cursor: pointer">
                    <div class="wrap-text" style="width: calc(100% - 70px);">${dataBarang.nama}</div>
                    <span class="detail-barang d-none" data-type-barang="${typeBarang}">${JSON.stringify(dataBarang)}</span>
                    <span class="badge text-bg-${typeBarang == "grosir" ? "primary" : "secondary"} position-absolute fs-12px" style="top: 50%; right: 10px; transform: translateY(-50%)">
                        ${typeBarang.charAt(0).toUpperCase() +typeBarang.slice(1)}
                    </span>
                </td>
                <td class="fit text-end">${formatRupiah(hargaJual)}</td>
            `;

            return tableRow;
        };

        fetch(currenturl + "/searchbarang?search=" + searchBarang.value, {
            method: "get",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((result) => result.json())
            .then((datas) => {
                tbody.innerHTML = "";

                if (datas.barang.length == 1 && !isNaN(searchBarang.value)) {
                    if (datas.barang[0].with_eceran != "on") {
                        const barang = datas.barang[0];
                        addItem(createItem(barang, "grosir"));
                        dropdownSearchBarang.classList.add("hide");
                        searchBarang.value = "";
                        return true;
                    }
                }

                datas.barang.forEach((data, index) => {
                    tbody.append(createItem(data, "grosir", index === 0));
                    if (data.with_eceran == "on") {
                        tbody.append(createItem(data, "ecer"));
                    }
                });

                if (datas.barang.length == 0) {
                    tbody.innerHTML = `
                        <td colspan="3" class="text-center py-2 loading">
                            Barang dengan nama atau barcode <strong>"${searchBarang.value}"</strong>, Tidak ditemukan.
                        </td>
                    `;
                }
                loadingSearchBarang = false;
            })
            .catch((err) => {
                console.log("Error : " + err);
            });
    }, 500);
});

let selectedIndex = 0;
searchBarang.addEventListener("keydown", (event) => {
    searchBarang.setSelectionRange(searchBarang.value.length, searchBarang.value.length)
    const tr = dropdownSearchBarang.querySelectorAll("tbody tr");
    function resetSelection() {
        tr.forEach((el) => {
            el.classList.remove("active");
        });
    }
    if (!loadingSearchBarang && event.code == "ArrowUp") {
        resetSelection();
        selectedIndex = selectedIndex == 0 ? tr.length - 1 : selectedIndex - 1;
        tr[selectedIndex].classList.add("active");
    }
    if (!loadingSearchBarang && event.code == "ArrowDown") {
        resetSelection();
        selectedIndex = selectedIndex == tr.length - 1 ? 0 : selectedIndex + 1;
        tr[selectedIndex].classList.add("active");
    }

    if (!loadingSearchBarang && event.code == "Enter") {
        addItem(document.querySelector("tbody tr.active"));
        dropdownSearchBarang.classList.add("hide");
        searchBarang.value = "";
        selectedIndex = 0;
        resetSelection();
    }
});

let setIntervalTanggal;
function setupHeaderKasir() {
    const noTransWrapper = document.querySelector(".nomor-transaksi");
    const tanggalWrapper = document.querySelector(".tanggal");

    noTransWrapper.innerHTML = '<span class="placeholder rounded-5" style="width: 200px"></span>';
    tanggalWrapper.innerHTML = '<span class="placeholder rounded-5" style="width: 200px"></span>';
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
setupHeaderKasir();

const modalbarang = document.getElementById("modalbarang");
modalbarang.addEventListener("show.bs.modal", () => {
    tampilDataModalBarang(currenturl + "/getdatabarang");
});

const selectJumlahData = modalbarang.querySelector(".jumlah-tampil");
selectJumlahData.addEventListener("change", () => {
    tampilDataModalBarang(
        currenturl + "/getdatabarang?jumlahdata=" + selectJumlahData.value
    );
});

const modalBarangSearch = modalbarang.querySelector(".search");
let timeoutSearcModalBarang;
modalBarangSearch.addEventListener("input", () => {
    clearInterval(timeoutSearcModalBarang);
    timeoutSearcModalBarang = setTimeout(() => {
        tampilDataModalBarang(
            currenturl +
                "/getdatabarang?search=" +
                modalBarangSearch.value +
                "&jumlahdata=" +
                selectJumlahData.value
        );
    }, 500);
});

function loadDataBarang(event, element) {
    event.preventDefault();

    const search = "search=" + modalBarangSearch.value;
    const jumlahData = "jumlahdata=" + selectJumlahData.value;
    const operator = element.href.includes("?") ? "&" : "?";
    tampilDataModalBarang(
        element.href +
            (search ? operator + search : "") +
            (jumlahData ? operator + jumlahData : "")
    );
}

function tampilDataModalBarang(url) {
    const tableBody = modalbarang.querySelector("#tbody-barang-modal");
    const tableBodyTerpilih = modalbarang.querySelector(
        "#tbody-barang-modal-terpilih"
    );
    const tabelKeranjang = document.querySelectorAll(
        ".table-keranjang tr:not(.intro)"
    );

    let idItemKeranjang = [];
    tabelKeranjang.forEach((element) => {
        idItemKeranjang.push(element.getAttribute("data-id-item-keranjang"));
    });

    let params;
    if (idItemKeranjang.length != 0) {
        params = new URLSearchParams({ selected: idItemKeranjang }).toString();
    }

    modalbarang.querySelector(".modal-body").classList.add("overflow-y-hidden");
    modalbarang.querySelector(".modal-body").scrollTop = 0;
    modalbarang.querySelector(".loading").classList.remove("hide");

    tableBody.innerHTML = "";

    const operator = url.includes("?") ? "&" : "?";
    url = url + (params ? operator + params : "");

    fetch(url, {
        method: "get",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((datas) => {
            const createItem = function (
                dataBarang,
                typeBarang,
                isTerpilih = false
            ) {
                const tableRow = document.createElement("tr");

                let hargaJual = dataBarang.harga_jual_grosir;
                let satuan = dataBarang.satuan_grosir;
                if (typeBarang == "ecer") {
                    hargaJual = dataBarang.harga_jual_ecer;
                    satuan = dataBarang.satuan_ecer;
                }

                tableRow.innerHTML = `
                    <td class="row-barang  position-relative ps-3" style="cursor: pointer">
                        <div class="wrap-text" style="width: calc(100% - 70px);">${
                            dataBarang.nama
                        }</div>
                        <span class="detail-barang d-none" data-type-barang="${typeBarang}">${JSON.stringify(
                    dataBarang
                )}</span>
                        <span class="badge text-bg-${
                            typeBarang == "grosir" ? "primary" : "secondary"
                        } position-absolute fs-12px" style="top: 50%; right: 10px; transform: translateY(-50%)">
                            ${
                                typeBarang.charAt(0).toUpperCase() +
                                typeBarang.slice(1)
                            }
                        </span>
                    </td>
                    <td class="fit text-end">${satuan}</td>
                    <td class="fit text-end">${formatRupiah(hargaJual)}</td>
                    <td class="text-center">
                        ${
                            isTerpilih
                                ? '<button class="btn btn-sm btn-danger fw-semibold px-2 py-1 btn-pilih fs-13px" state-btn="hapus">Hapus</button>'
                                : '<button class="btn btn-sm btn-success fw-semibold px-2 py-1 btn-pilih fs-13px" state-btn="pilih">Pilih</button>'
                        }
                    </td>
                `;

                const btnPilih = tableRow.querySelector(".btn-pilih");
                btnPilih.addEventListener("click", () => {
                    if (btnPilih.getAttribute("state-btn") == "pilih") {
                        btnPilih.setAttribute("state-btn", "hapus");
                        btnPilih.classList.remove("btn-success");
                        btnPilih.classList.add("btn-danger");
                        btnPilih.innerHTML = "Hapus";
                        addItem(tableRow);
                    } else {
                        btnPilih.setAttribute("state-btn", "pilih");
                        btnPilih.classList.add("btn-success");
                        btnPilih.classList.remove("btn-danger");
                        btnPilih.innerHTML = "Pilih";
                        removeItem(tableRow);
                    }
                });

                return tableRow;
            };
            tableBodyTerpilih.innerHTML = "";
            datas.barang_terpilih.forEach((barang) => {
                if (
                    document.querySelector(
                        `.table-keranjang tr[data-id-item-keranjang="${barang.id}"][data-type-item="grosir"]`
                    )
                ) {
                    tableBodyTerpilih.append(
                        createItem(barang, "grosir", true)
                    );
                } else {
                    if (
                        !modalBarangSearch.value ||
                        barang.nama
                            .toLowerCase()
                            .includes(modalBarangSearch.value)
                    ) {
                        tableBody.append(createItem(barang, "grosir", false));
                    }
                }

                if (barang.with_eceran == "on") {
                    if (
                        document.querySelector(
                            `.table-keranjang tr[data-id-item-keranjang="${barang.id}"][data-type-item="ecer"]`
                        )
                    ) {
                        tableBodyTerpilih.append(
                            createItem(barang, "ecer", true)
                        );
                    } else {
                        if (
                            !modalBarangSearch.value ||
                            barang.nama
                                .toLowerCase()
                                .includes(modalBarangSearch.value)
                        ) {
                            tableBody.append(createItem(barang, "ecer", false));
                        }
                    }
                }
                console.log(barang);
            });

            datas.barang.data.forEach((barang) => {
                tableBody.append(createItem(barang, "grosir"));

                if (barang.with_eceran == "on") {
                    tableBody.append(createItem(barang, "ecer"));
                }
            });

            modalbarang.querySelector(".pagination-wrapper").innerHTML =
                datas.pagination;

            modalbarang.querySelector(".loading").classList.add("hide");
            modalbarang
                .querySelector(".modal-body")
                .classList.remove("overflow-y-hidden");
        })
        .catch((err) => {
            console.log("Error : " + err);
        });
}

const tabelKeranjang = document.querySelector(".table-keranjang");
function removeItem(element) {
    const elementDetailBarang = element.querySelector(".detail-barang");
    const detailBarang = JSON.parse(elementDetailBarang.innerHTML);
    const typeBarang = elementDetailBarang.getAttribute("data-type-barang");

    const itemKeranjang = document.querySelector(
        `.table-keranjang tr[data-id-item-keranjang="${detailBarang.id}"][data-type-item="${typeBarang}"]`
    );
    if (itemKeranjang) {
        itemKeranjang.remove();
        hitungSubtotal(itemKeranjang);
    }
}

function addItem(element) {
    const intro = tabelKeranjang.querySelector(".intro");
    if (intro) {
        intro.remove();
    }

    console.log(element);

    const elementDetailBarang = element.querySelector(".detail-barang");
    const detailBarang = JSON.parse(elementDetailBarang.innerHTML);
    const typeBarang = elementDetailBarang.getAttribute("data-type-barang");

    console.log(detailBarang, typeBarang);
    
    let audio = new Audio(
        baseurl + "/assets/audio/Barcode-scanner-beep-sound.mp3"
    );
    audio.play();

    const itemKeranjang = document.querySelector(
        `.table-keranjang 
        tr[data-id-item-keranjang="${detailBarang.id}"][data-type-item="${typeBarang}"]`
    );
    if (itemKeranjang) {
        const inputJumlahBeli = itemKeranjang.querySelector('.input-qty');
        console.log("value sebelum = " + inputJumlahBeli.value);
        inputJumlahBeli.value = parseInt(inputJumlahBeli.value) + 1;
        console.log("value sesudah = " + inputJumlahBeli.value);
        
        console.log("value visible input sebelum = " + inputJumlahBeli.nextElementSibling.value);
        inputJumlahBeli.nextElementSibling.value = numberFormat(inputJumlahBeli.value);
        console.log("value visible input sesudah = " + inputJumlahBeli.nextElementSibling.value);

        hitungSubtotal(itemKeranjang);
        return ;
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
        <th class="fit">${tabelKeranjang.children.length + 1}</th>
        <td>
            <div>${detailBarang.nama}</div>
            <div>
                <input type="hidden" name="id_barang[]" value="${detailBarang.id}">
                <input type="hidden" name="harga_beli[]" value="${hargaBeli}">
                <input type="hidden" name="harga_jual[]" value="${hargaJual}">
                <input type="hidden" name="satuan[]" value="${satuan}">
            </div>
            <div class="detail-barang-keranjang d-none">${JSON.stringify(detailBarang)}</div>
        </td>
        <td class="fit">${formatRupiah(hargaJual)}</td>
        <td class="fit">${satuan}</td>
        <td class="fit">
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-success rounded fw-semibold btn-kurang-jumlah-beli"><i class="fa-solid fa-minus"></i></button>
                <input type="hidden" class="input-qty" name="jumlah_beli[]" value="1">
                <input type="text" class="form-control form-control-sm text-end input-jumlah-beli" style="width: 70px" value="1">
                <button class="btn btn-sm btn-success rounded fw-semibold btn-tambah-jumlah-beli"><i class="fa-solid fa-plus"></i></button>
            </div>
        </td>
        <td class="fit">
            <input type="hidden" class="input-subtotal" value="${hargaJual}">
            <p class="m-0 text-subtotal">${formatRupiah(hargaJual)}</p>
        </td>
        <td class="fit">
            <div class="d-flex gap-2">
                <input type="hidden" name="potongan[]" value="0">
                <input type="text" class="form-control form-control-sm text-end input-potongan" style="width: 100px" value="0">
            </div>
        </td>
        <td class="fit">
            <input type="hidden" class="input-netsubtotal" value="${hargaJual}">
            <p class="m-0 text-netsubtotal">${formatRupiah(hargaJual)}</p>
        </td>
        <td class="fit">
            <button class="btn btn-sm btn-danger fw-semibold btn-hapus"><i class="fa-regular fa-trash"></i></button>
        </td>
    `;

    const tr = document.createElement("tr");
    tr.setAttribute("data-id-item-keranjang", detailBarang.id);
    tr.setAttribute("data-type-item", typeBarang);
    tr.innerHTML = templateKeranjang;

    // menambahkan aksi menambah jumlah beli
    tr.querySelector(".btn-tambah-jumlah-beli").addEventListener("click", () => {
            tambahJumlah(tr.querySelector(".btn-tambah-jumlah-beli"));
            hitungPotongan(tr);
            hitungSubtotal(tr);
        }
    );

    // menambahkan aksi penghitungan subtotal sesuai perubahan input
    const inputJumlahBeli = tr.querySelector(".input-jumlah-beli");
    inputJumlahBeli.addEventListener("input", () => {
        const jumlah_beli = inputJumlahBeli.value.replace(/[^0-9]/g, "") > 1 ? inputJumlahBeli.value : 1;

        inputJumlahBeli.value = numberFormat(jumlah_beli);
        inputJumlahBeli.previousElementSibling.value = jumlah_beli;

        hitungPotongan(tr);
        hitungSubtotal(tr);
    });

    // menambahkan aksi mengurangi jumlah beli
    tr.querySelector(".btn-kurang-jumlah-beli").addEventListener("click", () => {
            kurangJumlah(tr.querySelector(".btn-kurang-jumlah-beli"));
            hitungPotongan(tr);
            hitungSubtotal(tr);
        }
    );

    // menambahkan aksi enghitungan subtotal sesuai perubahan input potongan
    const inputPotongan = tr.querySelector(".input-potongan");
    inputPotongan.addEventListener("input", () => {
        hitungPotongan(tr, hargaJual);
        hitungSubtotal(tr);
    });

    // menambahkan aksi menghapus item keranjang
    tr.querySelector(".btn-hapus").addEventListener("click", () => {
        tr.remove();
        document.querySelectorAll('.table-keranjang tr').forEach((elNourut, index) => { 
            elNourut.firstElementChild.innerHTML = index + 1;
        });
        hitungSubtotal(tr);
    });

    // menambahkan element tr ke tabel keranjang
    tabelKeranjang.append(tr);

    hitungSubtotal(tr);
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

function hitungPotongan(tr) {
    const detailBarang = JSON.parse(
        tr.querySelector(".detail-barang-keranjang").innerHTML
    );
    const inputJumlahBeli = tr.querySelector(".input-jumlah-beli");
    const inputPotongan = tr.querySelector(".input-potongan");

    let hargaJual =
        tr.getAttribute("data-type-item") == "ecer"
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
}

function hitungSubtotal(tr) {
    const typeBarang = tr.getAttribute("data-type-item");
    const detailBarang = JSON.parse(
        tr.querySelector(".detail-barang-keranjang").innerHTML
    );

    const potongan =
        tr.querySelector(".input-potongan").previousElementSibling.value;
    const jumlah_beli =
        tr.querySelector(".input-jumlah-beli").previousElementSibling.value;

    let hargaJual =
        typeBarang === "ecer"
            ? detailBarang.harga_jual_ecer
            : detailBarang.harga_jual_grosir;

    let subtotal = hargaJual * jumlah_beli;
    let netsubtotal = subtotal - potongan;

    // jumlah total sebelum potongan
    tr.querySelector(".text-subtotal").innerHTML = formatRupiah(subtotal);
    tr.querySelector(".input-subtotal").value = subtotal;
    // jumlah total setelah potongan
    tr.querySelector(".text-netsubtotal").innerHTML = formatRupiah(netsubtotal);
    tr.querySelector(".input-netsubtotal").value = netsubtotal;
    hitungTotal();
}

function hitungTotal() {
    const elInputPotongan = document.querySelectorAll(
        ".table-keranjang .input-potongan"
    );
    const elInputTotalSubtotal = document.querySelectorAll(
        ".table-keranjang .input-subtotal"
    );

    const elInputTotalBayar = document.querySelector(".input-total-bayar");
    const elInputTotalPotongan = document.querySelector(
        ".input-total-potongan"
    );
    const elInputTotal = document.querySelector(".input-total");

    let totalpotongan = 0;
    elInputPotongan.forEach((el) => {
        totalpotongan += parseInt(el.previousElementSibling.value);
    });

    let total = 0;
    elInputTotalSubtotal.forEach((el) => {
        total += parseInt(el.value);
    });

    elInputTotalBayar.value = formatRupiah(total);
    elInputTotalPotongan.value = formatRupiah(totalpotongan);
    elInputTotal.value = formatRupiah(total - totalpotongan);

    document.getElementById("bayar").value = formatRupiah(
        total - totalpotongan
    );
    document.getElementById("bayar").previousElementSibling.value =
        total - totalpotongan;

    document.getElementById("kembali").value = formatRupiah(0);

    const keterangan = document.querySelector(".keterangan-pembayaran");
    keterangan.innerHTML = "Lunas";
    keterangan.classList.remove("text-bg-danger");
    keterangan.classList.add("text-bg-primary");

    if (
        document.querySelectorAll(".table-keranjang tr:not(.intro)").length ===
        0
    ) {
        document
            .querySelector(".btn-pembayaran")
            .setAttribute("disabled", true);
        tabelKeranjang.innerHTML = `
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
        `;
    }
    // console.log("Total :" + total);
    // console.log("Total Potongan :" + totalpotongan);
}

const keterangan = document.querySelector(".keterangan-pembayaran");
const inputBayar = document.getElementById("bayar");
inputBayar.addEventListener("click", () => {
    const end = inputBayar.value.length;
    inputBayar.setSelectionRange(end, end);
});

inputBayar.addEventListener("input", () => {
    inputBayar.value = formatRupiah(inputBayar.value);

    const btnBayar = document.querySelector(".btn-pembayaran");
    const totalBayar = document
        .querySelector(".input-total")
        .value.replace(/[^0-9]/g, "");
    const bayar = inputBayar.value.replace(/[^0-9]/g, "");
    const kembali = totalBayar - bayar;

    inputBayar.previousElementSibling.value = bayar;

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

const btnPrint = document.getElementById("btn_cetak_struk");
const modalCetakEl = document.getElementById("modal_cetak");
modalCetakEl.addEventListener("hide.bs.modal", () => {
    bersih();
    btnPrint.classList.remove("loading");
    btnPrint.removeAttribute("disabled");
});

const modalCetak = new bootstrap.Modal("#modal_cetak", { keyboard: false });
const formkeranjang = document.querySelector(".form-keranjang");
formkeranjang.addEventListener("submit", (event) => {
    event.preventDefault();
});

document
    .querySelector(".btn-simpan-transaksi")
    .addEventListener("click", () => {
        const btnBayar = document.querySelector(".btn-pembayaran");
        btnBayar.setAttribute("disabled", true);
        btnBayar.classList.add("load");

        const itemKeranjang = document.querySelectorAll(".table-keranjang tr");
        let data = [];
        itemKeranjang.forEach((element) => {
            const elinputs = element.querySelectorAll("input[name]");
            console.log(elinputs);
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
        data.bayar =
            document.getElementById("bayar").previousElementSibling.value;

        // console.log(data, JSON.stringify(data));

        fetch(currenturl, {
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

const selaluCetak = document.getElementById("selalu_cetak");
selaluCetak.addEventListener("change", () => {
    const isChecked = selaluCetak.checked;
    selaluCetak.style.display = "none";
    selaluCetak.nextElementSibling.style.display = "inline-block";
    console.log(isChecked);
    fetch(baseurl + "/app-setting/selalu-cetak", {
        method: "post",
        body: JSON.stringify({
            value: isChecked ? "true" : "false",
        }),
        headers: {
            "X-CSRF-TOKEN": csrf,
            "Content-type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((result) => result.json())
        .then((data) => {
            console.log(data);
            selaluCetak.checked =
                data.selalu_cetak_struk == "true" ? true : false;
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

function bersih() {
    tabelKeranjang.innerHTML = `
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
        `;
    hitungTotal();
    setupHeaderKasir();
}
