<?php

use App\Models\appointment;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('appointments.index');

usesPagination(theme: 'bootstrap');

$appointments = computed(function () {
    return appointment::query()->latest()->paginate(10);
});

$destroy = function (appointment $appointment) {
    try {
        $appointment->delete();
        $this->alert('success', 'Data appointment berhasil dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data appointment gagal dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-app-layout>
    <div>
        <x-slot name="title">Data appointment</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">appointment</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive border rounded px-3">
                            <table class="table text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Dokter</th>
                                        <th>Pasien</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->appointments as $no => $appointment)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $appointment->doctor->name }}</td>
                                            <td>{{ $appointment->patient->name }}</td>
                                            <td>
                                                <span
                                                    class="badge p-2 bg-primary">{{ __('appointment.' . $appointment->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $this->appointments->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
