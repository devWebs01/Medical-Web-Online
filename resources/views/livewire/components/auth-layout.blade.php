<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> {{ $setting->name ?? "Klinik Rekam Medis" }}</title>
        <link rel="shortcut icon" type="image/png" href="{{ asset("/admin-assets/images/logos/favicon.png") }}" />
        <link rel="stylesheet" href="{{ asset("/admin-assets/css/styles.min.css") }}" />
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    </head>

    <body>
        <!--  Body Wrapper -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
            data-sidebar-position="fixed" data-header-position="fixed"
            style="  background-image: url('{{ asset("admin-assets/images/backgrounds/hero-bg.jpg") }}');
        background-repeat: no-repeat;
        background-size: cover; /* Memastikan gambar menutupi seluruh layar */
        background-position: center; /* Memusatkan gambar */
        min-height: 100vh; /* Pastikan tinggi penuh 100% viewport */
        width: 100%;">
            <div
                class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
                <div class="d-flex align-items-center justify-content-center w-100">
                    <section class="p-5">
                        <div class="container-fluid">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-lg-6 mb-5 mb-lg-0">
                                    <div class="pe-lg-3">
                                        <h1 class="display-3 fw-bolder mb-2 mb-md-3">
                                            <span class="text-dark">
                                                {{ $setting->name ?? "Klinik Rekam Medis" }}
                                            </span>
                                        </h1>
                                        <p class="lead mb-4">
                                            Memberikan layanan kesehatan terbaik dengan dukungan sistem informasi yang
                                            efisien.
                                        </p>
                                    </div>

                                </div>
                                <div class="col-lg-6">
                                    <div class="ps-lg-5">
                                        <div class="card shadow-lg border">
                                            <div class="card-body bg-white p-4 p-xl-5 rounded-5">
                                                {{ $slot }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
        <!--  Body Wrapper -->
        @include("layouts.config")
        <script src="{{ asset("/admin-assets/libs/jquery/dist/jquery.min.js") }}"></script>
        <script src="{{ asset("/admin-assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js") }}"></script>
    </body>

</html>
