<?php

use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('paymentRecords.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$paymentRecords = computed(function () {
    if ($this->search == null) {
        return PaymentRecord::query()->latest()->paginate(10);
    } else {
        return PaymentRecord::query()
            ->where(function ($query) {
                // isi
                $query->whereAny([' '], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

?>

<x-app-layout>
    <div>
        <x-slot name="title">Data PaymentRecord</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('paymentRecords.index') }}">PaymentRecord</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <input wire:PaymentRecord.live="search" type="search" class="form-control" name=""
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
                                        <th>Nama Pasien</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->PaymentRecords as $no => $paymentRecord)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $paymentRecord->medicalRecord->patient->name }}</td>
                                            <td>{{ $paymentRecord->total_amount }}</td>
                                            <td>{{ __('status.' . $paymentRecord->status) }}</td>
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

                            {{ $this->PaymentRecords->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
