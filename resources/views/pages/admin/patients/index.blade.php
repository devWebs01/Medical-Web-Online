<?php

use App\Models\Patient;
use function Livewire\Volt\{computed, state, usesPagination, uses};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('patients.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$patients = computed(function () {
    if ($this->search == null) {
        return patient::query()->latest()->paginate(10);
    } else {
        return patient::query()
            ->where(function ($query) {
                $query->whereAny(['name', 'gender', 'phone', 'address'], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (patient $patient) {
    try {
        $patient->delete();
        $this->alert('success', 'Data pasien berhasil dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data pasien gagal dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-app-layout>
    <div>
        <x-slot name="title">Data Pasien</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Pasien</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('patients.create') }}" class="btn btn-primary">Tambah
                                    Pasien</a>
                            </div>
                            <div class="col">
                                <input wire:patient.live="search" type="search" class="form-control" name=""
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
                                        <th>Nama Lengkap</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Telepon</th>
                                        <th>Alamat Tinggal</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->patients as $no => $patient)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ __('gender.' . $patient->gender) }}</td>
                                            <td>{{ $patient->dob }}</td>
                                            <td>{{ $patient->phone }}</td>
                                            <td>{{ Str::limit($patient->address, 20, '...') }}</td>
                                            <td>
                                                <div class="">
                                                    <a href="{{ route('patients.edit', ['patient' => $patient->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $patient->id }})'
                                                        wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                        class="btn btn-sm btn-danger">
                                                        {{ __('Hapus') }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $this->patients->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
