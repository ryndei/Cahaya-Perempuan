<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Support\Stats\ComplaintStats;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $from = $request->filled('from') ? Carbon::parse((string) $request->query('from'))->startOfDay() : null;
        $to   = $request->filled('to')   ? Carbon::parse((string) $request->query('to'))->endOfDay()   : null;

        $stats       = new ComplaintStats($from, $to);

        $kpis        = $stats->kpis();
        $byStatus    = $stats->byStatus();
        $byCategory  = $stats->byCategory(10);
        $byProvince  = $stats->byProvince(10);
        $timeseries  = $stats->timeseries(30);

        // umur (BUCKET default)
        $ageBucketsReporter = $stats->reporterAgeBuckets();
        $ageBucketsPerp     = $stats->perpetratorAgeBuckets();

        // umur (DETAIL untuk toggle)
        $agesReporterExact  = $stats->reporterAgesExact(100);
        $agesPerpExact      = $stats->perpetratorAgesExact(100);

        // pekerjaan (Top 10)
        $jobsReporter       = $stats->jobsReporterTop(10);
        $jobsPerp           = $stats->jobsPerpTop(10);

        $trendLabels = $timeseries['labels'] ?? [];
        $trendCounts = $timeseries['counts'] ?? [];

        if ($request->wantsJson()) {
            return response()->json([
                'kpis'              => $kpis,
                'timeseries'        => ['labels' => $trendLabels, 'counts' => $trendCounts],
                'byStatus'          => $byStatus,
                'byCategory'        => $byCategory,
                'byProvince'        => $byProvince,
                'ageBucketsReporter'=> $ageBucketsReporter,
                'ageBucketsPerp'    => $ageBucketsPerp,
                'agesReporterExact' => $agesReporterExact,
                'agesPerpExact'     => $agesPerpExact,
                'jobsReporter'      => $jobsReporter,
                'jobsPerp'          => $jobsPerp,
            ]);
        }

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

        $recent = \App\Models\Complaint::with('user')->latest('id')->limit(6)->get();

        return view('dashboard.admin.home', compact(
            'kpis','byStatus','byCategory','byProvince','timeseries',
            'trendLabels','trendCounts','recent',
            'statusLabels','statusClasses','shortStatusLabels',
            'ageBucketsReporter','ageBucketsPerp',
            'agesReporterExact','agesPerpExact',
            'jobsReporter','jobsPerp'
        ));
    }
}
