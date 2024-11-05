<?php

use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Prescription;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('medicalRecords.prescription');

state(['medicalRecord']);

state([
    'medicines' => [], // Menyimpan daftar obat
    'medical_record_id' => fn() => $this->medicalRecord->id ?? '',
    'medications' => fn() => Medication::get(),
]);

rules([
    'medicines.*.medicine_id' => 'required|exists:medications,id', // Validasi untuk medicine_id
    'medicines.*.quantity' => 'required|string',
    'medicines.*.frequency' => 'required|string',
    'medicines.*.duration' => 'required|string',
]);

$savePrescription = function () {
    $this->validate();

    foreach ($this->medicines as $medicine) {
        Prescription::create([
            'medical_record_id' => $this->medicalRecord->id,
            'medicine_id' => $medicine['medicine_id'],
            'quantity' => $medicine['quantity'],
            'frequency' => $medicine['frequency'],
            'duration' => $medicine['duration'],
        ]);
    }

    $this->alert('success', 'Resep berhasil disimpan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    // $this->reset('medicines'); // Mengosongkan form setelah penyimpanan
};

$addMedicine = function () {
    $this->medicines[] = [
        'medicine_id' => '',
        'quantity' => '',
        'frequency' => '',
        'duration' => '',
    ];
};

$removeMedicine = function ($index) {
    array_splice($this->medicines, $index, 1);
};

?>

<div>

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Pengobatan dan Resep Obat</strong>
                    </div>
                    <button type="button" wire:click="addMedicine" role="button" class="btn btn-outline-primary">
                        Tambah Obat
                    </button>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="savePrescription">
                        @csrf

                        @foreach ($medicines as $index => $medicine)
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="medicine_id_{{ $index }}" class="form-label">Obat</label>
                                        <select
                                            class="form-select @error('medicines.' . $index . '.medicine_id') is-invalid @enderror"
                                            wire:model="medicines.{{ $index }}.medicine_id"
                                            id="medicine_id_{{ $index }}">
                                            <option value="">Pilih Obat</option>
                                            @foreach ($medications as $medication)
                                                <option value="{{ $medication->id }}">{{ $medication->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('medicines.' . $index . '.medicine_id')
                                            <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="quantity_{{ $index }}" class="form-label">Jumlah Obat</label>
                                        <input type="text"
                                            class="form-control @error('medicines.' . $index . '.quantity') is-invalid @enderror"
                                            wire:model="medicines.{{ $index }}.quantity"
                                            id="quantity_{{ $index }}" />
                                        @error('medicines.' . $index . '.quantity')
                                            <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="frequency_{{ $index }}" class="form-label">Frekuensi</label>
                                        <input type="text"
                                            class="form-control @error('medicines.' . $index . '.frequency') is-invalid @enderror"
                                            wire:model="medicines.{{ $index }}.frequency"
                                            id="frequency_{{ $index }}" />
                                        @error('medicines.' . $index . '.frequency')
                                            <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="duration_{{ $index }}" class="form-label">Durasi</label>
                                        <input type="text"
                                            class="form-control @error('medicines.' . $index . '.duration') is-invalid @enderror"
                                            wire:model="medicines.{{ $index }}.duration"
                                            id="duration_{{ $index }}" />
                                        @error('medicines.' . $index . '.duration')
                                            <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <p class="form-label text-white">Hapus</p>

                                        <button type="button" wire:click="removeMedicine({{ $index }})"
                                            class="btn btn-danger">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="row mb-3">
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    Simpan Resep
                                </button>
                            </div>
                            <div class="col-auto align-self-center text-end">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endvolt
</div>
