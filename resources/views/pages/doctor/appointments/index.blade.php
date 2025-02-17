<?php

use App\Models\MedicalRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{state};

name('medicalRecords.index');

state(['medicalRecords' => fn() => MedicalRecord::query()->latest()->get()]);

?>

<x-app-layout>
    @include('layouts.table')

    <div>
        <x-slot name="title">Data Rekam Medis</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('medicalRecords.index') }}">Rekam Medis</a></li>
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
                                        <th>Pasien</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalRecords as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->appointment->patient->name }}</td>
                                            <td>
                                                <span class="badge p-2 bg-primary">
                                                    {{ __('status.' . $item->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ __('status.' . $item->type) }}
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('appointments.patient', ['appointment' => $item->appointment->id]) }}"
                                                    role="button">Detail</a>
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
