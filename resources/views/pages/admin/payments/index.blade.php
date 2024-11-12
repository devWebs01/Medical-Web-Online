<?php

use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('paymentRecords.index');

state(['paymentRecords' => fn() => PaymentRecord::query()->latest()->get()]);

?>

<x-app-layout>
    @include('layouts.table')

    <div>
        <x-slot name="title">Data Pembayaran Rekam Medis</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('paymentRecords.index') }}">Pembayaran</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Pasien</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Opsi</th>
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
                                            <td>
                                                <div>
                                                    <a href="{{ route('paymentRecords.show', ['PaymentRecord' => $paymentRecord->id]) }}"
                                                        class="btn btn-sm btn-primary">Detail</a>
                                                </div>
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
