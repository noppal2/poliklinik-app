<x-layouts.app title="Pendaftaran Pemeriksaan">
    <div class="flex min-h-[70vh] items-center justify-center bg-slate-100 px-4 py-8">
        <div class="w-full max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="mb-6 flex items-center justify-center gap-2 text-xl font-bold text-slate-800">
                <i class="fas fa-clipboard-list text-indigo-500"></i>
                Pendaftaran Poli
            </h1>

            <form action="{{ route('pasien.daftar.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="no_rm" class="mb-1 block text-sm font-semibold text-slate-700">Nomor Rekam Medis</label>
                    <input id="no_rm" type="text" value="{{ $pasien->no_rm ?: '-' }}" readonly
                        class="input input-bordered w-full bg-slate-100 text-slate-600">
                </div>

                <div>
                    <label for="id_poli" class="mb-1 block text-sm font-semibold text-slate-700">Pilih Poli</label>
                    <select id="id_poli" name="id_poli"
                        class="select select-bordered w-full @error('id_poli') select-error @enderror" required>
                        <option value="">-- Pilih Poli --</option>
                        @foreach ($polis as $poli)
                            <option value="{{ $poli->id }}" @selected(old('id_poli') == $poli->id)>
                                {{ $poli->nama_poli }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_poli')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($polis->isEmpty())
                        <p class="mt-1 text-sm text-amber-700">Belum ada poli dengan jadwal dokter yang tersedia.</p>
                    @endif
                </div>

                <div>
                    <label for="id_jadwal" class="mb-1 block text-sm font-semibold text-slate-700">Pilih Jadwal Periksa</label>
                    <select id="id_jadwal" name="id_jadwal"
                        class="select select-bordered w-full @error('id_jadwal') select-error @enderror" required disabled>
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach ($jadwals as $jadwal)
                            <option value="{{ $jadwal->id }}" data-poli="{{ $jadwal->dokter->id_poli }}"
                                @selected(old('id_jadwal') == $jadwal->id)>
                                {{ $jadwal->dokter->nama }} — {{ $jadwal->hari }},
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_jadwal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keluhan" class="mb-1 block text-sm font-semibold text-slate-700">Keluhan</label>
                    <textarea id="keluhan" name="keluhan" rows="4" required
                        class="textarea textarea-bordered w-full @error('keluhan') textarea-error @enderror"
                        placeholder="Tulis keluhan Anda...">{{ old('keluhan') }}</textarea>
                    @error('keluhan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" @disabled($jadwals->isEmpty())
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                        Daftar Poli
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const poliSelect = document.getElementById('id_poli');
        const jadwalSelect = document.getElementById('id_jadwal');

        function filterJadwal() {
            const selectedPoli = poliSelect.value;
            let hasAvailableSchedule = false;

            [...jadwalSelect.options].forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const matchesPoli = option.dataset.poli === selectedPoli;
                option.hidden = !matchesPoli;
                hasAvailableSchedule = hasAvailableSchedule || matchesPoli;

                if (!matchesPoli && option.selected) {
                    jadwalSelect.value = '';
                }
            });

            jadwalSelect.disabled = !selectedPoli || !hasAvailableSchedule;
            jadwalSelect.options[0].textContent = !selectedPoli
                ? '-- Pilih Poli Terlebih Dahulu --'
                : hasAvailableSchedule
                    ? '-- Pilih Jadwal --'
                    : '-- Jadwal Belum Tersedia --';
        }

        poliSelect.addEventListener('change', filterJadwal);
        filterJadwal();
    </script>
</x-layouts.app>
