<?php

use App\Models\Medication;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('medications.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$medications = computed(function () {
    if ($this->search == null) {
        return medication::query()->latest()->paginate(10);
    } else {
        return medication::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(['name', 'dosage', 'price', 'category'], 'LIKE', "{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (medication $medication) {
    try {
        $medication->delete();
        $this->alert('success', 'Data obat-obatan berhasil dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data obat-obatan gagal dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-app-layout>
    <div>
        <x-slot name="title">Data Obat-Obatan</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('medications.index') }}">Obat-Obatan</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('medications.create') }}" class="btn btn-primary">Tambah
                                    Obat-Obatan</a>
                            </div>
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
                                        <th>Kategori</th>
                                        <th>Nama</th>
                                        <th>Dosis</th>
                                        <th>Harga</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->medications as $no => $medication)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $medication->category }}</td>
                                            <td>{{ $medication->name }}</td>
                                            <td>{{ $medication->dosage }}</td>
                                            <td>{{ formatRupiah($medication->price) }}</td>
                                            <td>
                                                <div class="">
                                                    <a href="{{ route('medications.edit', ['medication' => $medication->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $medication->id }})'
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

                            {{ $this->medications->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
