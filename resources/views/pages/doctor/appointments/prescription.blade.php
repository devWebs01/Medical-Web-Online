<?php

use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PaymentRecord;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, mount, on};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('appointments.prescription');

state([
    'medicines' => [], // Menyimpan daftar obat
    'role' => fn() => Auth::user()->role,
    'medical_record_id' => fn() => $this->medicalRecord->id ?? '',
    'medications' => fn() => Medication::get(),
    'medicalRecord',
]);

rules([
    'medicines.*.medicine_id' => 'required|exists:medications,id', // Validasi untuk medicine_id
    'medicines.*.quantity' => 'required|string',
    'medicines.*.frequency' => 'required|string',
    'medicines.*.duration' => 'required|string',
]);

mount(function ($medicalRecord) {
    // Pastikan $medicalRecord terisi dan memiliki ID sebelum mengakses property-nya
    if ($medicalRecord && $medicalRecord->id) {
        $this->medicalRecord = $medicalRecord;
        $this->medicines = Prescription::where('medical_record_id', $medicalRecord->id)
            ->get()
            ->toArray();
    } else {
        $this->medicalRecord = null;
        $this->medicines = [];
        $this->alert('warning', 'Lengkap Data Rekam Medis!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
});

$saveMedicalRecord = function () {
    if (!$this->medicalRecord || !$this->medicalRecord->id) {
        $this->alert('error', 'Rekam medis tidak ditemukan!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    $this->validate();

    // Panggil fungsi untuk menyimpan resep
    $this->storePrescriptions();

    // Panggil fungsi untuk menangani perubahan tipe pasien
    $this->handlePaymentOnTypeChange();

    // Hitung total biaya obat
    $totalAmount = $this->calculateTotalAmount();

    // Simpan pembayaran hanya jika rawat jalan
    if ($this->medicalRecord->type === 'outpatient') {
        $this->createPaymentRecord($totalAmount);
        $this->medicalRecord->update(['status' => 'completed']);

        $this->alert('success', 'Resep dan pembayaran berhasil disimpan!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    } else {
        // Jika rawat inap, status menjadi 'follow-up'
        $this->medicalRecord->update(['status' => 'follow-up']);

        $this->alert('success', 'Resep berhasil disimpan untuk pasien rawat inap!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    // Ambil kembali data resep terbaru
    $this->medicines = Prescription::where('medical_record_id', $this->medicalRecord->id)
        ->get()
        ->toArray();

    // Redirect ke halaman daftar rekam medis
    $this->redirectRoute('medicalRecords.index');
};

$storePrescriptions = function () {
    if (!$this->medicalRecord || !$this->medical_record_id) {
        $this->alert('error', 'Rekam medis tidak ditemukan!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    try {
        // Hapus semua resep yang ada untuk medical_record_id ini
        Prescription::where('medical_record_id', $this->medicalRecord->id)->delete();

        // Simpan resep baru
        foreach ($this->medicines as $medicine) {
            if (isset($medicine['medicine_id'])) {
                Prescription::create([
                    'medical_record_id' => $this->medicalRecord->id,
                    'medicine_id' => $medicine['medicine_id'],
                    'quantity' => $medicine['quantity'],
                    'frequency' => $medicine['frequency'],
                    'duration' => $medicine['duration'],
                ]);
            }
        }
    } catch (\Exception $e) {
        $this->alert('error', 'Gagal menyimpan resep: ' . $e->getMessage(), [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

$calculateTotalAmount = function () {
    $totalAmount = 0;

    foreach ($this->medicines as $medicine) {
        $medication = Medication::find($medicine['medicine_id']);
        if ($medication) {
            $totalAmount += $medication->price * $medicine['quantity'];
        }
    }

    return $totalAmount;
};

$createPaymentRecord = function ($totalAmount) {
    if (!$this->medicalRecord || !$this->medicalRecord->id) {
        $this->alert('error', 'Rekam medis tidak ditemukan!', [
            'position' => 'top',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    // Cek apakah sudah ada entri pembayaran untuk rekam medis ini
    $payment = PaymentRecord::where('medical_record_id', $this->medicalRecord->id)->first();

    if ($payment) {
        // Jika sudah ada, update total amount dan tanggal pembayaran
        $payment->update([
            'total_amount' => $totalAmount,
            'payment_date' => now(),
        ]);
    } else {
        // Jika belum ada, buat entri baru
        $payment = PaymentRecord::create([
            'medical_record_id' => $this->medicalRecord->id,
            'total_amount' => $totalAmount,
            'payment_date' => now(),
        ]);
    }

    // Mengaitkan obat-obatan dengan entri pembayaran
    foreach ($this->medicines as $medicine) {
        $medication = Medication::find($medicine['medicine_id']);
        if ($medication) {
            $payment->medications()->attach($medicine['medicine_id'], [
                'quantity' => $medicine['quantity'],
                'price' => $medication->price,
            ]);
        }
    }
};

$handlePaymentOnTypeChange = function () {
    if (!$this->medicalRecord || !$this->medicalRecord->id) {
        return;
    }

    // Cek apakah sudah ada entri pembayaran untuk rekam medis ini
    $payment = PaymentRecord::where('medical_record_id', $this->medicalRecord->id)->first();

    if ($payment) {
        // Jika tipe pasien berubah menjadi rawat inap, hapus pembayaran
        if ($this->medicalRecord->type === 'inpatient') {
            $payment->medications()->detach();
            $payment->delete();
        } else {
            // Jika tetap rawat jalan, perbarui pembayaran
            $totalAmount = $this->calculateTotalAmount();
            $payment->update([
                'total_amount' => $totalAmount,
                'payment_date' => now(),
            ]);
        }
    }
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

                    @if (optional($medicalRecord)->paymentRecord == null)
                        <button type="button" wire:click="addMedicine" role="button" class="btn btn-outline-primary">
                            Tambah Obat
                        </button>
                    @endif
                </div>
                <div class="card-body">

                    @if (optional($medicalRecord)->paymentRecord == null)
                        <form wire:submit.prevent="saveMedicalRecord">
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
                                            <input type="number"
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
                                    <button type="submit"
                                        class="btn btn-primary
                                {{ $role === 'doctor' || $role === 'owner' ? '' : 'd-none' }}
                                 ">
                                        Submit
                                    </button>
                                </div>
                                <div class="col-auto align-self-center text-end">
                                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                                </div>
                            </div>
                        </form>
                    @else
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

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($medicalRecord->prescriptions as $no => $prescription)
                                            <tr class="border">
                                                <td>{{ ++$no }}</td>
                                                <td>{{ $prescription->medication->name }}</td>
                                                <td>{{ $prescription->duration }}</td>
                                                <td>{{ $prescription->frequency }}</td>
                                                <td>{{ $prescription->quantity }}</td>

                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endvolt
</div>
