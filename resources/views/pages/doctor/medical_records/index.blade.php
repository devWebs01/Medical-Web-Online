<?php

use App\Models\medicalRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('medicalRecords.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$medicalRecords = computed(function () {
    if ($this->search == null) {
        return MedicalRecord::query()->latest()->paginate(10);
    } else {
        return MedicalRecord::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(['appointment_id', 'complaint', 'diagnosis', 'physical_exam', 'recommendation', 'type'], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

?>

<x-app-layout>
    <div>
        <x-slot name="title">Data medicalRecord</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('medicalRecords.index') }}">medicalRecord</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <input wire:model.live="search" type="search" class="form-control" name=""
                                    id="search" aria-describedby="helpId" placeholder="Masukkan nama pengguna" />
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded px-3">
                            <table class="table text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Pasien</th>
                                        <th>Status</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->medicalRecords as $no => $medicalRecord)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $medicalRecord->appointment->patient->name }}</td>
                                            <td>
                                                <span
                                                    class="badge p-2
                                                    {{ $medicalRecord->status === 'in-progress' ? 'bg-warning' : '' }}
                                                    {{ $medicalRecord->status === 'completed' ? 'bg-success' : '' }}
                                                    {{ $medicalRecord->status === 'follow_up' ? 'bg-danger' : '' }}">
                                                    {{ __('appointment.' . $medicalRecord->status) }}

                                                </span>
                                                {{ $medicalRecord->status }}
                                            </td>

                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('medicalRecords.patient', ['appointment' => $medicalRecord->appointment->id]) }}"
                                                    role="button">Tindakan</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $this->medicalRecords->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
