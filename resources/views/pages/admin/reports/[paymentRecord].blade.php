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

name('paymentRecords.print');

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

// $confirmPayment = function () {
//     DB::beginTransaction();

//     try {
//         // Hitung ulang total biaya sebelum menyimpan
//         $this->calculateTotalCost();

//         // Simpan biaya tambahan yang dipilih ke tabel pivot
//         $this->paymentRecord->additionalFees()->sync($this->selectedFees);

//         // Update total biaya pada PaymentRecord
//         $this->paymentRecord->update([
//             'total_amount' => $this->totalCost,
//             'status' => 'paid', // Sesuaikan status jika diperlukan
//         ]);

//         DB::commit();

//         $this->alert('success', 'Pembayaran berhasil dikonfirmasi.', ['position' => 'top']);
//     } catch (\Exception $e) {
//         DB::rollBack();
//         $this->alert('error', 'Terjadi kesalahan saat menyimpan pembayaran: ' . $e->getMessage(), ['position' => 'top']);
//     }
// };

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

<x-print-layout>

    <style>
        * {
            font-size: 10px;
        }
    </style>
    @volt
        <div>
            <div class="">
                <div class="row mb-3">
                    <div class="col">
                        <h3 class="fw-bolder">
                            Klinik Dokter Eva Elvita Syofyan
                        </h3>
                    </div>
                    <div class="col text-end">
                        <h3 class="fw-bolder">
                            Invoice
                        </h3>
                    </div>
                </div>

                <div class="row gap-5">
                    <div class="col rounded-end-pill bg-primary p-3">
                        <h5 class="fw-bolder text-white">{{ $paymentRecord->invoice }}</h5>

                    </div>
                    <div class="col text-end rounded-start-pill bg-primary p-3">
                        <h5 class="fw-bolder text-white">
                            {{ $paymentRecord->payment_date }}
                        </h5>
                    </div>
                </div>

                <div class="card-body mt-3">
                    <div class="row">
                        <h6 class="fw-bolder mb-3">Biodata</h6>
                        <div class="col">
                            <p><strong>Nama Pasien:</strong> {{ $medicalRecord->patient->name }}</p>
                            <p><strong>Nomor Rekam Medis:</strong> {{ $medicalRecord->id }}</p>
                            <p><strong>Jenis Kelamin:</strong> {{ $medicalRecord->patient->gender }}</p>
                        </div>
                        <div class="col text-end">
                            <p><strong>Tanggal Lahir:</strong>
                                {{ \Carbon\Carbon::parse($medicalRecord->patient->dob)->format('d M Y') }}</p>
                            <p><strong>Alamat:</strong> {{ $medicalRecord->patient->address }}</p>
                            <p><strong>Telepon:</strong> {{ $medicalRecord->patient->phone }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <h6 class="fw-bolder mb-3">Rekam Medis</h6>
                        <div class="col">
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
                        <div class="col text-end">
                            <p><strong>Jenis Rawat:</strong>
                                <br>
                                {{ __('status.' . $medicalRecord->type) }}
                            </p>
                            <p><strong>Status:</strong>
                                <br>
                                {{ __('status.' . $medicalRecord->status) }}
                            </p>
                            <p><strong>Tanggal Dibuat:</strong>
                                <br>
                                {{ \Carbon\Carbon::parse($medicalRecord->created_at)->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="col-12">
                        <span class="fw-medium text-heading">Note:</span>
                        <span>{{ $medicalRecord->note ?? '-' }}</span>
                    </div>

                    <hr>

                    <div>
                        <div class="table-responsive pt-0">
                            <h6 class="fw-bolder mb-3">Resep Obat Dokter</h6>
                            <table class="table table-borderless text-center ">
                                <thead>
                                    <tr class="border">
                                        <th>#</th>
                                        <th>Nama Obat</th>
                                        <th>Durasi</th>
                                        <th>Frekuensi</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicines as $no => $prescription)
                                        <tr class="border">
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $prescription->medication->name }}</td>
                                            <td>{{ $prescription->duration }}</td>
                                            <td>{{ $prescription->frequency }}</td>
                                            <td>{{ $prescription->quantity }}</td>
                                            <td>X {{ formatRupiah($prescription->medication->price) }}</td>
                                            <td>
                                                {{ formatRupiah($prescription->medication->price * $prescription->quantity) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Total Biaya Obat -->
                                    <tr class="text-end">
                                        <td colspan="3"></td>
                                        <td colspan="2" class="text-center fw-bolder">Sub Total Obat:</td>
                                        <td colspan="2" class="fw-bolder text-dark">
                                            {{ formatRupiah(
                                                $medicines->sum(function ($prescription) {
                                                    return $prescription->medication->price * $prescription->quantity;
                                                }),
                                            ) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bolder">Biaya Tambahan:</h6>
                        @foreach ($additionalFees as $fee)
                            <div class="form-check">
                                <input type="checkbox" wire:model.live="selectedFees" value="{{ $fee->id }}"
                                    class="form-check-input" wire:change="calculateTotalCost"
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



                </div>

            </div>
        </div>
    @endvolt
</x-print-layout>
