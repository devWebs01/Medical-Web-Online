<?php

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\InpatientRecord;
use App\Models\Room;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, mount, boot};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('appointments.patient');

state([
    // Get Data
    'rooms' => fn() => Room::all(),
    'role' => fn() => Auth::user()->role,
    'medicalRecord' => fn() => MedicalRecord::where('appointment_id', $this->appointment->id)->first(),
    'appointment',

    // Inpatient MedicalRecord
    'room_id',
    'admission_date',
    'discharge_date',
    'doctor_notes',
    'status',

    // Get MedicalRecord
    'complaint',
    'diagnosis',
    'physical_exam',
    'recommendation',
    'type',
]);

rules([
    'complaint' => 'required|string|max:255',
    'diagnosis' => 'required|string|max:255',
    'physical_exam' => 'nullable|string|max:255',
    'recommendation' => 'nullable|string|max:255',
    'type' => 'required|in:outpatient,inpatient',
    'room_id' => 'nullable|exists:rooms,id',
    'doctor_notes' => 'nullable|string',
]);

$storeMedicalRecord = function () {
    $validateData = $this->validate();
    $validateData['patient_id'] = $this->appointment->patient_id;
    $validateData['appointment_id'] = $this->appointment->id;

    if ($this->type === 'inpatient') {
        $this->alert('error', 'Pilih kamar pasien yang diinginkan', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);
    }

    $medicalRecord = MedicalRecord::updateOrCreate(['appointment_id' => $validateData['appointment_id']], $validateData);

    if ($medicalRecord->exists) {
        $this->updateAppointmentStatus($this->appointment);
        $this->handleInpatientRecord($medicalRecord, $validateData);
        $this->handleBookingRoom($medicalRecord);
    }

    $this->alert('success', 'Data pemeriksaan berhasil disimpan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->medicalRecord = $medicalRecord;

    $this->redirectRoute('appointments.patient', ['appointment' => $this->appointment], navigate: true);
};

$updateAppointmentStatus = function ($appointment) {
    $appointment->update(['status' => 'checked-in']);
};

$handleInpatientRecord = function ($medicalRecord, $validateData) {
    if ($validateData['type'] === 'inpatient') {
        InpatientRecord::updateOrCreate(
            ['medical_record_id' => $medicalRecord->id],
            [
                'room_id' => $validateData['room_id'] ?? null,
                'admission_date' => now(),
                'discharge_date' => now()->addDay(1),
                'doctor_notes' => $validateData['doctor_notes'] ?? null,
                'status' => 'active',
            ],
        );
    } else {
        InpatientRecord::where('medical_record_id', $medicalRecord->id)->delete();
    }
};

$handleBookingRoom = function ($medicalRecord) {
    if ($medicalRecord->type === 'inpatient') {
        Room::find($this->room_id)->update(['availability' => 'occupied']);
    }
};

mount(function () {
    if ($this->medicalRecord) {
        $this->loadInpatientData($this->medicalRecord);
        $this->loadMedicalRecord($this->medicalRecord);
    }
});

$loadInpatientData = function ($medicalRecord) {
    $inpatientRecord = $medicalRecord->inpatientRecord;
    if ($inpatientRecord) {
        $this->room_id = $inpatientRecord->room_id;
        $this->admission_date = $inpatientRecord->admission_date;
        $this->discharge_date = $inpatientRecord->discharge_date;
        $this->doctor_notes = $inpatientRecord->doctor_notes;
        $this->status = $inpatientRecord->status;
    }
};

$loadMedicalRecord = function ($medicalRecord) {
    if ($medicalRecord) {
        $this->complaint = $this->medicalRecord->complaint;
        $this->diagnosis = $this->medicalRecord->diagnosis;
        $this->physical_exam = $this->medicalRecord->physical_exam;
        $this->recommendation = $this->medicalRecord->recommendation;
        $this->type = $this->medicalRecord->type;
    }
};

?>

<x-app-layout>
    <x-slot name="title">Pemeriksaan</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="#">Rekam Medis</a></li>
        <li class="breadcrumb-item"><a href="#">{{ $appointment->patient->name }}</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card mb-3">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Pemeriksaan - {{ $appointment->patient->name }}</strong>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        @if ($medicalRecord)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $medicalRecord ? 'active' : '' }}" id="pills-checkUp-tab"
                                    data-bs-toggle="pill" data-bs-target="#pills-checkUp" type="button" role="tab"
                                    aria-controls="pills-checkUp" aria-selected="{{ $medicalRecord ? 'true' : 'false' }}">
                                    Data Pemeriksaan
                                </button>
                            </li>
                        @endif

                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !$medicalRecord ? 'active' : '' }}" id="pills-formMedicalRecord-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-formMedicalRecord" type="button"
                                role="tab" aria-controls="pills-formMedicalRecord"
                                aria-selected="{{ !$medicalRecord ? 'true' : 'false' }}">
                                Form Input
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-history-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-history" type="button" role="tab" aria-controls="pills-history"
                                aria-selected="false">
                                Riwayat
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        @if ($medicalRecord)
                            <div class="tab-pane fade {{ $medicalRecord ? 'show active' : '' }}" id="pills-checkUp"
                                role="tabpanel" aria-labelledby="pills-checkUp-tab" tabindex="0">
                                @include('pages.doctor.appointments.checkUp')
                            </div>
                        @endif

                        <div class="tab-pane fade {{ !$medicalRecord ? 'show active' : '' }}" id="pills-formMedicalRecord"
                            role="tabpanel" aria-labelledby="pills-formMedicalRecord-tab" tabindex="0">
                            @include('pages.doctor.appointments.formMedicalRecord')
                        </div>

                        <div class="tab-pane fade" id="pills-history" role="tabpanel" aria-labelledby="pills-history-tab"
                            tabindex="0">
                            @include('pages.doctor.appointments.history', ['patient' => $appointment->patient])
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 {{ $medicalRecord != null ?: 'd-none' }}">

                @include('pages.doctor.appointments.prescription', [
                    'medicalRecord' => $medicalRecord,
                ])
            </div>
        </div>
    @endvolt
</x-app-layout>
