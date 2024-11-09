<div>
    <div class="card-body table-responsive pt-0">
        <h6 class="fw-bolder mb-3">Daftar Obat</h6>
        <table class="table table-borderless">
            <thead>
                <tr class="border">
                    <th class="text-center">#</th>
                    <th>Nama Obat</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $no => $prescription)
                    <tr class="border">
                        <td class="text-center">{{ ++$no }}</td>
                        <td>{{ $prescription->medication->name }}</td>
                        <td class="text-center">{{ $prescription->quantity }}</td>
                        <td class="text-end">{{ number_format($prescription->medication->price, 2) }}</td>
                        <td class="text-end">
                            {{ number_format($prescription->medication->price * $prescription->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach

                <!-- Total Biaya Obat -->
                <tr class="text-end">
                    <td colspan="3"></td>
                    <td class="text-center fw-bolder">Sub Total Obat:</td>
                    <td class="fw-bolder text-dark">
                        {{ number_format(
                            $medicines->sum(function ($prescription) {
                                return $prescription->medication->price * $prescription->quantity;
                            }),
                            2,
                        ) }}
                    </td>
                </tr>

                <!-- Total Biaya Keseluruhan -->
                <tr class="text-end">
                    <td colspan="3"></td>
                    <td class="text-center fw-bolder">Total Biaya:</td>
                    <td class="fw-bolder text-dark">
                        {{ number_format($totalCost, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="card-footer">
        <div class="col-12">
            <span class="fw-medium text-heading">Note:</span>
            <span>{{ $medicalRecord->note ?? '-' }}</span>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary rounded" wire:click="confirmPayment">Konfirmasi
                Pembayaran</button>
        </div>
    </div>
</div>
