<?php

use App\Models\InpatientRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('inpatientRecords.index');

state(['inpatientRecords' => fn() => inpatientRecord::query()->latest()->get()]);

?>

<x-app-layout>
    @include('layouts.table')

    <div>
        <x-slot name="title">Data inpatientRecord</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inpatientRecords.index') }}">Rawat Inap (One Day Care)</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive border rounded px-3">
                            <table class="table text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Pasien</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Tanggal Keluar</th>
                                        <th>Status</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inpatientRecords as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->medicalRecord->patient->name }}</td>
                                            <td>Ruang {{ $item->room->room_number }}</td>
                                            <td>{{ $item->admission_date }}</td>
                                            <td>{{ $item->discharge_date }}</td>
                                            <td>
                                                <span class="badge bg-dark p-2 text-capitalize">{{ $item->status }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ route('appointments.patient', ['appointment' => $item->medicalRecord->appointment->id]) }}"
                                                        role="button">Detail</a>
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
