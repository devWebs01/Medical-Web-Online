<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Medical' }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('/admin-assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('/admin-assets/css/styles.min.css') }}" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @stack('css')
    @vite([])
</head>

<body onload="window.print()">
    <!--  Body Wrapper -->
    <main>
        {{ $slot }}
    </main>
    <script src="{{ asset('/admin-assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/admin-assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/admin-assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('/admin-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('/admin-assets/libs/simplebar/dist/simplebar.js') }}"></script>

    @stack('scripts')
</body>

</html>
