<?php

use App\Models\User;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('users.create');

state(['name', 'email', 'password', 'telp', 'role']);

rules([
    'name' => 'required|min:5',
    'email' => 'required|min:5|unique:users,email',
    'password' => 'required|min:5',
    'telp' => 'required|unique:users,telp,id|digits_between:11,13',
    'role' => 'required|in:admin,doctor,owner',
]);

$create = function () {
    $validateData = $this->validate();
    User::create($validateData);

    $this->alert('success', 'Data klinik berhasil ditambahkan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('users.index', navigate: true);
};

?>
<x-app-layout>
    <x-slot name="title">Tambah Pengguna Baru</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
        <li class="breadcrumb-item"><a href="#">Tambah Pengguna</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah Pengguna</strong>
                        <p>Pada halaman tambah pengguna, kamu dapat memasukkan informasi pengguna baru, seperti nama, alamat
                            email,
                            kata sandi, dan peran pengguna (role)
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
                                        wire:model="name" id="name" aria-describedby="nameId"
                                        placeholder="Enter admin name" autofocus autocomplete="name" />
                                    @error('name')
                                        <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email" id="email" aria-describedby="emailId"
                                        placeholder="Enter admin email" />
                                    @error('email')
                                        <small id="emailId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="telp" class="form-label">Telpon</label>
                                    <input type="number" class="form-control @error('telp') is-invalid @enderror"
                                        wire:model="telp" id="telp" aria-describedby="telpId"
                                        placeholder="Enter admin telp" />
                                    @error('telp')
                                        <small id="telpId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Kata Sandi</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password" id="password" aria-describedby="passwordId"
                                        placeholder="Enter admin password" />
                                    @error('password')
                                        <small id="passwordId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" wire:model='role' name="role" id="role">
                                <option selected>Select one</option>
                                <option value="admin">Admin</option>
                                <option value="doctor">Dokter</option>
                                <option value="owner">Pemilik</option>
                            </select>
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
