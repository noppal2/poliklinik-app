<x-layouts.app title="Form Pemeriksaan Pasien">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('periksa-pasien.index') }}"
            class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-slate-200">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Pemeriksaan Pasien</h1>
            <p class="text-sm text-slate-500">Antrean {{ $daftarPoli->no_antrian }}</p>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold text-slate-800">Informasi Pasien</h2>
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-400">Nama</dt>
                <dd class="mt-1 text-slate-700">{{ $daftarPoli->pasien->nama }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-400">Nomor Rekam Medis</dt>
                <dd class="mt-1 text-slate-700">{{ $daftarPoli->pasien->no_rm ?: '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase text-slate-400">Keluhan</dt>
                <dd class="mt-1 whitespace-pre-line text-slate-700">{{ $daftarPoli->keluhan }}</dd>
            </div>
        </dl>
    </div>

    <form action="{{ route('periksa-pasien.store') }}" method="POST"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <input type="hidden" name="id_daftar_poli" value="{{ $daftarPoli->id }}">

        <div class="mb-6">
            <label for="catatan" class="mb-2 block text-sm font-semibold text-slate-700">Catatan Pemeriksaan</label>
            <textarea id="catatan" name="catatan" rows="4"
                class="textarea textarea-bordered w-full @error('catatan') textarea-error @enderror"
                placeholder="Masukkan hasil pemeriksaan atau diagnosis">{{ old('catatan') }}</textarea>
            @error('catatan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <fieldset class="mb-6">
            <legend class="mb-3 text-sm font-semibold text-slate-700">Obat</legend>
            @error('obat_ids')
                <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('obat_ids.*')
                <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200">
                @forelse ($obats as $obat)
                    <label class="flex cursor-pointer items-center justify-between gap-4 p-4 hover:bg-slate-50">
                        <span class="flex items-start gap-3">
                            <input type="checkbox" name="obat_ids[]" value="{{ $obat->id }}"
                                data-harga="{{ $obat->harga }}"
                                class="checkbox checkbox-primary mt-0.5"
                                {{ in_array($obat->id, old('obat_ids', [])) ? 'checked' : '' }}>
                            <span>
                                <span class="block font-medium text-slate-700">{{ $obat->nama_obat }}</span>
                                @if ($obat->kemasan)
                                    <span class="text-xs text-slate-500">{{ $obat->kemasan }}</span>
                                @endif
                            </span>
                        </span>
                        <span class="shrink-0 text-sm text-slate-600">Rp{{ number_format($obat->harga, 0, ',', '.') }}</span>
                    </label>
                @empty
                    <p class="p-4 text-sm text-slate-500">Belum ada data obat. Pemeriksaan tetap dapat disimpan tanpa obat.</p>
                @endforelse
            </div>
        </fieldset>

        <div class="mb-6 flex items-center justify-between rounded-xl bg-slate-50 p-4">
            <span class="font-semibold text-slate-700">Total biaya (termasuk konsultasi Rp150.000)</span>
            <span id="totalBiaya" class="font-bold text-slate-800">Rp150.000</span>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit"
                class="flex items-center gap-2 rounded-lg bg-[#2d4499] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#1e2d6b]">
                <i class="fas fa-save"></i>
                Simpan Pemeriksaan
            </button>
            <a href="{{ route('periksa-pasien.index') }}"
                class="btn btn-ghost rounded-lg bg-slate-100 px-6 text-slate-600 hover:bg-slate-200">
                Batal
            </a>
        </div>
    </form>

    <script>
        const consultationFee = 150000;
        const totalElement = document.getElementById('totalBiaya');

        function updateTotal() {
            const medicineTotal = [...document.querySelectorAll('input[name="obat_ids[]"]:checked')]
                .reduce((total, input) => total + Number(input.dataset.harga), 0);

            totalElement.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(consultationFee + medicineTotal);
        }

        document.querySelectorAll('input[name="obat_ids[]"]').forEach((input) => {
            input.addEventListener('change', updateTotal);
        });

        updateTotal();
    </script>
</x-layouts.app>
