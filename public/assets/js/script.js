function clearFilterTanggal(el) {
    el.previousElementSibling.value = "";
    document.getElementById('form_filter').submit();
}

const inputFormatedNumber = document.querySelectorAll(
    ".input-number-to-rupiah"
);
inputFormatedNumber.forEach((element) => {
    element.addEventListener("input", () => {
        const formattedValue = formatRupiah(element.value);
        element.value = formattedValue;
        element.previousElementSibling.value = formattedValue.replace(
            /[^0-9]/g,
            ""
        );
    });
});

const inputNumber = document.querySelectorAll(".input-number");
inputNumber.forEach((element) => {
    element.addEventListener("input", () => {
        const formattedValue = numberFormat(element.value);
        element.value = formattedValue;
        element.previousElementSibling.value = formattedValue.replace(
            /[^0-9]/g,
            ""
        );
    });
});

function numberFormat(number) {
    if (!number) number = 0;
    number += "";
    const rawValue = number.replace(/[^0-9]/g, "");
    const formattedValue = new Intl.NumberFormat("id-ID", {
        currency: "IDR",
    }).format(rawValue);
    return formattedValue.split(",")[0];
}

function formatRupiah(number) {
    if (!number) number = 0;
    number += "";
    const rawValue = number.replace(/[^0-9]/g, "");
    const formattedValue = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(rawValue);
    return formattedValue.split(",")[0];
}

let liveSearch = $("#modaltambah .livesearch");
liveSearch.select2({
    width: "100%",
    dropdownCssClass: "fs-14px",
    theme: "bootstrap4",
    language: id(),
    dropdownParent: $("#modaltambah"),
    allowClear: true,
    ajax: {
        dataType: "json",
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.nama,
                        id: item.id,
                    };
                }),
            };
        },
        cache: true,
    },
});
// liveSearch.val(null).trigger('change');
let liveSearchedit = $("#modaledit .livesearch");
liveSearchedit.select2({
    width: "100%",
    dropdownCssClass: "fs-14px",
    theme: "bootstrap4",
    language: id(),
    dropdownParent: $("#modaledit"),
    allowClear: true,
    ajax: {
        dataType: "json",
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.nama,
                        id: item.id,
                    };
                }),
            };
        },
        cache: true,
    },
});

const checkboxEceran = document.getElementById("with_eceran");
if (checkboxEceran) {
    checkboxEceran.addEventListener("change", () => {
        if (checkboxEceran.checked) {
            document
                .getElementById("fieldst-eceran")
                .removeAttribute("disabled");
        } else {
            document
                .getElementById("fieldst-eceran")
                .setAttribute("disabled", "");
        }
    });
}

const inputSatuanGroisr = document.getElementById("satuan_grosir");
if (inputSatuanGroisr) {
    inputSatuanGroisr.addEventListener("input", () => {
        document.getElementById(
            "isi_satuan_grosir"
        ).innerHTML = `1 ${inputSatuanGroisr.value} = `;
    });
}

const inputIsiBarang = document.getElementById("isi_barang");
if (inputIsiBarang) {
    inputIsiBarang.addEventListener("input", () => {
        hitungHargaEcer();
    });
}

const inputHargaBeliGrosir = document.getElementById("harga_beli_grosir");
if (inputHargaBeliGrosir) {
    inputHargaBeliGrosir.addEventListener("input", () => {
        hitungHargaEcer();
    });
}

function hitungHargaEcer() {
    const hargaBeliGrosir = inputHargaBeliGrosir.previousElementSibling.value;
    const jumlahIsi = inputIsiBarang.previousElementSibling.value;
    let hargaEcer = hargaBeliGrosir / jumlahIsi;
    hargaEcer = Math.round(hargaEcer);
    document.getElementById("harga_beli_ecer").previousElementSibling.value =
        hargaEcer;
    document.getElementById("harga_beli_ecer").value = formatRupiah(hargaEcer);
}

const checkboxEceranEdit = document.getElementById("with_eceran-edit");
if (checkboxEceranEdit) {
    checkboxEceranEdit.addEventListener("change", () => {
        if (checkboxEceranEdit.checked) {
            document
                .getElementById("fieldst-eceran-edit")
                .removeAttribute("disabled");
        } else {
            document
                .getElementById("fieldst-eceran-edit")
                .setAttribute("disabled", "");
        }
    });
}

const inputSatuanGroisrEdit = document.getElementById("satuan_grosir-edit");
if (inputSatuanGroisrEdit) {
    inputSatuanGroisrEdit.addEventListener("input", () => {
        document.getElementById(
            "isi_satuan_grosir-edit"
        ).innerHTML = `1 ${inputSatuanGroisrEdit.value} = `;
    });
}

const inputIsiBarangEdit = document.getElementById("isi_barang-edit");
if (inputIsiBarangEdit) {
    inputIsiBarangEdit.addEventListener("input", () => {
        hitungHargaEcerEdit();
    });
}

const inputHargaBeliGrosirEdit = document.getElementById(
    "harga_beli_grosir-edit"
);
if (inputHargaBeliGrosirEdit) {
    inputHargaBeliGrosir.addEventListener("input", () => {
        hitungHargaEcerEdit();
    });
}

function hitungHargaEcerEdit() {
    const hargabeli = inputHargaBeliGrosirEdit.previousElementSibling.value;
    const jumlahIsi = inputIsiBarangEdit.previousElementSibling.value;
    let hargaEcer = hargabeli / jumlahIsi;
    console.log(hargabeli);
    console.log(jumlahIsi);
    console.log(hargaEcer);
    hargaEcer = Math.round(hargaEcer);
    document.getElementById(
        "harga_beli_ecer-edit"
    ).previousElementSibling.value = hargaEcer;
    document.getElementById("harga_beli_ecer-edit").value =
        formatRupiah(hargaEcer);
}

function toggleSidebar() {
    const main = document.querySelector(".main");
    if (main) {   
        main.classList.toggle("active");
        sessionStorage.setItem("sidebar_state", main.classList.contains("active"));
    }
}

window.addEventListener("load", () => {
    if (sessionStorage.getItem("sidebar_state") == "false") {
        toggleSidebar();
    }
});

function expandMenu(element) {
    let arrowParent = element.parentElement.parentElement;
    arrowParent.classList.toggle("showMenu");
}

let url;
let loadcount = 0;
let loading = false;
let disabledLoading = false;
function setupInfinityScroll(urlto) {
    url = urlto;
    loadData();
    const main2 = document.querySelector("main");
    main2.addEventListener("scroll", () => {
        if (
            main2.scrollTop >= main2.scrollHeight - main2.clientHeight &&
            !loading
        ) {
            loadData();
        }
    });
}

function loadData() {
    loadcount = loadcount + 1;
    loading = true;
    console.log(loadcount);
    console.log(loading);

    const tbody = document.querySelector("tbody");
    const loadingElement = document.createElement("tr");
    if (!disabledLoading) {   
        loadingElement.innerHTML = `
        <td colspan="8" class="text-center py-2 loading">
            Memuat Data <i class="fa-regular fa-spinner-third fa-spin"></i>
        </td>`;
        tbody.append(loadingElement);
    }


    const xhr = new XMLHttpRequest();
    xhr.open("GET", url + "?showing=all&page=" + loadcount);
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let datas = JSON.parse(xhr.responseText);
                let nourut = datas.from;
                datas.data.forEach((data) => {
                    tbody.append(rowData(data, nourut++));
                });

                if (datas.current_page >= datas.last_page) {
                    disabledLoading = true;
                } else {
                    disabledLoading = false;
                }

                console.log(datas);

                tbody.querySelectorAll(".loading").forEach((el) => {
                    el.remove();
                });
                loading = false;
            } else {
                console.error("Error:", xhr.statusText);
            }
        }
    };

    xhr.send();
}
