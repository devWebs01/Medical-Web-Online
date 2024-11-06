<nav class="sidebar-nav scroll-sidebar" data-simplebar="">
    <ul id="sidebarnav" class="in">
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">Beranda</span>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" href="/home" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
            </a>
        </li>

        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">Manajemen Data </span>
        </li>

        @if (Auth()->user()->role !== 'doctor')
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('users.index') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Akun Pengguna</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('patients.index') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Pasien</span>
                </a>
            </li>
        @endif

        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('medicalRecords.index') }}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Rekam Medis</span>
            </a>
        </li>

        @if (Auth()->user()->role !== 'doctor')
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Klinik</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('medications.index') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Obat</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('rooms.index') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Kamar</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('settings.index') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Pengaturan</span>
                </a>
            </li>
        @endif

        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">Laporan</span>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('appointments.index') }}" aria-expanded="false">
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Janji Temu</span>
            </a>
        </li>


    </ul>
</nav>
<!-- End Sidebar navigation -->
