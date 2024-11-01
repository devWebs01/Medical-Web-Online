<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use function Livewire\Volt\{state, uses, rules, computed};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

state(['patient_id', 'doctor_id', 'notes']);

state([
    'doctors' => fn() => User::where('role', 'doctor')->get(),
    'date' => fn() => now(),
]);

rules([
    'patient_id' => 'required|exists:patients,id',
    'doctor_id' => 'required|exists:users,id',
    'date' => 'required|date',
    'notes' => 'nullable',
]);

$appointments = computed(function () {
    return Appointment::all()->sortBy(function ($appointment) {
        return [$appointment->status !== 'waiting', $appointment->date];
    });
});

$patients = computed(function () {
    return Patient::whereDoesntHave('appointments', function ($query) {
        $query->where('status', 'waiting')->whereDate('date', Carbon::today());
    })->get();
});

$save = function () {
    $validateData = $this->validate();

    Appointment::create($validateData);

    $this->reset('patient_id', 'doctor_id', 'notes');

    $this->alert('success', 'Data klinik berhasil ditambahkan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);
};

?>

<x-app-layout>
    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    Janji Temu Pasien
                </div>
                <div class="card-body">
                    <form action="save" wire:submit='save' method="post">
                        @csrf

                        <div class="d-none mb-3">
                            <label for="date" class="form-label">Tanggal</label>
                            <input type="dateTime-local" class="form-control" name="date" id="date" wire:model='date'
                                aria-describedby="Id" placeholder="date" />
                            @error('date')
                                <small id="dateId" class="text-danger text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md mb-3">
                                <label for="patient_id" class="form-label">Pasien</label>
                                <select class="form-select" wire:model='patient_id' name="patient_id" id="doctor_id">
                                    <option selected>Select one</option>
                                    @foreach ($this->patients() as $patient)
                                        <option value="{{ $patient->id }}">
                                            {{ $patient->name }} -
                                            {{ __('gender.' . $patient->gender) }} -
                                            {{ Carbon::parse($patient->dob)->format('d M Y') }}

                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <small class="text-danger fw-bold">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md mb-3">
                                <label for="doctor_id" class="form-label">Dokter</label>
                                <select class="form-select" wire:model='doctor_id' name="doctor_id" id="doctor_id">
                                    <option selected>Select one</option>
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                    @endforeach
                                </select>
                                @error('doctor_id')
                                    <small class="text-danger fw-bold">{{ $message }}</small>
                                @enderror
                            </div>


                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan
                                <small class="text-danger fs-1">(Opsional)</small>
                            </label>
                            <textarea wire:model='notes' class="form-control" name="notes" id="notes" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                            <div class="col-md align-self-center text-end">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>

                            </div>
                        </div>

                    </form>


                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Daftar Tunggu Pasien
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0 m-0">
                        <table class="table text-center">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Dokter</th>
                                    <th>Pasien</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->appointments as $no => $appointment)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ $appointment->patient->name }}</td>
                                        <td>{{ $appointment->doctor->name }}</td>
                                        <td>
                                            <span
                                                class="badge p-2 bg-warning">{{ __('appointment.' . $appointment->status) }}</span>
                                        </td>
                                        <td>
                                            <a name="" id="" class="btn btn-primary btn-sm" href="#"
                                                role="button">Tindakan</a>
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

</x-app-layout>
