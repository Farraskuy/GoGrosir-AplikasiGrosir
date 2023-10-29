<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/bootstrap') }}/css/bootstrap.min.css">
    {{-- bootstrap js --}}
    <script src="{{ asset('assets/bootstrap') }}/js/bootstrap.bundle.min.js"></script>

    {{-- font awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/font-awesome') }}/css/fontawesome.css">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome') }}/css/solid.css">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome') }}/css/light.css">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome') }}/css/regular.css">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome') }}/css/duotone.css">

    {{-- fonts --}}
    <link rel="stylesheet" href="{{ asset('assets/css') }}/fonts.css">

    {{-- icon --}}
    <link rel="shortcut icon" href="{{ asset('assets/img') }}/logo.png" type="image/x-icon">

    {{-- select2 --}}
    <link href="{{ asset('assets/select2') }}/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/select2') }}/theme/select2-bootstrap4.min.css" rel="stylesheet">

    {{-- style --}}
    <link rel="stylesheet" href="{{ asset('assets/css') }}/style.css">

    <script>
        const baseurl = '{{ url('/') }}';
        const csrf = '{{ csrf_token() }}';
        const currenturl = '{{ url()->current() }}';
    </script>

    <title>@yield('title')</title>
</head>

<body class="bg-light d-flex overflow-hidden" style="font-family: Poppins, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
    <div class="main w-100 active">
        <aside class="sidebar bg-white py-2">
            <div class="logo">
                <a href="/" class="logo-details">
                    <div class="img">
                        <img src="/assets/img/logo.png" alt="Logo" width="35" height="35" class="d-inline-block align-text-top">
                    </div>
                    <span class="logo_name h6 m-0 fw-semibold">App Toko Zafira</span>
                </a>
            </div>
            <ul class="nav-links" style="padding-bottom: 115px;">
                <li>
                    <div class="nav-button {{ request()->is('/') ? 'active' : '' }}">
                        <a href="/">
                            <i class="fa-regular fa-house"></i>
                            <span class="link_name">Home</span>
                        </a>
                    </div>
                    <hr class="m-0 mx-3">
                    <ul class="sub-menu blank">
                        <li class="fw-semibold link_name">Home</li>
                    </ul>
                </li>
                @if (Auth::user()->hasPermission('dashboard'))
                    <li>
                        <div class="nav-button {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="fa-regular fa-grid-2"></i>
                                <span class="link_name">Dashboard</span>
                            </a>
                        </div>
                        <ul class="sub-menu blank">
                            <li class="fw-semibold link_name">Dashboard</li>
                        </ul>
                    </li>
                @endif
                @if (Auth::user()->hasPermission('kasir'))
                    <li>
                        <div class="nav-button {{ request()->is('/kasir') ? 'active' : '' }}">
                            <a href="/kasir">
                                <i class="fa-regular fa-cash-register"></i>
                                <span class="link_name">Kasir</span>
                            </a>
                        </div>
                        <ul class="sub-menu blank">
                            <li class="fw-semibold link_name">Kasir</li>
                        </ul>
                    </li>
                @endif
                @if (Auth::user()->hasPermission('lihat_barang') || Auth::user()->hasPermission('lihat_kategori') || Auth::user()->hasPermission('lihat_supplier'))
                    <li class="{{ request()->is('barang*') ? 'showMenu' : '' }}">
                        <div class="nav-button {{ request()->is('barang*') ? 'active' : '' }}">
                            <div class="iocn-link" onclick="expandMenu(this)">
                                <a>
                                    <i class="fa-light fa-box"></i>
                                    <span class="link_name">Data Barang</span>
                                </a>
                                <i class='fa-regular fa-angle-down arrow'></i>
                            </div>
                        </div>
                        <ul class="sub-menu">
                            @if (Auth::user()->hasPermission('lihat_kategori'))
                                <li><span class="link_name fw-semibold">Data Barang</span></li>
                                <li class="nav-button {{ request()->is('barang/kategori') ? 'active' : '' }}">
                                    <a class="d-flex gap-2 fw-semibold" href="{{ route('kategori.index') }}">
                                        <span class="fa-regular fa-hashtag"></span>
                                        Daftar Kategori
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->hasPermission('lihat_supplier'))
                                <li class="nav-button {{ request()->is('barang/supplier') ? 'active' : '' }}">
                                    <a class="d-flex gap-2 fw-semibold" href="{{ route('supplier.index') }}">
                                        <span class="fa-regular fa-user-tag"></span>
                                        Daftar Supplier
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->hasPermission('lihat_barang'))
                                <li class="nav-button {{ request()->is('barang') ? 'active' : '' }}">
                                    <a class="d-flex gap-2 fw-semibold" href="/barang">
                                        <span class="fa-regular fa-book"></span>
                                        Daftar Barang
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (Auth::user()->hasPermission('lihat_penjualan'))
                    <li class="{{ request()->is('penjualan') || request()->is('penjualan/tambah') ? 'showMenu' : '' }}">
                        <div class="nav-button {{ request()->is('penjualan') || request()->is('penjualan/tambah') ? 'active' : '' }}">
                            <div class="iocn-link" onclick="expandMenu(this)">
                                <a>
                                    <i class="fa-regular fa-cart-shopping"></i>
                                    <span class="link_name">Transaksi</span>
                                </a>
                                <i class='fa-regular fa-angle-down arrow'></i>
                            </div>
                        </div>
                        <ul class="sub-menu">
                            <li><span class="link_name fw-semibold">Transaksi</span></li>
                            <li class="nav-button {{ request()->is('penjualan') ? 'active' : '' }}">
                                <a class="d-flex gap-2 fw-semibold" href="/penjualan">
                                    <span class="fa-regular fa-cart-arrow-down"></span>
                                    Data Penjualan
                                </a>
                            </li>
                            @if (Auth::user()->hasPermission('tambah_penjualan'))
                                <li class="nav-button {{ request()->is('penjualan/tambah') ? 'active' : '' }}">
                                    <a class="d-flex gap-2 fw-semibold" href="/penjualan/tambah">
                                        <span class="fa-regular fa-cart-arrow-down"></span>
                                        Transaksi Penjualan
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (isSuperAdmin(Auth::user()->role_id))
                    <li class="{{ request()->is('pengguna') || request()->is('pengguna/role') ? 'showMenu' : '' }}">
                        <div class="nav-button {{ request()->is('pengguna') || request()->is('pengguna/role') ? 'active' : '' }}">
                            <div class="iocn-link" onclick="expandMenu(this)">
                                <a>
                                    <i class="fa-regular fa-user-gear"></i>
                                    <span class="link_name">Pengguna</span>
                                </a>
                                <i class='fa-regular fa-angle-down arrow'></i>
                            </div>
                        </div>
                        <ul class="sub-menu">
                            <li><span class="link_name fw-semibold">Pengguna</span></li>
                            <li class="nav-button {{ request()->is('pengguna/role') ? 'active' : '' }}">
                                <a class="d-flex gap-2 fw-semibold" href="{{ route('role') }}">
                                    <span class="fa-solid fa-shield-halved"></span>
                                    Data Role
                                </a>
                            </li>
                            <li class="nav-button {{ request()->is('pengguna') ? 'active' : '' }}">
                                <a class="d-flex gap-2 fw-semibold" href="{{ route('pengguna') }}">
                                    <span class="fa-regular fa-user"></span>
                                    Data Pengguna
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="position-absolute w-100 bg-white" style="bottom: 0">
                    <div class="bg-white me-2">
                        <div class="nav-button {{ request()->is('/pengaturan') ? 'active' : '' }}">
                            <a href="/pengaturan">
                                <i class="fa-regular fa-gear"></i>
                                <span class="link_name">Pengaturan</span>
                            </a>
                        </div>
                    </div>
                    <ul class="sub-menu blank">
                        <li class="fw-semibold link_name">Pengaturan</li>
                    </ul>
                </li>
            </ul>
            {{-- <div class="overflow-y-auto" style="height: calc(100% - 60px); padding-bottom: 50px"> --}}
            {{-- </div> --}}
        </aside>

        <main class="w-100 vh-100 overflow-y-scroll">
            <nav class="navbar sticky-top navbar-expand p-0">
                <div class="container-fluid bg-white d-flex justify-content-between py-2">
                    <button class="btn text-purple" type="button" onclick="toggleSidebar()"><i class="fa-solid fa-bars fa-lg"></i></button>
                    <div class="ps-2 border-start border-2 dropdown" style="cursor: pointer">
                        <a class="text-decoration-none" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="true">
                            <div style="height: 45px; width: 170px;" class="row g-0">
                                <div class="text-dark text-nowrap wrap-text col-9 d-flex flex-column justify-content-center">
                                    <small class="p-0 m-0 fw-semibold wrap-text fs-13px">{{ Auth::user()->username }}</small>
                                    <small class="p-0 m-0 wrap-text w-75 fs-12px">{{ Auth::user()->role->nama }}</small>
                                </div>
                                <div class="h-100 col-3 text-center">
                                    <img style="object-fit: cover;" class="rounded-circle" height="40" width="40" src="{{ asset('storage/upload/' . Auth::user()->foto) }}" alt="">
                                </div>
                            </div>
                        </a>
                        <div style="min-width: 300px;" class="dropdown-menu dropdown-menu-end p-3 dropdown-menu-profil" data-bs-popper="static">
                            <div class="d-flex align-items-center flex-column">
                                <img style="object-fit: cover;" class="rounded-circle mb-3" height="70" width="70" src="{{ asset('storage/upload/' . Auth::user()->foto) }}" alt="">
                                <p class="text-wrap fw-semibold fs-14px mb-0">{{ Auth::user()->nama }}</p>
                                <p class="text-wrap text-secondary fw-semibold fs-13px mb-0">{{ Auth::user()->username }}</p>
                                <p class="text-wrap fs-13px mb-0">{{ Auth::user()->role->nama }}</p>
                            </div>
                            <hr class="my-3 mb-2">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#logout" class="dropdown-item small btn btn-sm rounded">
                                <div class="row g-0 flex-nowrap align-items-center p-1 px-2">
                                    <i class="fa-regular fa-right-from-bracket col-2"></i>
                                    <p class="m-0 col">Logout</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="modal fade" id="logout" tabindex="-1" aria-hidden="true">
                <form action="{{ route('logout') }}" method="POST" class="modal-dialog">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-16px" id="exampleModalLabel">Konfirmasi Logout</h1>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0 fs-15px">Apakah anda yain ingin logout?</p>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex gap-1 justify-content-end">
                                <button type="button" class="fw-semibold btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="fw-semibold btn btn-sm btn-danger"><i class="fa-regular fa-right-from-bracket"></i> Ya Keluar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            @yield('content')
        </main>
    </div>

    {{-- development test clockwork --}}
    {{-- <script src="https://cdn.jsdelivr.net/gh/underground-works/clockwork-browser@1/dist/toolbar.js"></script> --}}

    {{-- Jquery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    {{-- select2 --}}
    <script src="{{ asset('assets/select2') }}/js/id.js"></script>
    <script src="{{ asset('assets/select2') }}/js/select2.min.js"></script>

    {{-- custom script --}}
    <script src="{{ asset('assets/js') }}/script.js"></script>

    <script>
        const submain = document.querySelector('.mains');
        const main = document.querySelector('.main');

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        @if (request()->query('showing') == 'all')
            setupInfinityScroll('{{ url()->current() }}');
        @endif

        @if (Session::has('message'))
            showAlert("{!! Session::get('message_type') . '","' . Session::get('message') !!}");
        @endif

        function showAlert(type, message) {
            const alertContainer = document.querySelector('.alert-container');
            alertContainer.innerHTML = `
            <div class="alert alert-${type} fs-14px alert-dismissible fade custom mt-3 mb-1" role="alert">
                ${message}
                <button type="button" class="btn-close" onclick="dismissAlert(this)"></button>
            </div>`;
            setTimeout(() => {
                const alert = alertContainer.firstElementChild;
                const alertStyle = window.getComputedStyle(alert);
                alertContainer.style.height =
                    parseInt(alertStyle.height.replace(/[^0-9]/g, "")) +
                    parseInt(alertStyle.marginTop.replace(/[^0-9]/g, "")) +
                    parseInt(alertStyle.marginBottom.replace(/[^0-9]/g, "")) + "px";

                setTimeout(() => {
                    alert.classList.add('show');
                }, 500);
            }, 500);
        }

        function dismissAlert(el) {
            el.parentElement.classList.add('animate');
            el.parentElement.classList.remove('show');
            setTimeout(() => {
                el.parentElement.parentElement.style.height = "";
                el.parentElement.parentElement.innerHTML = '';
            }, 1100);
        }

        if (document.getElementById('modaltambah')) {
            const modal = new bootstrap.Modal('#modaltambah');
            @if (Session::has('show_modal_tambah'))
                modal.show();
            @endif
        }
        if (document.getElementById('modaledit')) {
            const modal = new bootstrap.Modal('#modaledit');
            @if (Session::has('show_modal_edit'))
                modal.show();
            @endif
        }

        const modalhapus = document.getElementById('modalhapus');
        if (modalhapus) {
            modalhapus.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const nama = button.getAttribute('data-bs-nama');
                const id = button.getAttribute('data-bs-id');
                const modalContent = modalhapus.querySelector('.modal-content');
                const elnama = modalhapus.querySelector('.modal-body .nama');

                elnama.textContent = nama;
                modalContent.setAttribute('action', currenturl + "/" + id);
            });
        }

        const modalConfirmEdit = document.getElementById('confirm');
        if (modalConfirmEdit) {
            modalConfirmEdit.addEventListener('show.bs.modal', event => {
                event.preventDefault();
                const input = modalConfirmEdit.parentElement.querySelectorAll('input');
                let state = true;
                input.forEach(element => {
                    if (element.value == '') {
                        element.classList.add('is-invalid');
                        state = false;
                    }
                });

                if (state) {
                    const myModal = new bootstrap.Modal('#confirm');
                    myModal.show();
                } else {
                    modalConfirmEdit.parentElement.classList.add('is-invalid');
                    document.getElementById('invalid-feedback').innerHTML =
                        'Harap pilih data di tabel terlebih dahulu';
                }
            });
        }

        function filterData(el) {
            el.submit();
        }

        function getDetail(url) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        let datas = JSON.parse(xhr.responseText);
                        console.log(datas);
                        const form = document.querySelector('.detail-pane');
                        let i = 0;
                        for (const key in datas) {
                            if (i == 0) {
                                form.setAttribute('action', form.getAttribute('base-action') + datas['id']);
                            }
                            if (form.querySelector('#' + key)) {
                                form.querySelector('#' + key).value = datas[key];
                                form.querySelector('#' + key).classList.remove('is-invalid');
                            }
                            i++;
                        }
                        form.classList.remove('is-invalid');
                    } else {
                        console.error('Error:', xhr.statusText);
                    }
                }
            };

            xhr.send();
        }
    </script>

</body>

</html>
