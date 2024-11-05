<?php

use App\Models\medicalRecord;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('medicalRecords.edit');

state([
    'medicalRecord',
    // isi
]);

rules([
    // isi
]);

$edit = function () {
    $medicalRecord = $this->medicalRecord;

    $validateData = $this->validate();

    $medicalRecord->update($validateData);

    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('medicalRecords.index', navigate: true);
};

?>

<x-app-layout>
    <x-slot name="title">Edit medicalRecord Baru</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('medicalRecords.index') }}">medicalRecord</a></li>
        <li class="breadcrumb-item"><a href="#">Edit medicalRecord</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit medicalRecord</strong>
                        <p>Pada halaman edit medicalRecord, kamu dapat mengubah informasi medicalRecord yang sudah ada.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="contoh1" class="form-label">contoh1</label>
                                    <input type="text" class="form-control @error('contoh1') is-invalid @enderror"
                                        wire:medicalRecord="contoh1" id="contoh1" aria-describedby="contoh1Id"
                                        placeholder="Enter medicalRecord contoh1" autofocus autocomplete="contoh1" />
                                    @error('contoh1')
                                        <small id="contoh1Id" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="contoh2" class="form-label">contoh2</label>
                                    <input type="text" class="form-control @error('contoh2') is-invalid @enderror"
                                        wire:medicalRecord="contoh2" id="contoh2" aria-describedby="contoh2Id"
                                        placeholder="Enter medicalRecord contoh2" />
                                    @error('contoh2')
                                        <small id="contoh2Id" class="form-text text-danger">{{ $message }}</small>
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
