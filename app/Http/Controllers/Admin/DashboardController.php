<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        
        $counts = [
            'baru' => Complaint::where('status', 'submitted')->count(),
            'aktif' => Complaint::whereIn('status', ['submitted','in_review','follow_up'])->count(),
            'follow_up' => Complaint::where('status', 'follow_up')->count(),
            'selesai_bulan_ini' => Complaint::where('status','like', 'closed%')
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        // Tabel "Pengaduan Terbaru"
        $recent = Complaint::with('user')->latest()->take(6)->get();

        // Data grafik: tren 30 hari terakhir
        $start = now()->subDays(29)->startOfDay();
        $dateKeys = collect(range(0, 29))->map(
            fn ($i) => $start->copy()->addDays($i)->toDateString()
        );

        $raw = Complaint::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd'); // ['2025-01-01' => 5, ...]

        $trendLabels = $dateKeys
            ->map(fn ($d) => Carbon::parse($d)->format('d M'))
            ->values()
            ->toArray();

        $trendCounts = $dateKeys
            ->map(fn ($d) => (int)($raw[$d] ?? 0))
            ->values()
            ->toArray();

        return view('dashboard.admin.home', compact('counts', 'recent', 'trendLabels', 'trendCounts'));
    }
}
