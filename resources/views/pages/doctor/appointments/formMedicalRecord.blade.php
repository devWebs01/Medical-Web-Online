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

            @error('room_id')
                <p id="room_id" class="form-text text-danger">{{ $message }}</p>
            @enderror

            <div class="row">
                @foreach ($rooms as $room)
                    <div class="col-md">
                        <div class="form-check px-0">
                            <label class="form-check-label card" for="flexRadioDefault{{ $room->room_number }}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto px-5">
                                            <!-- Set nilai input radio sesuai ID room dan bind dengan Livewire -->
                                            <input class="form-check-input p-3 border border-primary" type="radio"
                                                wire:model="room_id" name="room_id" value="{{ $room->id }}"
                                                id="flexRadioDefault{{ $room->room_number }}"
                                                {{ $room_id == $room->id ? 'checked' : '' }}
                                                {{ $room->availability == 'occupied' ? 'disabled' : '' }}>
                                        </div>
                                        <div class="col-auto">
                                            <span
                                                class="badge {{ $room->availability !== 'occupied' ? 'bg-primary' : 'bg-danger' }}">
                                                {{ $room->availability !== 'occupied' ? 'Tersedia' : 'Tidak Tersedia' }}

                                            </span>
                                            <h4 class="fw-bolder">
                                                Kamar {{ $room->room_number }}
                                            </h4>
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

    @if (optional($medicalRecord)->paymentRecord == null)
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
    @endif
</form>
