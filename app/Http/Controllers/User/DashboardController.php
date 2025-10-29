<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $meId = Auth::id();

        // Ringkasan
        $counts = [
            'baru'   => Complaint::where('user_id', $meId)->where('status', 'submitted')->count(),
            'aktif'  => Complaint::where('user_id', $meId)->whereIn('status', ['submitted','in_review','follow_up'])->count(),
            'follow_up' => Complaint::where('user_id', $meId)->where('status', 'follow_up')->count(),
            'selesai_bulan_ini' => Complaint::where('user_id', $meId)
                ->where('status', 'like', 'closed%')
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        // Pengaduan terakhir user
        $last = Complaint::with('user')->where('user_id', $meId)->latest()->first();

        // Label & kelas badge status
        $statusLabels = Complaint::statusLabels();
        $statusClasses = [
            'submitted'        => 'bg-slate-100 text-slate-700',
            'in_review'        => 'bg-amber-100 text-amber-800',
            'follow_up'        => 'bg-blue-100 text-blue-800',
            'closed'           => 'bg-emerald-100 text-emerald-800',
            'closed_pa'        => 'bg-emerald-100 text-emerald-800',
            'closed_pn'        => 'bg-teal-100 text-teal-800',
            'closed_mediation' => 'bg-lime-100 text-lime-800',
        ];
        $shortStatusLabels = [
            'submitted'        => 'Diajukan',
            'in_review'        => 'Ditinjau',
            'follow_up'        => 'Ditindaklanjuti',
            'closed'           => 'Selesai',
            'closed_pa'        => 'Selesai — PA',
            'closed_pn'        => 'Selesai — PN',
            'closed_mediation' => 'Selesai — Mediasi',
        ];
        $lastBadge = $last ? ($statusClasses[$last->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200') : '';

        return view('dashboard.user.home', compact(
            'counts', 'last', 'statusLabels', 'statusClasses', 'shortStatusLabels', 'lastBadge'
        ));
    }
}
