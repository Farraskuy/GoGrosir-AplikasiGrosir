<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/bootstrap') }}/css/bootstrap.min.css">

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
    <link rel="stylesheet" href="{{ asset('assets/css') }}/kasir-style.css">

    <script>
        const baseurl = '{{ url('/') }}';
        const csrf = '{{ csrf_token() }}';
    </script>

    <title>@yield('title')</title>
</head>


<body class="bg-light vh-100 d-flex flex-column" style="font-family: poppins, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">

    <div class="container-fluid bg-white d-flex justify-content-center py-2">
        <div class="d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="logo aplikasi" width="35" height="35">
            <h5 class="m-0 border-start ms-2 ps-2">Kasir</h5>
        </div>
    </div>

    <main style="height: calc(100% - (51px * 2))">
        @yield('content')
    </main>

    <div class="container-fluid d-flex row g-0 text-white" style="background-color: #00a545; height: 51px">
        <a href="/kasir" class="col h-100 d-flex justify-content-center align-items-center nav-kasir-bottom  @if (request()->is('kasir')) {{ 'active' }} @endif" style="cursor: pointer">
            <i class="fa-regular fa-calculator"></i>
            <h6 class="m-0 ms-2 ps-2">Penjualan</h6>
        </a>
        <div class="border col-auto my-2"></div>
        <a href="/kasir/histori" class="col h-100 d-flex justify-content-center align-items-center nav-kasir-bottom  @if (request()->is('kasir/histori') || request()->is('kasir/histori*')) {{ 'active' }} @endif" style="cursor: pointer">
            <i class="fa-regular fa-receipt"></i>
            <h6 class="m-0 ms-2 ps-2">Histori Penjualan</h6>
        </a>
        <div class="border col-auto my-2"></div>
        <div class="col-auto d-flex justify-content-center align-items-center px-2">
            <button class="btn rounded-3 border" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fa-solid fa-bars text-white"></i></button>
        </div>
    </div>


    <div class="offcanvas offcanvas-end d-flex align-items-end show" style="width: 320px" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="d-flex align-items-center w-100 p-3 py-2">
            <img src="{{ asset('assets/img/logo.png') }}" alt="logo aplikasi" width="35" height="35">
            <h5 class="m-0 border-start ms-2 ps-2">App Toko Grosir Zafira</h5>
        </div>
        <div class="offcanvas-body w-100 d-flex flex-column justify-content-end">
            <div class="d-flex flex-column align-items-center mb-2 pb-3 border-bottom">
                <img class="rounded-circle mb-3" src="{{ asset('storage/upload/' . Auth::user()->foto) }}" height="100" width="100">
                <small class="p-0 m-0 fw-semibold wrap-text fs-15px">{{ Auth::user()->nama }}</small>
                <small class="p-0 m-0 fw-semibold wrap-text text-secondary">{{ Auth::user()->username }}</small>
                <small class="p-0 m-0 wrap-text fs-14px">{{ Auth::user()->role->nama }}</small>
            </div>
            @if (Auth::user()->hasPermission('dashboard'))
                <a href="/" class="btn offcanvas-menu"><i class="fa-regular fa-grid-2"></i>Dashboard</a>
            @endif
            <button type="button" data-bs-toggle="modal" data-bs-target="#logout" class="btn offcanvas-menu w-100"><i class="fa-regular fa-right-from-bracket"></i>Logout</button>
        </div>
        <div class="offcanvas-header w-100 border-top" style="height: 54px;">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>

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

    {{-- Jquery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    {{-- select2 --}}
    <script src="{{ asset('assets/select2') }}/js/id.js"></script>
    <script src="{{ asset('assets/select2') }}/js/select2.min.js"></script>
    {{-- bootstrap js --}}
    <script src="{{ asset('assets/bootstrap') }}/js/bootstrap.bundle.min.js"></script>

    {{-- custom script --}}
    <script src="{{ asset('assets/js') }}/script.js"></script>

    @if (request()->is('kasir'))
        <script src="{{ asset('assets/js') }}/script-kasir.js"></script>
    @endif
    @if (request()->is('kasir/histori') || request()->is('kasir/histori*'))
        <script src="{{ asset('assets/js') }}/script-kasir-histori.js"></script>
    @endif
</body>

</html>
