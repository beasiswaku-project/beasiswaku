<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PencocokanController extends Controller
{
    /**
     * Recommend open programs that match the student's current profile
     * (proposal subbab 4.6.3): programs for another level are skipped,
     * programs failing a mandatory requirement are hidden, and the rest are
     * sorted by match percentage, then by nearest deadline, with programs
     * without non-document requirements ("terbuka umum") last.
     */
    public function __invoke(Request $request): View
    {
        $profil = $request->user()->mahasiswa?->load(['wilayah', 'riwayatPendidikan']);

        $rekomendasi = collect();

        if ($profil) {
            $rekomendasi = Program::query()
                ->terbuka()
                ->where('jenjang_id', $profil->jenjang_id)
                ->with(['penyedia', 'kategori', 'jenjang', 'persyaratan'])
                ->get()
                ->map(fn (Program $program): array => ['program' => $program, ...$program->hasilPencocokan($profil)])
                ->filter(fn (array $hasil): bool => $hasil['memenuhi'])
                ->sortBy([
                    fn (array $a, array $b): int => ($a['persentase'] === null) <=> ($b['persentase'] === null),
                    fn (array $a, array $b): int => ($b['persentase'] ?? 0) <=> ($a['persentase'] ?? 0),
                    fn (array $a, array $b): int => $a['program']->deadline <=> $b['program']->deadline,
                ])
                ->values();
        }

        return view('mahasiswa.beasiswa.pencocokan', [
            'profil' => $profil,
            'rekomendasi' => $rekomendasi,
        ]);
    }
}
