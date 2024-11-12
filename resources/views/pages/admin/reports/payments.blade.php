<?php

use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('reports.paymentRecords');

state([
    'paymentRecords' => fn () => PaymentRecord::query()->latest()->get(),
]);
?>

<x-app-layout>
    <div>
        @include('layouts.table-print')

        <x-slot name="title">Data Antrian Pasien</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="#">Antrian Pasien</a></li>
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
                                        <th>Nama Pasien</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentRecords as $no => $paymentRecord)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $paymentRecord->medicalRecord->patient->name }}</td>
                                            <td>{{ formatRupiah($paymentRecord->total_amount) }}</td>
                                            <td>{{ __('status.' . $paymentRecord->status) }}</td>
                                            <td>{{ Carbon\carbon::parse($paymentRecord->payment_date)->format('d M Y') }}
                                            </td>
                                            
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
