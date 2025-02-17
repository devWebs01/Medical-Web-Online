<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> {{ $setting->name ?? 'Klinik Dokter Eva Elvita Syofyan' }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('/admin-assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('/admin-assets/css/styles.min.css') }}" />


</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed"
        style="  background-image: url('{{ asset('admin-assets/images/backgrounds/hero-bg.jpg') }}');
        background-repeat: no-repeat;
        background-size: cover; /* Memastikan gambar menutupi seluruh layar */
        background-position: center; /* Memusatkan gambar */
        min-height: 100vh; /* Pastikan tinggi penuh 100% viewport */
        width: 100%;"
        >
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
                                            {{ $setting->name ?? 'Klinik Dokter Eva Elvita Syofyan' }}
                                        </span>
                                    </h1>
                                    <p class="lead mb-4">
                                        Memberikan layanan kesehatan terbaik dengan dukungan sistem informasi yang
                                        efisien.
                                    </p>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="text-primary">
                                                    <svg class="bi bi-chat-right-fill" fill="currentColor"
                                                        height="32" viewbox="0 0 16 16" width="32"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14 0a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0">24/7</h6>
                                                <p>Customer Support</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex">
                                            <div class="text-primary">
                                                <svg class="bi bi-shield-fill-check" fill="currentColor" height="32"
                                                    viewbox="0 0 16 16" width="32"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647z"
                                                        fill-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0">99.99%</h6>
                                                <p>Uptime Guarantee</p>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
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
    <script src="{{ asset('/admin-assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/admin-assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
