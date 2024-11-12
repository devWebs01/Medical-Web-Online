<?php

use App\Models\PaymentRecord;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\AdditionalFees;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, mount, on};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('paymentRecords.show');

state([
    'paymentRecord',
    'medicalRecord',
    'medicines',
    'appointmentId',
    'prescriptions' => fn() => Prescription::where('medical_record_id', $this->appointmentId)->get(),
    'medications' => Medication::all(),
    'selectedFees' => [], // Biaya tambahan yang dipilih
    'totalCost' => fn() => $this->paymentRecord->total_amount ?? 0, // Ambil dari database jika tersedia
    'additionalFees' => fn() => AdditionalFees::all(),
]);

on([
    'prescription-updated' => function () {
        $this->prescriptions = Prescription::where('appointment_id', $this->appointmentId)->get();
        $this->calculateTotalCost();
    },
]);

$calculateTotalCost = function () {
    $doctorPrescriptions = $this->medicines->sum(function ($prescription) {
        return $prescription->medication->price * $prescription->quantity;
    });

    $medicationTotal = $this->prescriptions->sum(fn($item) => $item->medication->price * $item->qty);
    $additionalTotal = collect($this->selectedFees)->sum(fn($feeId) => AdditionalFees::find($feeId)->cost);
    $this->totalCost = $medicationTotal + $additionalTotal + $doctorPrescriptions;
};

$confirmPayment = function () {
    DB::beginTransaction();

    try {
        // Hitung ulang total biaya sebelum menyimpan
        $this->calculateTotalCost();

        // Simpan biaya tambahan yang dipilih ke tabel pivot
        $this->paymentRecord->additionalFees()->sync($this->selectedFees);

        // Update total biaya pada PaymentRecord
        $this->paymentRecord->update([
            'total_amount' => $this->totalCost,
            'status' => 'paid', // Sesuaikan status jika diperlukan
        ]);

        DB::commit();

        $this->alert('success', 'Pembayaran berhasil dikonfirmasi.', ['position' => 'top']);
    } catch (\Exception $e) {
        DB::rollBack();
        $this->alert('error', 'Terjadi kesalahan saat menyimpan pembayaran: ' . $e->getMessage(), ['position' => 'top']);
    }
};

mount(function ($paymentRecord) {
    $paymentRecordId = $paymentRecord->id;

    $this->paymentRecord = PaymentRecord::with('medicalRecord')->find($paymentRecordId);

    if (!$this->paymentRecord) {
        session()->flash('error', 'Data pembayaran tidak ditemukan.');
        return redirect()->route('medicalRecords.index');
    }

    $this->medicalRecord = $this->paymentRecord->medicalRecord;
    $this->appointmentId = $this->paymentRecord->medicalRecord->appointment->id;
    $this->medicines = $this->loadMedicines();

    // Inisialisasi selectedFees dengan data yang sudah tersimpan
    $this->selectedFees = $this->paymentRecord->additionalFees->pluck('id')->toArray();

    $this->calculateTotalCost();
});

// Fungsi untuk memuat data resep
$loadMedicines = function () {
    return Prescription::where('medical_record_id', $this->paymentRecord->medicalRecord->id)->get();
};
?>

<x-app-layout>
    @volt
        <x-slot name="title">Pembayaran {{ $medicalRecord->patient->name }}</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('paymentRecords.index') }}">Pembayaran</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $medicalRecord->patient->name }}</a></li>
        </x-slot>

        <div>
            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#administration" aria-expanded="false" aria-controls="administration">
                                    <strong>Data Pasien</strong>
                                    <span
                                        class=" ms-3 badge text-bg-primary fs-1">{{ __('status.' . $medicalRecord->type) }}</span>
                                </button>
                            </h2>
                            <div id="administration" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    @include('pages.admin.payments.loadPatient', [
                                        'medicalRecord' => $medicalRecord,
                                    ])
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#administration" aria-expanded="true" aria-controls="administration">
                                    <strong>Administrasi</strong>
                                </button>
                            </h2>
                            <div id="administration" class="accordion-collapse collapse show"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    @include('pages.admin.payments.loadMedicines')

                                    <hr>

                                    <div class="mb-4">
                                        <h6 class="fw-bolder">Biaya Tambahan:</h6>
                                        @foreach ($additionalFees as $fee)
                                            <div class="form-check">
                                                <input type="checkbox" wire:model.live="selectedFees"
                                                    value="{{ $fee->id }}" class="form-check-input"
                                                    wire:change="calculateTotalCost"
                                                    {{ $paymentRecord->status === 'unpaid' ?: 'disabled' }}>
                                                <div class="row">
                                                    <div class="col-md">{{ $fee->name }}</div>
                                                    <div class="col-md text-end">{{ formatRupiah($fee->cost) }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row fw-bolder mb-3">
                                        <div class="col">Total Biaya:</div>
                                        <div class="col text-end">{{ formatRupiah($totalCost) }}</div>
                                    </div>

                                    <div class="row fw-bolder mb-3">
                                        <div class="col">Status Pembayaran:</div>
                                        <div class="col text-end">{{ __('status.' . $paymentRecord->status) }}</div>
                                    </div>

                                    <hr>

                                    <div class="row fw-bolder mb-3">
                                        <div class="col">
                                            <button
                                                class="btn btn-primary {{ $paymentRecord->status == 'unpaid' ?: 'd-none' }}"
                                                wire:click="confirmPayment">Konfirmasi
                                                Pembayaran</button>
                                        </div>
                                        <div class="col text-end">

                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-app-layout>
