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
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $no => $prescription)
                    <tr class="border">
                        <td>{{ ++$no }}</td>
                        <td>{{ $prescription->medication->name }}</td>
                        <td>{{ $prescription->duration }}</td>
                        <td>{{ $prescription->frequency }}</td>
                        <td>{{ $prescription->quantity }}</td>
                        <td>X {{ formatRupiah($prescription->medication->price) }}</td>
                        <td>
                            {{ formatRupiah($prescription->medication->price * $prescription->quantity) }}
                        </td>
                    </tr>
                @endforeach

                <!-- Total Biaya Obat -->
                <tr class="text-end">
                    <td colspan="5"></td>
                    <td class="text-center fw-bolder">Sub Total Obat:</td>
                    <td class="fw-bolder text-dark">
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
