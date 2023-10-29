<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta username="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/bootstrap') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap') }}/css/bootstrap-utilities.min.css">

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

    <title>Login | {{ nama_aplikasi() }}</title>
</head>

<body>
    <div class="min-vh-100 row g-0 justify-content-center align-items-center" style="background-color: #ffd72f; font-family: poppins, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
        <form method="POST" class="bg-white rounded-4 shadow p-4 py-3 col-3">
            @csrf
            <div class="mb-3 d-flex align-items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="logo aplikasi" width="40" height="40">
                <h4 class="fw-semibold m-0 border-start ps-2 ms-2">Login</h4>
            </div>
            
            @if (Session::has('error_login'))
                <div class="alert alert-danger d-block show fade fs-13px" style="display: none" role="alert">
                    {{ Session::get('error_login') }}
                </div>
            @endif

            <div class="mb-2">
                <label for="username" class="form-label fs-14px mustFilled">Nama Pengguna</label>
                <input type="text" class="form-control form-control-sm @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" autofocus>
                <div class="invalid-feedback fs-13px">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fs-14px mustFilled">Password</label>
                <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}">
                <div class="invalid-feedback fs-13px">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <button class="btn fw-semibold text-white w-100" style="background-color: #ffa857">Masuk</button>
        </form>
    </div>

    <script>
        const form = document.querySelector('form');
        form.addEventListener('keypress', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });

        const input = document.querySelectorAll('form input');
        input.forEach(element => {
            element.addEventListener('keypress', function(e) {
                if (e.keyCode != 13) return false;
                if (element.getAttribute('id') == 'username') input[2].focus();
                if (element.getAttribute('id') == 'password') form.submit();
            });
        });
    </script>
</body>

</html>
