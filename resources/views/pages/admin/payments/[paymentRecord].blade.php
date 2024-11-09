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
    'totalCost', // Tambahkan total biaya
]);

rules([
    // Tambahkan rules jika diperlukan
]);

mount(function ($paymentRecord) {
    $paymentRecordId = $paymentRecord->id;

    // Ambil data rekam medis berdasarkan ID
    $this->paymentRecord = PaymentRecord::with('medicalRecord')->find($paymentRecordId);

    if (!$this->paymentRecord) {
        session()->flash('error', 'Data pembayaran tidak ditemukan.');
        return redirect()->route('medicalRecords.index');
    } else {
        $this->medicalRecord = $this->paymentRecord->medicalRecord;
        $this->medicines = $this->loadMedicines();
        $this->totalCost = $this->calculateTotalCost();
    }
});

// Fungsi untuk memuat data resep
$loadMedicines = function () {
    return Prescription::where('medical_record_id', $this->paymentRecord->medicalRecord->id)->get();
};

// Fungsi untuk menghitung total biaya
$calculateTotalCost = function () {
    $type = $this->medicalRecord->type;

    // Biaya tetap
    $administrationFee = 100000;
    $consultationFee = 30000;

    // Jika rawat jalan
    if ($type === 'rawat jalan') {
        return $administrationFee + $consultationFee;
    }

    // Jika rawat inap, hitung semua biaya kecuali oksigen
    $roomFee = 50000;
    $nursingCare = 20000;
    $specialTreatment = 30000;
    $infusionSet = 25000;
    $infusionFluids = 35000;
    $nutrition = 45000;
    $laboratoryFee = 70000;

    $subtotalNonMedicine = $roomFee + $consultationFee + $nursingCare + $specialTreatment + $infusionSet + $infusionFluids + $nutrition + $administrationFee + $laboratoryFee;

    // Tambahkan biaya obat-obatan
    $medicineCost = $this->medicines->sum(function ($prescription) {
        return $prescription->medication->price * $prescription->quantity;
    });

    return $subtotalNonMedicine + $medicineCost;
};
?>


<x-app-layout>
    @volt
        <x-slot name="title">Edit paymentRecord Baru</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('paymentRecords.index') }}">paymentRecord</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $medicalRecord->patient->name }}</a></li>
        </x-slot>

        <div>
            <div class="card d-print-block border-0">
                <div class="card-body pt-4 pb-0">
                    <div class="row">
                        <h6 class="fw-bolder mb-3">Biodata</h6>
                        <div class="col-md">
                            <p><strong>Nama Pasien:</strong> {{ $medicalRecord->patient->name }}</p>
                            <p><strong>Nomor Rekam Medis:</strong> {{ $medicalRecord->id }}</p>
                            <p><strong>Jenis Kelamin:</strong> {{ $medicalRecord->patient->gender }}</p>
                        </div>
                        <div class="col-md text-md-end">
                            <p><strong>Tanggal Lahir:</strong>
                                {{ \Carbon\Carbon::parse($medicalRecord->patient->dob)->format('d M Y') }}</p>
                            <p><strong>Alamat:</strong> {{ $medicalRecord->patient->address }}</p>
                            <p><strong>Telepon:</strong> {{ $medicalRecord->patient->phone }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <h6 class="fw-bolder mb-3">Rekam Medis</h6>
                        <div class="col-md">
                            <p><strong>Keluhan:</strong>
                                <br>
                                {{ $medicalRecord->complaint }}
                            </p>
                            <p><strong>Diagnosis:</strong>
                                <br>
                                {{ $medicalRecord->diagnosis }}
                            </p>
                            <p><strong>Pemeriksaan Fisik:</strong>
                                <br>
                                {{ $medicalRecord->physical_exam }}
                            </p>
                            <p><strong>Rekomendasi:</strong>
                                <br>
                                {{ $medicalRecord->recommendation }}
                            </p>

                        </div>
                        <div class="col-md text-md-end">
                            <p><strong>Jenis Rawat:</strong>
                                <br>
                                {{ ucfirst($medicalRecord->type) }}
                            </p>
                            <p><strong>Status:</strong>
                                <br>
                                {{ ucfirst($medicalRecord->status) }}
                            </p>
                            <p><strong>Tanggal Dibuat:</strong>
                                <br>
                                {{ \Carbon\Carbon::parse($medicalRecord->created_at)->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                    <hr>
                </div>

                @include('pages.admin.payments.medicalRecord', ['medicalRecord' => $medicalRecord])


                <div class="card-footer">
                    <div class="col-12">
                        <span class="fw-medium text-heading">Note:</span>
                        <span>{{ $medicalRecord->note ?? '-' }}</span>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary rounded" wire:click="confirmPayment">Konfirmasi
                            Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-app-layout>
