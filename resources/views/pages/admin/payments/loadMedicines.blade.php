<div>
    <div class="table-responsive pt-0">
        <h6 class="fw-bolder mb-3">Resep Obat Dokter</h6>
        <table class="table table-borderless text-center table-sm">
            <thead>
                <tr class="border">
                    <th class="text-start">Nama Obat</th>
                    <th>Durasi</th>
                    <th>Frekuensi</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $no => $prescription)
                    <tr class="border">
                        <td class="text-start">{{ $prescription->medication->name }}</td>
                        <td>{{ $prescription->duration }}</td>
                        <td>{{ $prescription->frequency }}</td>
                        <td>{{ $prescription->quantity }}</td>
                        <td>X {{ formatRupiah($prescription->medication->price) }}</td>
                        <td class="text-end">
                            {{ formatRupiah($prescription->medication->price * $prescription->quantity) }}
                        </td>
                    </tr>
                @endforeach

                <!-- Total Biaya Obat -->
                <tr class="text-end">
                    <td colspan="4"></td>
                    <td class="text-center fw-bolder">Sub Total Obat:</td>
                    <td class="fw-bolder text-dark text-end">
                        {{ formatRupiah(
                            $medicines->sum(function ($prescription) {
                                return $prescription->medication->price * $prescription->quantity;
                            }),
                        ) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
