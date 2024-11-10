<?php

use App\Models\InpatientRecord;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('inpatientRecords.index');

state(['inpatientRecords' => fn() => inpatientRecord::query()->latest()->get()]);

$dischargePatient = function ($inpatientRecordId) {
    // Temukan InpatientRecord berdasarkan ID
    $inpatientRecord = InpatientRecord::find($inpatientRecordId);

    if (!$inpatientRecord) {
        $this->alert('error', 'Data rekam medis tidak ditemukan!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    // Update status menjadi 'discharged'
    $inpatientRecord->update(['status' => 'discharged']);

    // Ambil data obat-obatan yang terkait dengan medical record
    $prescriptions = Prescription::where('medical_record_id', $inpatientRecord->medical_record_id)->get();

    // Hitung total biaya
    $totalAmount = 0;
    $medicationsData = [];

    foreach ($prescriptions as $prescription) {
        $medication = Medication::find($prescription->medicine_id);
        if ($medication) {
            $totalAmount += $medication->price * $prescription->quantity;
            $medicationsData[] = [
                'medicine_id' => $medication->id,
                'quantity' => $prescription->quantity,
                'price' => $medication->price,
            ];
        }
    }

    // Buat entri PaymentRecord
    $paymentRecord = PaymentRecord::create([
        'medical_record_id' => $inpatientRecord->medical_record_id,
        'total_amount' => $totalAmount,
        'payment_date' => now(),
        'status' => 'pending',
    ]);

    // Mengaitkan obat-obatan dengan entri pembayaran
    foreach ($medicationsData as $data) {
        $paymentRecord->medications()->attach($data['medicine_id'], [
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    // Tampilkan pesan sukses
    $this->alert('success', 'Pasien telah dipulangkan dan pembayaran berhasil dibuat!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    // Refresh data
    $this->inpatientRecords = InpatientRecord::query()->latest()->get();

    $this->redirectRoute('inpatientRecords.index');
};

?>

<x-app-layout>
    @include('layouts.table')

    <div>
        <x-slot name="title">Data inpatientRecord</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inpatientRecords.index') }}">Rawat Inap (One Day Care)</a></li>
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
                                        <th>Nama Pasien</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Tanggal Keluar</th>
                                        <th>Status</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inpatientRecords as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->medicalRecord->patient->name }}</td>
                                            <td>Ruang {{ $item->room->room_number }}</td>
                                            <td>{{ $item->admission_date }}</td>
                                            <td>{{ $item->discharge_date }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-dark p-2 text-capitalize">{{ __('status.' . $item->status) }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ route('appointments.patient', ['appointment' => $item->medicalRecord->appointment->id]) }}"
                                                        role="button">Detail</a>
                                                    <button
                                                        class="btn btn-danger btn-sm {{ $item->status === 'active' ?: 'd-none' }}"
                                                        wire:click="dischargePatient({{ $item->id }})">Pulangkan</button>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-app-layout>
