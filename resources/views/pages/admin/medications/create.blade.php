<?php

use App\Models\medication;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('medications.create');

state(['name', 'dosage', 'price', 'category']);

rules([
    'name' => 'required|string|max:255', // name wajib, harus berupa string, dan maksimal 255 karakter
    'dosage' => 'nullable|string|max:255', // dosage tidak wajib, harus berupa integer, dan minimal 1
    'price' => 'required|numeric|min:0', // price wajib, harus berupa angka, dan minimal 0
    'category' => 'required|string|max:100', // category wajib, harus berupa string, dan maksimal 100 karakter
]);

$create = function () {
    $validateData = $this->validate();

    Medication::create($validateData);

    $this->reset();

    $this->alert('success', 'Data berhasil ditambahkan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('medications.index', navigate: true);
};

?>

<x-app-layout>
    <x-slot name="title">Tambah Obat-Obatan Baru</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('medications.index') }}">Obat-Obatan</a></li>
        <li class="breadcrumb-item"><a href="#">Tambah Obat-Obatan</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah Obat-Obatan</strong>
                        <p>Pada halaman tambah Obat-Obatan, kamu dapat memasukkan informasi dari Obat-Obatan baru yang akan
                            disimpan ke
                            sistem.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <form wire:submit="create">
                        @csrf

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Obat</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" id="name" aria-describedby="nameId"
                                        placeholder="Enter medication name" autofocus autocomplete="name" />
                                    @error('name')
                                        <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="dosage" class="form-label">Dosis</label>
                                    <input type="text" class="form-control @error('dosage') is-invalid @enderror"
                                        wire:model="dosage" id="dosage" aria-describedby="dosageId"
                                        placeholder="Enter medication dosage" />
                                    @error('dosage')
                                        <small id="dosageId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategory Obat</label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror"
                                        wire:model="category" id="category" aria-describedby="categoryId"
                                        placeholder="Enter medication category" autofocus autocomplete="category" />
                                    @error('category')
                                        <small id="categoryId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga</label>
                                    <input type="text" class="form-control @error('price') is-invalid @enderror"
                                        wire:model="price" id="price" aria-describedby="priceId"
                                        placeholder="Enter medication price" autofocus autocomplete="price" />
                                    @error('price')
                                        <small id="priceId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div class="col-md">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                            <div class="col-md align-self-center text-end">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endvolt
</x-app-layout>
