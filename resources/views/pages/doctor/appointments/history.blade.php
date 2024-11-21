<?php

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Medicalrecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses};

state([
    'medicalHistory' => fn() => Medicalrecord::where('patient_id', $this->patient->id)->get(),
    'patient',
]);

$loadMedicines = function () {
    return Prescription::where('medical_record_id', $this->paymentRecord->medicalRecord->id)->get();
};

?>


@volt
    <div>
        <div class="accordion mb-3" id="accordionExample">
            @foreach ($medicalHistory as $item)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                            <strong>{{ $item->created_at->format('d M Y') }}</strong>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <hr>

                            <h6 class="fw-bolder">
                                Rekam Medis
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Dokter:</strong>
                                    <p>{{ $item->appointment->doctor->name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <strong>Pemeriksaan Fisik:</strong>
                                    <p>{{ $item->physical_exam }}</p>
                                </div>

                                <div class="col-md-6">
                                    <strong>Keluhan Pasien:</strong>
                                    <p>{{ $item->complaint }}</p>
                                </div>

                                <div class="col-md-6">
                                    <strong>Diagnosa Pasien:</strong>
                                    <p>{{ $item->diagnosis }}</p>
                                </div>

                                <div class="col-md-6">
                                    <strong>Saran Perawatan atau Tindakan Lebih Lanjut:</strong>
                                    <p>{{ $item->recommendation }}</p>
                                </div>

                                <div class="col-md-6">
                                    <strong>Tipe Perawatan:</strong>
                                    <p>{{ __('status.' . $item->type) }}</p>
                                </div>

                                @if ($item->type === 'inpatient')
                                    <div class="col-md-6">
                                        <strong>Ruangan:</strong>
                                        <p>Ruang {{ $item->inpatientRecord->room_id }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <strong>Tanggal Masuk - Keluar</strong>
                                        <p>
                                            {{ Carbon\Carbon::parse($item->inpatientRecord->admission_date)->format('d M Y') }}
                                            -
                                            {{ Carbon\Carbon::parse($item->inpatientRecord->discharge_date)->format('d M Y') }}
                                        </p>
                                    </div>

                                    <div class="col-md-12">
                                        <strong>Catatan Dokter:</strong>
                                        <p>{{ $item->doctor_notes ?? '-' }}</p>
                                    </div>
                                @endif
                            </div>

                            <hr>

                            <h6 class="fw-bolder">Resep Obat Dokter</h6>
                            <table class="table table-borderless text-center table-sm">
                                <thead>
                                    <tr class="border">
                                        <th>Nama Obat</th>
                                        <th>Durasi</th>
                                        <th>Frekuensi</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->prescriptions as $no => $prescription)
                                        <tr class="border">
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
                </div>
            @endforeach
        </div>
    </div>
@endvolt
