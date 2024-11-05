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

$destroy = function (medicalRecord $medicalRecord) {
    try {
        $medicalRecord->delete();
        $this->alert('success', 'Data medicalRecord berhasil dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data medicalRecord gagal dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

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
                                <a href="{{ route('medicalRecords.create') }}" class="btn btn-primary">Tambah
                                    medicalRecord</a>
                            </div>
                            <div class="col">
                                <input wire:medicalRecord.live="search" type="search" class="form-control" name=""
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
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Telp</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->medicalRecords as $no => $medicalRecord)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $medicalRecord->name }}</td>
                                            <td>{{ $medicalRecord->email }}</td>
                                            <td>{{ $medicalRecord->telp }}</td>
                                            <td>
                                                <div class="">
                                                    <a href="{{ route('medicalRecords.edit', ['medicalRecord' => $medicalRecord->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $medicalRecord->id }})'
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

                            {{ $this->medicalRecords->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
