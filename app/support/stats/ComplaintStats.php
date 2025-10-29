<?php

namespace App\Support\Stats;

use App\Models\Complaint;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ComplaintStats
{
    public function __construct(
        protected ?Carbon $from = null,
        protected ?Carbon $to   = null,
    ) {
        if ($this->from && $this->to && $this->from->gt($this->to)) {
            [$this->from, $this->to] = [$this->to, $this->from];
        }
    }

    /** Query dasar dengan filter created_at */
    protected function base(): Builder
    {
        $q = Complaint::query();

        if ($this->from) $q->where('created_at', '>=', $this->from);
        if ($this->to)   $q->where('created_at', '<=', $this->to);

        return $q;
    }

    /* ===================== KPI ===================== */

    public function kpis(): array
    {
        $active = [Complaint::STATUS_SUBMITTED, Complaint::STATUS_IN_REVIEW, Complaint::STATUS_FOLLOW_UP];

        $baru     = (clone $this->base())->where('status', Complaint::STATUS_SUBMITTED)->count();
        $aktif    = (clone $this->base())->whereIn('status', $active)->count();
        $followUp = (clone $this->base())->where('status', Complaint::STATUS_FOLLOW_UP)->count();

        $selesaiBulanIni = Complaint::where('status','like','closed%')
            ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $resolved = (clone $this->base())->whereNotNull('closed_at')->get(['created_at','closed_at']);
        $avgHours = 0;
        if ($resolved->count()) {
            $totalHours = $resolved->sum(fn($c) => $c->created_at->diffInMinutes($c->closed_at)/60);
            $avgHours   = round($totalHours / $resolved->count(), 1);
        }

        return [
            'baru'              => $baru,
            'aktif'             => $aktif,
            'follow_up'         => $followUp,
            'selesai_bulan_ini' => $selesaiBulanIni,
            'avg_resolve_hours' => $avgHours,
        ];
    }

    /* ===================== Distribusi ===================== */

    public function byStatus(): array
    {
        $labels = Complaint::statusLabels();

        $rows = (clone $this->base())
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->orderByDesc('c')
            ->get();

        return $rows->map(fn ($r) => [
            'status' => $r->status,
            'label'  => $labels[$r->status] ?? ucfirst(str_replace('_',' ',$r->status)),
            'count'  => (int) $r->c,
        ])->values()->all();
    }

    public function byCategory(int $limit = 10): array
    {
        $rows = (clone $this->base())
            ->whereNotNull('category')
            ->select('category', DB::raw('COUNT(*) as c'))
            ->groupBy('category')
            ->orderByDesc('c')
            ->limit($limit)
            ->get();

        return $rows->map(fn ($r) => [
            'category' => (string) $r->category,
            'count'    => (int) $r->c,
        ])->values()->all();
    }

    public function byProvince(int $limit = 10): array
    {
        $rows = (clone $this->base())
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(province_name),''), province_code, 'Unknown') as province"),
                DB::raw('COUNT(*) as c')
            )
            ->groupBy('province')
            ->orderByDesc('c')
            ->limit($limit)
            ->get();

        return $rows->map(fn ($r) => [
            'province' => (string) $r->province,
            'count'    => (int) $r->c,
        ])->values()->all();
    }

    /* ===================== Umur: BUCKET ===================== */

    public function reporterAgeBuckets(): array
    {
        return $this->ageBuckets('reporter_age_bucket');
    }

    public function perpetratorAgeBuckets(): array
    {
        return $this->ageBuckets('perpetrator_age_bucket');
    }

    protected function ageBuckets(string $bucketCol): array
    {
        $rows = (clone $this->base())
            ->select($bucketCol, DB::raw('COUNT(*) as c'))
            ->whereNotNull($bucketCol)
            ->groupBy($bucketCol)
            ->orderBy($bucketCol)
            ->get();

        $labels = [
            1 => '<=17', 2 => '18–24', 3 => '25–34', 4 => '35–44',
            5 => '45–54', 6 => '55–64', 7 => '>=65',
        ];

        return $rows->map(fn ($r) => [
            'label' => $labels[(int)$r->{$bucketCol}] ?? (string)$r->{$bucketCol},
            'count' => (int) $r->c,
        ])->values()->all();
    }

    /* ===================== Umur: DETAIL (Exact) ===================== */

    public function reporterAgesExact(int $maxAge = 100): array
    {
        return $this->agesExact('reporter_age', $maxAge);
    }

    public function perpetratorAgesExact(int $maxAge = 100): array
    {
        return $this->agesExact('perpetrator_age', $maxAge);
    }

    protected function agesExact(string $column, int $maxAge = 100): array
    {
        $vals = (clone $this->base())
            ->whereNotNull($column)
            ->get(['id', $column])
            ->pluck($column);

        $ints = $vals
            ->map(fn ($v) => (int) preg_replace('/\D+/', '', (string) $v))
            ->filter(fn ($n) => $n > 0 && $n <= $maxAge)
            ->values();

        if ($ints->isEmpty()) return ['labels' => [], 'counts' => []];

        $hist = $ints->groupBy(fn ($n) => $n)->sortKeys()->map->count();

        return [
            'labels' => $hist->keys()->values()->all(),
            'counts' => $hist->values()->all(),
        ];
    }

    /* ===================== Pekerjaan (Top N) ===================== */

    public function jobsReporterTop(int $limit = 10): array
    {
        return $this->jobsTop('reporter_job', $limit);
    }

    public function jobsPerpTop(int $limit = 10): array
    {
        return $this->jobsTop('perpetrator_job', $limit);
    }

    protected function jobsTop(string $column, int $limit): array
    {
        // Ambil model (agar cast encrypted -> plaintext), lalu pluck & normalisasi
        $vals = (clone $this->base())
            ->whereNotNull($column)
            ->get(['id', $column])
            ->pluck($column);

        // Normalisasi sederhana: trim, lowercase, hapus tanda baca, map sinonim
        $hist = $vals
            ->map(fn ($v) => $this->normalizeJob((string) $v))
            ->filter() // buang kosong/null
            ->groupBy(fn ($s) => $s)
            ->map->count()
            ->sortDesc()
            ->take($limit);

        return $hist->map(fn ($c, $label) => [
            'label' => Str::title($label),
            'count' => (int) $c,
        ])->values()->all();
    }

    protected function normalizeJob(?string $v): ?string
    {
        $s = strtolower(trim((string) $v));
        if ($s === '') return null;

        // hapus karakter selain huruf/angka/spasi
        $s = preg_replace('/[^a-z0-9\s]/', '', $s);
        $s = preg_replace('/\s+/', ' ', $s);

        // contoh pemetaan ringan (silakan tambah sendiri)
        $map = [
            'wirausaha' => 'wiraswasta',
            'entrepreneur' => 'wiraswasta',
            'ibu rumah tangga' => 'irt',
            'i r t' => 'irt',
            'pelajar' => 'siswa',
            'student' => 'siswa',
            'mahasiswa' => 'mahasiswa',
            'buruh' => 'buruh',
            'pns' => 'pns',
            'asn' => 'pns',
            'petani' => 'petani',
            'nelayan' => 'nelayan',
            'guru' => 'guru',
            'swasta' => 'karyawan swasta',
            'karyawan' => 'karyawan swasta',
        ];

        foreach ($map as $k => $to) {
            if (str_contains($s, $k)) return $to;
        }

        return $s;
    }

    /* ===================== Timeseries ===================== */

    public function timeseries(int $days = 30): array
    {
        if ($this->from && $this->to) {
            $start = (clone $this->from)->startOfDay();
            $end   = (clone $this->to)->endOfDay();
        } else {
            $end   = now()->endOfDay();
            $start = (clone $end)->subDays($days - 1)->startOfDay();
        }

        $keys = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $keys->push($d->toDateString());
        }

        $raw = (clone $this->base())
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd');

        $labels = $keys->map(fn ($d) => Carbon::parse($d)->locale('id')->isoFormat('D MMM'))->values()->all();
        $counts = $keys->map(fn ($d) => (int) ($raw[$d] ?? 0))->values()->all();

        return ['labels' => $labels, 'counts' => $counts];
    }
}
