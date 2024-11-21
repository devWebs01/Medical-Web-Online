<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\InpatientRecord;
use App\Models\Appointment;
use function Livewire\Volt\{state, uses, rules, computed};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

state([
    'doctors' => fn() => User::where('role', 'doctor')->orWhere('role', 'owner')->get(),
    'date' => fn() => now(),
    'patient_id',
    'doctor_id',
    'notes',
    'role' => fn() => Auth()->user()->role,
    'inpatientCount' => fn() => InpatientRecord::where('status', 'active')->count(),
    'appointmentCount' => fn() => Appointment::where('status', 'waiting')->whereDate('date', now()->toDateString())->count(),
]);

rules([
    'patient_id' => 'required|exists:patients,id',
    'doctor_id' => 'required|exists:users,id',
    'date' => 'required|date',
    'notes' => 'nullable',
]);

$appointments = computed(function () {
    // Ambil role pengguna saat ini
    $userRole = $this->role;

    // Ambil semua Antrian hari ini
    $allAppointments = Appointment::whereDate('date', now()->toDateString())->orderBy('date', 'desc');

    // Jika pengguna adalah dokter, filter Antrian berdasarkan dokter
    if ($userRole === 'doctor') {
        $allAppointments = $allAppointments->where('doctor_id', auth()->user()->id);
        $allAppointmentsCount = $allAppointments->where('doctor_id', auth()->user()->id)->count();
    }

    // Ambil semua Antrian sesuai filter
    $allAppointments = $allAppointments->get();

    // Filter Antrian berdasarkan status
    $todayAppointments = $allAppointments->filter(function ($appointment) {
        return $appointment->status === 'waiting'; // Adjust if needed
    });

    $completedAppointments = $allAppointments->filter(function ($appointment) {
        return $appointment->status === 'checked-in';
    });

    $canceledAppointments = $allAppointments->filter(function ($appointment) {
        return $appointment->status === 'canceled';
    });

    return (object) [
        'allAppointmentsCount' => $allAppointmentsCount ?? '0',
        'todayAppointments' => $todayAppointments,
        'completedAppointments' => $completedAppointments,
        'canceledAppointments' => $canceledAppointments,
    ];
});

$patients = computed(function () {
    return Patient::whereDoesntHave('appointments', function ($query) {
        $query->where('status', 'waiting')->whereDate('date', Carbon::today());
    })->get();
});

$createAppointment = function () {
    $validateData = $this->validate();

    Appointment::create($validateData);

    $this->reset('patient_id', 'doctor_id', 'notes');

    $this->alert('success', 'Antrian berhasil dibuat!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);
};

$cancelAppointment = function (appointment $appointment) {
    $appointment->update(['status' => 'canceled']);

    $this->alert('error', 'Antrian telah dibatalkan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);
};

?>

<x-app-layout>

    @volt
        <div>
            <div class="row gx-3">
                <div class="col-xxl-12 col-sm-12">
                    <div class="card mb-3">
                        <div class="card-body text-white rounded-3"
                            style="
                            background-image: url('https://bootstrapget.com/demos/medflex-medical-admin-template/assets/images/banner.svg');
                            background-size: cover;
                            background-position: right;
                            ">
                            <div class="py-4 px-3 text-white">
                                <h6 class="fw-bold text-white">Hello,</h6>
                                <h2 class="text-white">{{ Auth()->User()->name }}</h2>
                                <h5 class="text-white">Jadwal Anda hari ini.</h5>
                                <div class="mt-4 d-flex gap-3">
                                    @if ($role == 'doctor')
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-primary rounded-3 me-3">
                                                <i class='bx bx-universal-access fs-6 p-2'></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <h2 class="m-0 lh-1 fw-bolder text-white">
                                                    {{ $this->appointments->allAppointmentsCount ?? '0' }}
                                                </h2>
                                                <p class="m-0">Pasien</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-primary rounded-3 me-3">
                                            <i class='bx bxs-user-rectangle fs-6 p-2'></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h2 class="m-0 lh-1 fw-bolder text-white">
                                                {{ $appointmentCount ?? '0' }}
                                            </h2>
                                            <p class="m-0">Antrian</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-primary rounded-3 me-3">
                                            <i class='bx bxs-bed fs-6 p-2'></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h2 class="m-0 lh-1 fw-bolder text-white">
                                                {{ $inpatientCount ?? '0' }}
                                            </h2>
                                            <p class="m-0">Rawat Inap</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($role == 'admin')
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md">
                                <span class="fw-bold">Antrian Pasien</span>
                            </div>
                            <div class="col-md text-md-end">
                                <a class="btn btn-primary btn-sm" href="{{ route('patients.create') }}"
                                    role="button">Tambah
                                    Pasien</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="createAppointment" wire:submit='createAppointment' method="post">
                            @csrf

                            <div class="d-none mb-3">
                                <label for="date" class="form-label">Tanggal</label>
                                <input type="dateTime-local" class="form-control" name="date" id="date"
                                    wire:model='date' aria-describedby="Id" placeholder="date" />
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
            @endif

            <div wire:poll.5s>
                @include('appointments')
            </div>
        </div>
    @endvolt

</x-app-layout>
