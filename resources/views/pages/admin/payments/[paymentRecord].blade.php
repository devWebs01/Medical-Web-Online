<?php

use App\Models\PaymentRecord;
use App\Models\Prescription;
use App\Models\Medication;
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
    'additionalFees' => [],
    'prescriptions' => fn() => Prescription::where('medical_record_id', $this->appointmentId)->get(),
    'medications' => Medication::all(),
    'selectedFees' => [], // Biaya tambahan yang dipilih
    'totalCost' => 0,
]);

on([
    'prescription-updated' => function () {
        $this->prescriptions = Prescription::where('appointment_id', $this->appointmentId)->get();
        $this->calculateTotalCost();
    },
]);

rules([
    // Tambahkan rules jika diperlukan
]);

$additionalFees = [
    'administrasi' => 100000,
    'konsultasi_dokter' => 30000,
    'perawatan' => 20000,
    'perawatan_khusus' => 30000,
    'nutrisi' => 45000,
];

$calculateTotalCost = function () use ($additionalFees) {
    $doctorPrescriptions = $this->medicines->sum(function ($prescription) {
        return $prescription->medication->price * $prescription->quantity;
    });

    $costRoom = $this->medicalRecord->type === 'outpatient' ? 0 : 50000;

    $medicationTotal = $this->prescriptions->sum(fn($item) => $item->medication->price * $item->qty);
    $additionalTotal = collect($this->selectedFees)->sum(fn($fee) => $additionalFees[$fee] ?? 0);
    $this->totalCost = $medicationTotal + $additionalTotal + $doctorPrescriptions + $costRoom;
};

$addMedication = function ($medicationId) {
    $medication = Medication::find($medicationId);

    Prescription::updateOrCreate(
        [
            'appointment_id' => $this->appointmentId,
            'medication_id' => $medicationId,
        ],
        [
            'qty' => DB::raw('qty + 1'),
        ],
    );

    $this->dispatch('prescription-updated');
};

$increaseQty = function ($prescriptionId) {
    $prescription = Prescription::find($prescriptionId);
    $prescription->update(['qty' => $prescription->qty + 1]);
    $this->dispatch('prescription-updated');
};

$decreaseQty = function ($prescriptionId) {
    $prescription = Prescription::find($prescriptionId);
    if ($prescription->qty > 1) {
        $prescription->update(['qty' => $prescription->qty - 1]);
        $this->dispatch('prescription-updated');
    }
};

$deletePrescription = function ($prescriptionId) {
    Prescription::find($prescriptionId)->delete();
    $this->dispatch('prescription-updated');
};

$confirmPayment = function () {
    $this->calculateTotalCost();
    $this->alert('success', 'Pembayaran berhasil dikonfirmasi.', ['position' => 'center']);
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
    $this->additionalFees = [
        'administrasi' => 100000,
        'konsultasi_dokter' => 30000,
        'perawatan' => 20000,
        'perawatan_khusus' => 30000,
        'nutrisi' => 45000,
    ];
    $this->medicines = $this->loadMedicines();
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
            <li class="breadcrumb-item"><a href="{{ route('paymentRecords.index') }}">paymentRecord</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $medicalRecord->patient->name }}</a></li>
        </x-slot>

        <div>
            {{ $appointmentId }}
            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#administration" aria-expanded="false" aria-controls="administration">
                                    <strong>Data Pasien</strong>
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
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" wire:change="calculateTotalCost"
                                                {{ $medicalRecord->type === 'outpatient' ?: 'checked' }} disabled>
                                            <div class="row">
                                                <div class="col-md">
                                                    Biaya Kamar
                                                </div>
                                                <div class="col-md text-end">
                                                    {{ formatRupiah(50000) }}
                                                </div>
                                            </div>
                                        </div>
                                        @foreach ($additionalFees as $fee => $amount)
                                            <div class="form-check">
                                                <input type="checkbox" wire:model.live="selectedFees"
                                                    value="{{ $fee }}" class="form-check-input"
                                                    wire:change="calculateTotalCost">

                                                <div class="row">
                                                    <div class="col-md">
                                                        {{ ucwords(str_replace('_', ' ', $fee)) }}
                                                    </div>
                                                    <div class="col-md text-end">
                                                        {{ formatRupiah($amount) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row fw-bolder">
                                        <div class="col-md">
                                            Total Biaya:
                                        </div>
                                        <div class="col-md text-end">
                                            {{ formatRupiah($totalCost) }}
                                        </div>
                                    </div>
                                    <hr>

                                    <button class="btn btn-primary" wire:click="confirmPayment">Konfirmasi
                                        Pembayaran</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-app-layout>
