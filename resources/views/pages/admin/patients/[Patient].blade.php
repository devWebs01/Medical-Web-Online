<?php

use App\Models\Patient;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('patients.edit');

state([
    'identity' => fn() => $this->patient->identity,
    'name' => fn() => $this->patient->name,
    'gender' => fn() => $this->patient->gender,
    'dob' => fn() => $this->patient->dob,
    'address' => fn() => $this->patient->address,
    'phone' => fn() => $this->patient->phone,
    'patient',
]);

$edit = function () {
    $patient = $this->patient;

    $validateData = $this->validate([
        'identity' => [
            'required',
            Rule::unique(Patient::class)->ignore($patient->id),
        ],
        'name' => 'required|string|max:255',
        'gender' => 'required|in:male,female',
        'dob' => 'required|date|before:today',
        'address' => 'required|string|max:500',
        'phone' => 'required|min:11|max:13|regex:/^([0-9\s\-\+\(\)]*)$/',
    ]);

    $patient->update($validateData);

    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('patients.index', navigate: true);
};

?>

<x-app-layout>
    <x-slot name="title">Edit Pasien</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Pasien</a></li>
        <li class="breadcrumb-item"><a href="#">Edit Pasien</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit Pasien</strong>
                        <p>Pada halaman edit pasien, kamu dapat mengubah informasi pasien yang sudah ada.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="identity" class="form-label">NIK / No. Id</label>
                                    <input type="number" class="form-control @error('identity') is-invalid @enderror"
                                        wire:model="identity" identity="identity" id="identity"
                                        aria-describedby="identityId" placeholder="Enter patient identity" autofocus
                                        autocomplete="identity" />
                                    @error('identity')
                                        <small id="identityId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

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
