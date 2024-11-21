<?php

use App\Models\InpatientRecord;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\Room;
use App\Models\PaymentRecord;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses};


uses([LivewireAlert::class]);

state(['medicalRecord', 'inpatientRecordId']);

$dischargePatient = function ($inpatientRecordId) {
    // Temukan InpatientRecord berdasarkan ID

    $medicalRecord = MedicalRecord::find($this->medicalRecord->id);
    $inpatientRecord = InpatientRecord::find($inpatientRecordId);

    if (!$inpatientRecord) {
        $this->alert('error', 'Data rekam medis tidak ditemukan!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    $medicalRecord->update(['status' => 'completed']);

    // Update status menjadi 'discharged'
    $inpatientRecord->update(['status' => 'discharged']);

    // Update kamar menjadi 'available'
    $room = Room::find($inpatientRecord->room_id);
    $room->update(['availability' => 'available']);

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

    // Buat entri PaymentRecord dengan benar
    $paymentRecord = PaymentRecord::updateOrCreate(
        ['medical_record_id' => $inpatientRecord->medical_record_id],
        [
            'total_amount' => $totalAmount,
            'payment_date' => now(),
            'status' => 'unpaid',
        ],
    );

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

    $this->redirectRoute('medicalRecords.index');
};

?>



@volt
    <div>
        <div class="row">
            <div class="col-md-6">
                <strong>Pemeriksaan Fisik:</strong>
                <p>{{ $medicalRecord->physical_exam }}</p>
            </div>

            <div class="col-md-6">
                <strong>Keluhan Pasien:</strong>
                <p>{{ $medicalRecord->complaint }}</p>
            </div>

            <div class="col-md-6">
                <strong>Diagnosa Pasien:</strong>
                <p>{{ $medicalRecord->diagnosis }}</p>
            </div>

            <div class="col-md-6">
                <strong>Saran Perawatan atau Tindakan Lebih Lanjut:</strong>
                <p>{{ $medicalRecord->recommendation }}</p>
            </div>

            <div class="col-md-12">
                <strong>Tipe Perawatan:</strong>
                <p>{{ __('status.' . $medicalRecord->type) }}</p>
            </div>

            @if ($medicalRecord->type === 'inpatient')
                <div class="col-md-6">
                    <strong>Ruangan:</strong>
                    <p>Ruang {{ $medicalRecord->inpatientRecord->room_id }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Tanggal Masuk - Keluar</strong>
                    <p>
                        {{ Carbon\Carbon::parse($medicalRecord->inpatientRecord->admission_date)->format('d M Y') }}
                        -
                        {{ Carbon\Carbon::parse($medicalRecord->inpatientRecord->discharge_date)->format('d M Y') }}
                    </p>
                </div>

                <div class="col-md-12">
                    <strong>Catatan Dokter:</strong>
                    <p>{{ $medicalRecord->doctor_notes ?? '-' }}</p>
                </div>


                <div class="col-md-12 text-end">
                    <button
                        class="btn btn-danger
                    {{ $medicalRecord->status !== 'completed' ?: 'd-none' }}
                     "
                        wire:click="dischargePatient({{ $medicalRecord->inpatientRecord->id }})">
                        Pulangkan Pasien
                    </button>
                </div>
            @endif

        </div>
    </div>
@endvolt
