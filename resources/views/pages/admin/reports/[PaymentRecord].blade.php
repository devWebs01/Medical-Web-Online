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
        body {
            font-size: 10px;
        }

        table {
            font-size: 8px;
        }
    </style>
    @volt
        <div>
            <h5 class="fw-bolder text-center mb-3">
                Klinik Dokter Eva Elvita Syofyan
            </h5>

            <div class="card-body">
                <div class="row">
                    <h6 class="fw-bolder">Biodata</h6>
                    <div class="col">
                        <p><strong>Nama Pasien:</strong>
                            <br>
                            {{ $medicalRecord->patient->name }}
                        </p>
                        <p><strong>Nomor Rekam Medis:</strong>
                            <br>
                            {{ $medicalRecord->id }}
                        </p>
                        <p><strong>Jenis Kelamin:</strong>
                            <br>
                            {{ $medicalRecord->patient->gender }}
                        </p>
                    </div>
                    <div class="col text-end">
                        <p><strong>Tanggal Lahir:</strong>
                            <br>

                            {{ \Carbon\Carbon::parse($medicalRecord->patient->dob)->format('d M Y') }}
                        </p>
                        <p><strong>Alamat:</strong>
                            <br>
                            {{ $medicalRecord->patient->address }}
                        </p>
                        <p><strong>Telepon:</strong>
                            <br>
                            {{ $medicalRecord->patient->phone }}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h6 class="fw-bolder">Rekam Medis</h6>
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
                        <h6 class="fw-bolder">Resep Obat Dokter</h6>
                        <table class="table table-borderless text-center table-sm">
                            <thead>
                                <tr class="border">
                                    <th class="text-start">Nama Obat</th>
                                    <th>Durasi</th>
                                    <th>Frekuensi</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medicines as $no => $prescription)
                                    <tr class="border">
                                        <td class="text-start">{{ $prescription->medication->name }}</td>
                                        <td>{{ $prescription->duration }}</td>
                                        <td>{{ $prescription->frequency }}</td>
                                        <td>{{ $prescription->quantity }}</td>
                                        <td>X {{ formatRupiah($prescription->medication->price) }}</td>
                                        <td class="text-end">
                                            {{ formatRupiah($prescription->medication->price * $prescription->quantity) }}
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Total Biaya Obat -->
                                <tr class="text-end">
                                    <td colspan="4"></td>
                                    <td class="text-center fw-bolder">Sub Total Obat:</td>
                                    <td class="fw-bolder text-dark">
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
                    <div class="form-check">
                        <input type="checkbox" value="room" class="form-check-input" disabled
                            {{ $medicalRecord->type !== 'inpatient' ?: 'checked' }}>
                        <div class="row">
                            <div class="col">Biaya Kamar</div>
                            <div class="col text-end">{{ formatRupiah(50000) }}</div>
                        </div>
                    </div>

                    @foreach ($additionalFees as $fee)
                        <div class="form-check">
                            <input type="checkbox" wire:model.live="selectedFees" value="{{ $fee->id }}"
                                class="form-check-input" wire:change="calculateTotalCost"
                                {{ $paymentRecord->status === 'unpaid' ?: 'disabled' }}>
                            <div class="row">
                                <div class="col">{{ $fee->name }}</div>
                                <div class="col text-end">{{ formatRupiah($fee->cost) }}</div>
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
    @endvolt
</x-print-layout>
