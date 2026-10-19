<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PoliController extends Controller
{
    public function get()
    {
        $polis = Poli::whereHas('dokters', function ($query) {
            $query->where('role', 'dokter')
                ->whereHas('jadwalPeriksas');
        })
            ->orderBy('nama_poli')
            ->get();

        $jadwals = JadwalPeriksa::with(['dokter.poli'])
            ->whereHas('dokter', function ($query) {
                $query->where('role', 'dokter')
                    ->whereNotNull('id_poli');
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('pasien.daftar', [
            'polis' => $polis,
            'jadwals' => $jadwals,
            'pasien' => Auth::user(),
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'id_poli' => ['required', 'integer', 'exists:poli,id'],
            'id_jadwal' => ['required', 'integer', 'exists:jadwal_periksa,id'],
            'keluhan' => ['required', 'string'],
        ]);

        $jadwal = JadwalPeriksa::whereKey($validated['id_jadwal'])
            ->whereHas('dokter', function ($query) use ($validated) {
                $query->where('role', 'dokter')
                    ->where('id_poli', $validated['id_poli']);
            })
            ->first();

        if (! $jadwal) {
            throw ValidationException::withMessages([
                'id_jadwal' => 'Jadwal tidak sesuai dengan poli yang dipilih.',
            ]);
        }

        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $validated['id_jadwal'])->count();

        DaftarPoli::create([
            'id_pasien' => Auth::id(),
            'id_jadwal' => $validated['id_jadwal'],
            'keluhan' => $validated['keluhan'],
            'no_antrian' => $jumlahSudahDaftar + 1,
        ]);

        return redirect()
            ->route('pasien.daftar')
            ->with('success', 'Berhasil mendaftar ke poli.');
    }
}