<?php

use App\Models\patient;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('patients.create');

state(['name', 'gender', 'dob', 'address', 'phone']);

rules([
    'name' => 'required|string|max:255',
    'gender' => 'required|in:male,female',
    'dob' => 'required|date|before:today',
    'address' => 'required|string|max:500',
    'phone' => 'required|string|min:11|max:13|regex:/^([0-9\s\-\+\(\)]*)$/',
]);

$create = function () {
    $validateData = $this->validate();

    Patient::create($validateData);

    $this->alert('success', 'Data berhasil ditambahkan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('patients.index', navigate: true);
};

?>

<x-app-layout>
    <x-slot name="title">Tambah patient Baru</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">patient</a></li>
        <li class="breadcrumb-item"><a href="#">Tambah patient</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah patient</strong>
                        <p>Pada halaman tambah pengguna, kamu dapat memasukkan informasi pengguna baru, seperti nama, alamat
                            phone,
                            kata sandi, dan peran pengguna (patient)
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <form wire:submit="create">
                        @csrf

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" name="name" id="name" aria-describedby="nameId"
                                        placeholder="Enter patient name" autofocus autocomplete="name" />
                                    @error('name')
                                        <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telepon</label>
                                    <input type="number" class="form-control @error('phone') is-invalid @enderror"
                                        wire:model="phone" id="phone" aria-describedby="phoneId"
                                        placeholder="Enter patient phone" />
                                    @error('phone')
                                        <small id="phoneId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                    <select wire:model='gender' class="form-select" name="gender" id="gender">
                                        <option selected>Select one</option>
                                        <option value="male">Pria</option>
                                        <option value="female">Wanita</option>
                                    </select>
                                    @error('gender')
                                        <small id="genderId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                        wire:model="dob" id="dob" aria-describedby="dobId"
                                        placeholder="Enter patient dob" />
                                    @error('dob')
                                        <small id="dobId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Tinggal</label>
                            <textarea class="form-control" wire:model="address" name="address" id="address" rows="3"
                                placeholder="Enter address patient"></textarea>
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
