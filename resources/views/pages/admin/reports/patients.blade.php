<?php

use App\Models\Patient;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('reports.patients');

state([
    'patients' => fn() => Patient::query()->latest()->get(),
]);
?>

<x-app-layout>
    <div>
        @include('layouts.table-print')

        <x-slot name="title">Data Pasien</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="#">Pasien</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>NIK/No. Id</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Telepon</th>
                                        <th>Alamat Tinggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->patients as $no => $patient)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $patient->identity }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ __('gender.' . $patient->gender) }}</td>
                                            <td>{{ $patient->dob }}</td>
                                            <td>{{ $patient->phone }}</td>
                                            <td>{{ Str::limit($patient->address, 20, '...') }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
