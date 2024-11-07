<?php

use App\Models\PaymentRecord;
use App\Models\Prescription;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, mount};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('paymentRecords.show');

state([
    'paymentRecord',
    'medicalRecord',
    'medicines',
    // isi
]);

rules([
    // isi
]);

mount(function ($paymentRecord) {
    $paymentRecordId = $paymentRecord->id;

    // Ambil data rekam medis berdasarkan ID
    $this->paymentRecord = PaymentRecord::with('medicalRecord')->find($paymentRecordId);

    if (!$this->paymentRecord) {
        // Jika tidak ditemukan, redirect atau tampilkan error
        session()->flash('error', 'Data pembayaran tidak ditemukan.');
        return redirect()->route('medicalRecords.index');
    } else {
        $this->medicalRecord = $this->paymentRecord->medicalRecord;
        $this->medicines = $this->loadMedicines();
    }
});

// Ambil daftar obat yang terkait dengan rekam medis
$loadMedicines = function () {
    $ab = Prescription::where('medical_record_id', $this->paymentRecord->medicalRecord->id)->get();

    dd($ab);
};

?>

<x-app-layout>
    <x-slot name="title">Edit paymentRecord Baru</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('paymentRecords.index') }}">paymentRecord</a></li>
        <li class="breadcrumb-item"><a href="#">Edit paymentRecord</a></li>
    </x-slot>

    @volt
        <div>
            <h1 class="text-2xl font-bold mb-4">Halaman Pembayaran</h1>

            @if (session()->has('error'))
                <div class="bg-red-500 text-white p-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <h2 class="text-xl font-semibold">Data Rekam Medis</h2>
            <p><strong>Nama Pasien:</strong> {{ $medicalRecord->patient_name }}</p>
            <p><strong>Nomor Rekam Medis:</strong> {{ $medicalRecord->id }}</p>
            <p><strong>Tanggal:</strong> {{ $medicalRecord->created_at->format('d-m-Y') }}</p>

            <h2 class="text-xl font-semibold mt-4">Daftar Obat</h2>
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 p-2">Nama Obat</th>
                        <th class="border border-gray-300 p-2">Harga</th>
                        <th class="border border-gray-300 p-2">Jumlah</th>
                        <th class="border border-gray-300 p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicines as $prescription)
                        <tr>
                            <td class="border border-gray-300 p-2">{{ $prescription->medicine->name }}</td>
                            <td class="border border-gray-300 p-2">{{ number_format($prescription->medicine->price, 2) }}
                            </td>
                            <td class="border border-gray-300 p-2">{{ $prescription->quantity }}</td>
                            <td class="border border-gray-300 p-2">
                                {{ number_format($prescription->medicine->price * $prescription->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <h2 class="text-xl font-semibold">Total Pembayaran:
                    {{ number_format(
                        $medicines->sum(function ($prescription) {
                            return $prescription->medicine->price * $prescription->quantity;
                        }),
                        2,
                    ) }}
                </h2>
            </div>

            <div class="mt-4">
                <button class="bg-blue-500 text-white px-4 py-2 rounded" wire:click="confirmPayment">Konfirmasi
                    Pembayaran</button>
            </div>
        </div>
    @endvolt
</x-app-layout>
