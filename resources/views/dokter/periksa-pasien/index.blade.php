<x-layouts.app title="Periksa Pasien">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Periksa Pasien</h1>
        <p class="mt-1 text-sm text-slate-500">Daftar pasien yang terdaftar pada jadwal praktik Anda.</p>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="table w-full">
            <thead class="bg-slate-100 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-4">No. Antrean</th>
                    <th class="px-5 py-4">Pasien</th>
                    <th class="px-5 py-4">Jadwal</th>
                    <th class="px-5 py-4">Keluhan</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPasien as $daftar)
                    @php($sudahDiperiksa = $daftar->periksas->isNotEmpty())
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-semibold text-slate-700">{{ $daftar->no_antrian }}</td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-800">{{ $daftar->pasien->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $daftar->pasien->no_rm }}</div>
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $daftar->jadwalPeriksa->hari }}
                            {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i') }}
                            –
                            {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_selesai)->format('H:i') }}
                        </td>
                        <td class="max-w-xs whitespace-normal px-5 py-4 text-slate-600">{{ $daftar->keluhan }}</td>
                        <td class="px-5 py-4">
                            @if ($sudahDiperiksa)
                                <span class="badge badge-success badge-outline">Sudah diperiksa</span>
                            @else
                                <span class="badge badge-warning badge-outline">Menunggu</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($sudahDiperiksa)
                                <span class="text-sm text-slate-400">Selesai</span>
                            @else
                                <a href="{{ route('periksa-pasien.create', $daftar->id) }}"
                                    class="btn btn-sm border-none bg-[#2d4499] text-white hover:bg-[#1e2d6b]">
                                    <i class="fas fa-notes-medical"></i>
                                    Periksa
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center text-slate-400">
                            Belum ada pasien terdaftar pada jadwal praktik Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
