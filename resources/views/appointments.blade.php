<div class="card">
    <div class="card-header">
        Daftar Tunggu Pasien
    </div>
    <div class="card-body">
        <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-todayAppointments-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-todayAppointments" type="button" role="tab"
                    aria-controls="pills-todayAppointments" aria-selected="true">Menunggu</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-completedAppointments-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-completedAppointments" type="button" role="tab"
                    aria-controls="pills-completedAppointments" aria-selected="false">Selesai</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-canceledAppointments-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-canceledAppointments" type="button" role="tab"
                    aria-controls="pills-canceledAppointments" aria-selected="false">Dibatalkan</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">

            <!-- Tab untuk Antrian Hari Ini/Menunggu -->
            <div class="tab-pane fade show active" id="pills-todayAppointments" role="tabpanel"
                aria-labelledby="pills-todayAppointments-tab">
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
                                @foreach ($this->appointments->todayAppointments as $no => $appointment)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ $appointment->doctor->name }}</td>
                                        <td>{{ $appointment->patient->name }}</td>
                                        <td>
                                            <span
                                                class="badge p-2 bg-warning">{{ __('status.' . $appointment->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-3 justify-content-center">
                                                <a class="btn btn-primary btn-sm
                                            {{ $role === 'doctor' || $role === 'owner' ? '' : 'd-none' }}
                                             "
                                                    href="{{ route('appointments.patient', ['appointment' => $appointment->id]) }}"
                                                    role="button">Tindakan</a>
                                                <button wire:loading.attr='disabled'
                                                    wire:click='cancelAppointment({{ $appointment->id }})'
                                                    wire:confirm="Apakah kamu yakin ingin membatalkan Antrian ini?"
                                                    class="btn btn-sm btn-danger {{ $role === 'admin' ?: 'd-none' }} {{ $appointment->status === 'waiting' ? '' : 'd-none' }}">
                                                    Batalkan
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab untuk Antrian Selesai/Sudah Berobat -->
            <div class="tab-pane fade" id="pills-completedAppointments" role="tabpanel"
                aria-labelledby="pills-completedAppointments-tab">
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
                                @foreach ($this->appointments->completedAppointments as $no => $appointment)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ $appointment->doctor->name }}</td>
                                        <td>{{ $appointment->patient->name }}</td>
                                        <td>
                                            <span
                                                class="badge p-2 bg-success">{{ __('status.' . $appointment->status) }}</span>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('appointments.patient', ['appointment' => $appointment->id]) }}"
                                                role="button">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab untuk Antrian dibatalkan/Tidak hadir -->
            <div class="tab-pane fade" id="pills-canceledAppointments" role="tabpanel"
                aria-labelledby="pills-canceledAppointments-tab">
                <div class="card-body">
                    <div class="table-responsive p-0 m-0">
                        <table class="table text-center">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Dokter</th>
                                    <th>Pasien</th>
                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->appointments->canceledAppointments as $no => $appointment)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ $appointment->doctor->name }}</td>
                                        <td>{{ $appointment->patient->name }}</td>
                                        <td>
                                            <span
                                                class="badge p-2 bg-danger">{{ __('status.' . $appointment->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
