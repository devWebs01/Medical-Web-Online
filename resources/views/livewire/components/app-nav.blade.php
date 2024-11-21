<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\PaymentRecord;
use App\Models\Appointment;
use function Livewire\Volt\{state, uses, rules, computed};

$paymentUnpaid = computed(function () {
    return PaymentRecord::where('status', 'unpaid')->count();
});

?>


@volt
    <div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav" class="in">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Beranda</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link position-relative" href="/home" aria-expanded="false">
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

                <li class="sidebar-item {{ Auth()->user()->role === 'owner' ?: 'd-none' }}">
                    <a class="sidebar-link position-relative" href="{{ route('users.index') }}" aria-expanded="false">
                        <span>
                            <i class='fs-6 bx bx-user-circle'></i>
                        </span>
                        <span class="hide-menu">Akun Pengguna</span>

                    </a>
                </li>

                @if (Auth()->user()->role !== 'doctor')
                    <li class="sidebar-item">
                        <a class="sidebar-link position-relative" href="{{ route('patients.index') }}"
                            aria-expanded="false">
                            <span>
                                <i class='fs-6 bx bxs-user-plus'></i>
                            </span>
                            <span class="hide-menu">Pasien</span>
                        </a>
                    </li>
                @endif

                <li class="sidebar-item">
                    <a class="sidebar-link position-relative" href="{{ route('medicalRecords.index') }}"
                        aria-expanded="false">
                        <span>
                            <i class='fs-6 bx bx-plus-medical'></i>
                        </span>
                        <span class="hide-menu">Rekam Medis</span>
                    </a>
                </li>

                @if (Auth()->user()->role !== 'doctor')
                    <li class="sidebar-item">
                        <a class="sidebar-link position-relative" href="{{ route('paymentRecords.index') }}"
                            aria-expanded="false">
                            <span>
                                <i class='fs-6 bx bx-money'></i>
                            </span>
                            <span class="hide-menu">Pembayaran</span>
                            @if ($this->paymentUnpaid > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                </span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Klinik</span>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link position-relative" href="{{ route('medications.index') }}"
                            aria-expanded="false">
                            <span>
                                <i class='fs-6 bx bxs-capsule'></i>
                            </span>
                            <span class="hide-menu">Obat</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link position-relative" href="{{ route('rooms.index') }}" aria-expanded="false">
                            <span>
                                <i class='fs-6 bx bx-door-open'></i>
                            </span>
                            <span class="hide-menu">Kamar</span>
                        </a>
                    </li>
                @endif

                <li class="sidebar-item {{ Auth()->user()->role === 'owner' ?: 'd-none' }}">
                    <a class="sidebar-link position-relative" href="{{ route('settings.index') }}" aria-expanded="false">
                        <span>
                            <i class='fs-6 bx bx-cog'></i>
                        </span>
                        <span class="hide-menu">Pengaturan</span>
                    </a>
                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Laporan</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                        data-bs-target="#reportsDropdown">
                        <span>
                            <i class='fs-6 bx bxs-report'></i>
                        </span>
                        <span class="hide-menu">Laporan</span>
                    </a>
                    <ul id="reportsDropdown" class="collapse">
                        <li>
                            <a class="sidebar-link" href="{{ route('reports.patients') }}">
                                <span>
                                    <i class='fs-6 bx bxs-calendar-plus'></i>
                                </span>
                                <span class="hide-menu">Pasien</span>
                            </a>
                        </li>
                        <li>
                            <a class="sidebar-link" href="{{ route('reports.medicalRecords') }}">
                                <span>
                                    <i class='fs-6 bx bxs-calendar'></i>
                                </span>
                                <span class="hide-menu">Rekam Medis</span>
                            </a>
                        </li>
                        <li>
                            <a class="sidebar-link {{ Auth()->user()->role !== 'doctor' ?: 'd-none' }}" href="{{ route('reports.paymentRecords') }}">
                                <span>
                                    <i class='fs-6 bx bx-money'></i>
                                </span>
                                <span class="hide-menu">Pembayaran</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->

    </div>
@endvolt
