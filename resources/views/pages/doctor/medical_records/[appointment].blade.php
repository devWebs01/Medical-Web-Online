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

name('medicalRecords.patient');

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

    $medicalRecord = MedicalRecord::updateOrCreate(['appointment_id' => $validateData['appointment_id']], $validateData);

    if ($medicalRecord->exists) {
        $this->updateAppointmentStatus($this->appointment);
        $this->handleInpatientRecord($medicalRecord, $validateData);
    }

    $this->alert('success', 'Data pemeriksaan berhasil disimpan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->medicalRecord = $medicalRecord;
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

mount(function () {
    if ($this->medicalRecord) {
        $this->loadInpatientData($this->medicalRecord);
        $this->loadMedicalRecord($this->medicalRecord);
    }
});

$loadInpatientData = function ($medicalRecord) {
    $inpatientRecord = $medicalRecord->inpatientRecords->first();
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
                @foreach ($errors->all() as $item)
                    {{ $item }}
                @endforeach
                <div class="card-body">
                    <form wire:submit="storeMedicalRecord">
                        @csrf

                        <div class="mb-3">
                            <label for="physical_exam" class="form-label">Pemeriksaan Fisik</label>
                            <textarea wire:model="physical_exam" class="form-control" name="physical_exam" id="physical_exam" rows="3"></textarea>
                            @error('physical_exam')
                                <small id="physical_exam" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="complaint" class="form-label">Keluhan Pasien</label>
                                    <textarea wire:model="complaint" class="form-control" name="complaint" id="complaint" rows="3"></textarea>
                                    @error('complaint')
                                        <small id="complaint" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="diagnosis" class="form-label">Diagnosa Pasien</label>
                                    <textarea wire:model="diagnosis" class="form-control" name="diagnosis" id="diagnosis" rows="3"></textarea>
                                    @error('diagnosis')
                                        <small id="diagnosis" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="recommendation" class="form-label">Saran Perawatan atau Tindakan Lebih
                                Lanjut</label>
                            <textarea wire:model="recommendation" class="form-control" name="recommendation" id="recommendation" rows="3"></textarea>
                            @error('recommendation')
                                <small id="recommendation" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Perawatan</label>
                            <select class="form-select" wire:model.live="type" name="type" id="type">
                                <option selected>Select one</option>
                                <option value="outpatient">Rawat Jalan</option>
                                <option value="inpatient">Rawat Inap (Care One Day)</option>
                            </select>
                            @error('type')
                                <small id="type" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="{{ $type == 'inpatient' ?: 'd-none' }}">

                            <div class="alert alert-primary" role="alert">
                                <p><strong>Rawat inap "Care One Day" (atau rawat inap singkat)</strong> adalah layanan
                                    perawatan medis di
                                    mana pasien tinggal di fasilitas kesehatan selama kurang dari 24 jam.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="rooms" class="form-label">Pilih Kamar</label>

                                <div class="row">
                                    @foreach ($rooms as $room)
                                        <div class="col-md">
                                            <div class="form-check px-0">
                                                <label class="form-check-label card"
                                                    for="flexRadioDefault{{ $room->room_number }}">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto px-5">
                                                                <!-- Set nilai input radio sesuai ID room dan bind dengan Livewire -->
                                                                <input class="form-check-input p-3 border border-primary"
                                                                    type="radio" wire:model="room_id" name="room_id"
                                                                    value="{{ $room->id }}"
                                                                    id="flexRadioDefault{{ $room->room_number }}"
                                                                    {{ $room_id == $room->id ? 'checked' : '' }}>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h5 class="fw-bold">
                                                                    Kamar {{ $room->room_number }}
                                                                </h5>
                                                                <span
                                                                    class="badge {{ $room->availability == 'available' ? 'bg-primary' : 'bg-danger' }}">
                                                                    {{ __('room.' . $room->availability) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                            </div>

                            <div class="mb-3">
                                <label for="doctor_notes" class="form-label">Catatan Dokter</label>
                                <textarea class="form-control" name="doctor_notes" wire:model="doctor_notes" id="doctor_notes" rows="3">
                                    {{ $doctor_notes }}
                                </textarea>
                            </div>


                        </div>

                        <div class="row mb-3">
                            <div class="col-md">
                                <button type="submit" class="btn btn-primary">
                                    {{ $medicalRecord ? 'Edit' : 'Submit' }}
                                </button>
                            </div>
                            <div class="col-md align-self-center text-end">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mb-3 {{ $medicalRecord != null ?: 'd-none' }}">
                @include('pages.doctor.medical_records.[medicalRecord]', [
                    'medicalRecord' => $medicalRecord,
                ])
            </div>
        </div>
    @endvolt
</x-app-layout>
