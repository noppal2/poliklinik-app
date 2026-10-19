<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PeriksaPasienController extends Controller
{
    private const BIAYA_KONSULTASI = 150000;

    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        $daftarPoli = $this->findDoctorRegistration($id);

        if ($daftarPoli->periksas()->exists()) {
            return redirect()
                ->route('periksa-pasien.index')
                ->with('error', 'Pasien ini sudah diperiksa.');
        }

        $obats = Obat::orderBy('nama_obat')->get();

        return view('dokter.periksa-pasien.create', compact('daftarPoli', 'obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_daftar_poli' => ['required', 'integer', 'exists:daftar_poli,id'],
            'obat_ids' => ['sometimes', 'array'],
            'obat_ids.*' => ['integer', 'distinct', 'exists:obat,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $daftarPoli = $this->findDoctorRegistration($validated['id_daftar_poli'], true);

            if ($daftarPoli->periksas()->exists()) {
                throw ValidationException::withMessages([
                    'id_daftar_poli' => 'Pasien ini sudah diperiksa.',
                ]);
            }

            $obatIds = array_values($validated['obat_ids'] ?? []);
            $totalObat = $obatIds === []
                ? 0
                : Obat::whereIn('id', $obatIds)->sum('harga');

            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPoli->id,
                'tgl_periksa' => now(),
                'catatan' => $validated['catatan'] ?? null,
                'biaya_periksa' => self::BIAYA_KONSULTASI + $totalObat,
            ]);

            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);
            }
        });

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }

    private function findDoctorRegistration(int|string $id, bool $lock = false): DaftarPoli
    {
        $query = DaftarPoli::whereKey($id)
            ->whereHas('jadwalPeriksa', function ($query) {
                $query->where('id_dokter', Auth::id());
            });

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->firstOrFail();
    }
}