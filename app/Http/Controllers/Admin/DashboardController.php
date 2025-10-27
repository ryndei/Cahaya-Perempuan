<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Kartu/stat ringkas
        $counts = [
            'baru'             => Complaint::where('status', 'submitted')->count(),
            'aktif'            => Complaint::whereIn('status', ['submitted','in_review','follow_up'])->count(),
            'follow_up'        => Complaint::where('status', 'follow_up')->count(),
            // Tanpa kolom closed_at → gunakan updated_at sebagai tanggal selesai
            'selesai_bulan_ini'=> Complaint::where('status', 'like', 'closed%')
                                    ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                                    ->count(),
        ];

        // Tabel "Pengaduan Terbaru" (6 data)
        $recent = Complaint::with('user')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        // Data grafik: tren 30 hari terakhir (mulai 29 hari lalu s/d hari ini)
        $start = now()->startOfDay()->subDays(29);

        // Kunci tanggal harian: ['YYYY-MM-DD', ...]
        $dateKeys = collect(range(0, 29))
            ->map(fn (int $i) => $start->copy()->addDays($i)->toDateString());

        // Hitung jumlah per hari dari created_at
        $raw = Complaint::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereDate('created_at', '>=', $start->toDateString())
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd');  // contoh: ['2025-01-01' => 5, ...]

        // Label & nilai untuk Chart.js (lokalisasi Indonesia: 14 Okt)
        $trendLabels = $dateKeys
            ->map(fn (string $d) => Carbon::parse($d)->locale('id')->isoFormat('D MMM'))
            ->values()
            ->toArray();

        $trendCounts = $dateKeys
            ->map(fn (string $d) => (int) ($raw[$d] ?? 0))
            ->values()
            ->toArray();

        $trendTotal = array_sum($trendCounts);

        return view('dashboard.admin.home', compact(
            'counts', 'recent', 'trendLabels', 'trendCounts', 'trendTotal'
        ));
    }
}
