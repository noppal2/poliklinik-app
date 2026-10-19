<x-layouts.app title="Detail Riwayat Pemeriksaan">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('riwayat-pasien.index') }}"
            class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-slate-200"
            aria-label="Kembali ke riwayat pasien">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Detail Riwayat Pemeriksaan</h1>
            <p class="text-sm text-slate-500">Nomor antrean {{ $periksa->daftarPoli->no_antrian }}</p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-slate-800">Informasi Pasien</h2>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Nama Pasien</dt>
                    <dd class="mt-1 text-slate-700">{{ $periksa->daftarPoli->pasien->nama }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Nomor Rekam Medis</dt>
                    <dd class="mt-1 text-slate-700">{{ $periksa->daftarPoli->pasien->no_rm ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Nomor Antrean</dt>
                    <dd class="mt-1 text-slate-700">{{ $periksa->daftarPoli->no_antrian }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Tanggal Periksa</dt>
                    <dd class="mt-1 text-slate-700">
                        {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-slate-800">Informasi Poli</h2>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Poli</dt>
                    <dd class="mt-1 text-slate-700">
                        {{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Dokter</dt>
                    <dd class="mt-1 text-slate-700">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Jadwal</dt>
                    <dd class="mt-1 text-slate-700">
                        {{ $periksa->daftarPoli->jadwalPeriksa->hari }},
                        {{ \Carbon\Carbon::parse($periksa->daftarPoli->jadwalPeriksa->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($periksa->daftarPoli->jadwalPeriksa->jam_selesai)->format('H:i') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-400">Biaya Pemeriksaan</dt>
                    <dd class="mt-1 font-semibold text-slate-700">
                        Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-3 font-semibold text-slate-800">Keluhan</h2>
            <p class="whitespace-pre-line text-sm leading-6 text-slate-600">
                {{ $periksa->daftarPoli->keluhan ?: '-' }}
            </p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-3 font-semibold text-slate-800">Catatan Pemeriksaan</h2>
            <p class="whitespace-pre-line text-sm leading-6 text-slate-600">
                {{ $periksa->catatan ?: '-' }}
            </p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-4 font-semibold text-slate-800">Obat yang Diberikan</h2>
            <div class="divide-y divide-slate-100">
                @forelse ($periksa->detailPeriksas as $detail)
                    <div class="flex flex-wrap items-center justify-between gap-2 py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-medium text-slate-700">{{ $detail->obat->nama_obat }}</p>
                            @if ($detail->obat->kemasan)
                                <p class="text-sm text-slate-500">{{ $detail->obat->kemasan }}</p>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600">
                            Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Tidak ada obat yang diberikan.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
