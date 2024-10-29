<?php

use App\Models\Room;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('rooms.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$rooms = computed(function () {
    if ($this->search == null) {
        return Room::query()->latest()->paginate(10);
    } else {
        return room::query()
            ->where(function ($query) {
                // isi
                $query->whereAny([ 'room_number',
        'price',
        'availability',], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (room $room) {
    try {
        $room->delete();
        $this->alert('success', 'Data kamar berhasil dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data kamar gagal dihapus!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-app-layout>
    <div>
        <x-slot name="title">Data Kamar</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">Kamar</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('rooms.create') }}" class="btn btn-primary">Tambah
                                    Kamar</a>
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
                                        <th>Nomor Kamar</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->rooms as $no => $room)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $room->room_number }}</td>
                                            <td>{{ formatRupiah($room->price) }}</td>
                                            <td>{{ __('room.' . $room->availability) }}</td>
                                            <td>
                                                <div class="">
                                                    <a href="{{ route('rooms.edit', ['room' => $room->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $room->id }})'
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

                            {{ $this->rooms->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
