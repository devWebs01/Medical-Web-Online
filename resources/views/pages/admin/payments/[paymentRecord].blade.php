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
    'selectedFees' => [], // Tambahkan biaya yang dipilih
    'totalCost',
]);

rules([
    // Tambahkan rules jika diperlukan
]);

mount(function ($paymentRecord) {
    $paymentRecordId = $paymentRecord->id;

    $this->paymentRecord = PaymentRecord::with('medicalRecord')->find($paymentRecordId);

    if (!$this->paymentRecord) {
        session()->flash('error', 'Data pembayaran tidak ditemukan.');
        return redirect()->route('medicalRecords.index');
    }

    $this->medicalRecord = $this->paymentRecord->medicalRecord;
    $this->medicines = $this->loadMedicines();
    $this->totalCost = $this->calculateTotalCost();
});

// Fungsi untuk memuat data resep
$loadMedicines = function () {
    return Prescription::where('medical_record_id', $this->paymentRecord->medicalRecord->id)->get();
};

// Fungsi untuk menghitung total biaya
$calculateTotalCost = function () {
    $type = $this->medicalRecord->type;

    // Biaya dasar
    $administrationFee = 100000;
    $consultationFee = 30000;
    $roomFee = 50000;
    $nursingCare = 20000;
    $specialTreatment = 30000;
    $infusionSet = 25000;
    $infusionFluids = 35000;
    $nutrition = 45000;
    $laboratoryFee = 70000;

    // Biaya tambahan yang dipilih oleh admin
    $additionalFees = [
        'administration' => $administrationFee,
        'consultation' => $consultationFee,
        'room' => $roomFee,
        'nursing' => $nursingCare,
        'special_treatment' => $specialTreatment,
        'infusion_set' => $infusionSet,
        'infusion_fluids' => $infusionFluids,
        'nutrition' => $nutrition,
        'laboratory' => $laboratoryFee,
    ];

    // Hitung total biaya berdasarkan pilihan admin
    $subtotalNonMedicine = collect($this->selectedFees)
        ->map(fn($fee) => $additionalFees[$fee] ?? 0)
        ->sum();

    // Tambahkan biaya obat-obatan
    $medicineCost = $this->medicines->sum(fn($prescription) => $prescription->medication->price * $prescription->quantity);

    return $subtotalNonMedicine + $medicineCost;
};

// Fungsi untuk konfirmasi pembayaran
$confirmPayment = function () {
    $this->totalCost = $this->calculateTotalCost();
    $this->alert('success', 'Pembayaran berhasil dikonfirmasi.', ['position' => 'center']);
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

            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#administration" aria-expanded="false" aria-controls="administration">
                                    <strong>
                                        Data Pasien
                                    </strong>
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
                                    <strong>
                                        Administrasi
                                    </strong>
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
                                            <input type="checkbox" wire:model="selectedFees" value="administration"
                                                class="form-check-input"> Administrasi (Rp 100.000)
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" wire:model="selectedFees" value="consultation"
                                                class="form-check-input"> Konsultasi Dokter (Rp 30.000)
                                        </div>
                                        @if ($medicalRecord->type === 'rawat inap')
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="room"
                                                    class="form-check-input"> Biaya Kamar (Rp 50.000)
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="nursing"
                                                    class="form-check-input"> Perawatan (Rp 20.000)
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="special_treatment"
                                                    class="form-check-input"> Perawatan Khusus (Rp 30.000)
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="infusion_set"
                                                    class="form-check-input"> Set Infus (Rp 25.000)
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="infusion_fluids"
                                                    class="form-check-input"> Cairan Infus (Rp 35.000)
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="nutrition"
                                                    class="form-check-input"> Nutrisi (Rp 45.000)
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="selectedFees" value="laboratory"
                                                    class="form-check-input"> Biaya Laboratorium (Rp 70.000)
                                            </div>
                                        @endif
                                    </div>

                                    <hr>

                                    <h5>Total Biaya: Rp {{ formatRupiah($totalCost) }}</h5>
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
