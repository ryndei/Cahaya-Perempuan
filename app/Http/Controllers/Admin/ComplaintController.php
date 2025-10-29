<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComplaintsExport;

class ComplaintController extends Controller
{
    /**
     * Halaman indeks: daftar pengaduan + filter + kartu ringkas.
     */
    public function index(Request $request): View
    {
        // Policy: admin bisa melihat daftar pengaduan
        $this->authorize('viewAny', Complaint::class);

        // Kartu/stat ringkas (opsional untuk header halaman)
        $counts = [
            'baru'              => Complaint::where('status', 'submitted')->count(),
            'aktif'             => Complaint::whereIn('status', ['submitted','in_review','follow_up'])->count(),
            'follow_up'         => Complaint::where('status', 'follow_up')->count(),
            'selesai_bulan_ini' => Complaint::where('status','like','closed%')
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        // Batasi per_page ke nilai yang diizinkan
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10,20,50,100], true) ? $perPage : 10;

        // Build query + info dateField yang dipakai
        [$query, $dateField] = $this->buildFilteredQuery($request);

        $complaints = $query
            ->with(['user', 'lastStatusActivity.causer'])
            ->orderByDesc($dateField)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        // Pertahankan nilai filter untuk view
        $q           = trim((string) $request->get('q', ''));
        $status      = $request->get('status');
        $statusGroup = $request->get('status_group');
        $statusLike  = $request->get('status_like');
        $from        = $request->get('from');
        $to          = $request->get('to');

        // Label/kualitas badge status untuk tabel
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

        return view('dashboard.admin.complaints.index', compact(
            'complaints',
            'q','status','statusGroup','statusLike','from','to','perPage',
            'counts','statusLabels','statusClasses','shortStatusLabels'
        ));
    }

    /**
     * Detail pengaduan.
     */
    public function show(Complaint $complaint): View
    {
        // Policy
        $this->authorize('view', $complaint);

        $statusLabels = Complaint::statusLabels();
        $statusClasses = [
            'submitted'        => 'bg-slate-100 text-slate-800',
            'in_review'        => 'bg-amber-100 text-amber-800',
            'follow_up'        => 'bg-blue-100 text-blue-800',
            'closed'           => 'bg-emerald-100 text-emerald-800',
            'closed_pa'        => 'bg-emerald-100 text-emerald-800',
            'closed_pn'        => 'bg-teal-100 text-teal-800',
            'closed_mediation' => 'bg-lime-100 text-lime-800',
        ];
        $badge = $statusClasses[$complaint->status] ?? 'bg-slate-100 text-slate-800';

        // Tanggal
        $createdAtText = optional($complaint->created_at)?->translatedFormat('d M Y H:i') ?? '—';
        $updatedAtText = optional($complaint->updated_at)?->translatedFormat('d M Y H:i') ?? '—';

        // Disabilitas
        $disabilityText = is_null($complaint->reporter_is_disability)
            ? '—'
            : ($complaint->reporter_is_disability ? 'Ya' : 'Tidak');

        // Lokasi ringkas
        $lokasiRingkas = collect([
            $complaint->district_name,
            $complaint->regency_name,
            $complaint->province_name,
        ])->filter()->implode(', ');

        // Label status untuk tampilan
        $statusLabel = $complaint->status_label;

        // Info pengubah terakhir (activitylog)
        $lastAct = $complaint->lastStatusActivity
            ?? $complaint->activities()->whereIn('event', ['status_updated','updated'])->latest('id')->first();

        $lastChanger       = optional($lastAct?->causer)->name ?? optional($lastAct?->causer)->email;
        $lastChangedAtText = optional($lastAct?->created_at)?->translatedFormat('d M Y H:i');
        $lastChangedDiff   = optional($lastAct?->created_at)?->diffForHumans();

        // Riwayat status (opsional)
        $history = $complaint->activities()
            ->whereIn('event', ['status_updated','updated'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard.admin.complaints.show', compact(
            'complaint',
            'statusLabels','statusLabel','badge',
            'createdAtText','updatedAtText',
            'disabilityText','lokasiRingkas',
            'lastChanger','lastChangedAtText','lastChangedDiff',
            'history'
        ));
    }

    /**
     * Update status pengaduan.
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('updateStatus', $complaint);

        $allowed = array_keys(Complaint::statusLabels());

        $data = $request->validate(
            [
                'status'     => ['required', Rule::in($allowed)],
                'admin_note' => ['nullable','string','max:1000'],
            ],
            [
                'status.required' => 'Status wajib dipilih.',
                'status.in'       => 'Pilihan status tidak valid.',
            ]
        );

        $complaint->update($data);

        return back()->with('status', 'Status pengaduan diperbarui.');
    }

    /**
     * Export Excel (XLSX) sesuai filter aktif pada index().
     * Pastikan ComplaintsExport menerima Builder $query di constructor.
     */
    public function exportXlsx(Request $request)
    {
        $this->authorize('export', Complaint::class);

        // Reuse filter yg sama dengan index()
        [$query] = $this->buildFilteredQuery($request);

        $filename = 'complaints-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ComplaintsExport($query), $filename);
    }

    /* ===================== Helpers ===================== */

    /**
     * Bangun query berdasarkan semua filter.
     * @return array{0: Builder, 1: string}  [Builder $query, string $dateField]
     */
    protected function buildFilteredQuery(Request $request): array
    {
        $q           = trim((string) $request->get('q', ''));
        $status      = $request->get('status');
        $statusGroup = $request->get('status_group');   // "active" | "closed_all"
        $statusLike  = $request->get('status_like');    // contoh: "closed%"
        $from        = $request->get('from');           // yyyy-mm-dd
        $to          = $request->get('to');             // yyyy-mm-dd

        // dateField dinamis atau override manual (?date_field=created_at/updated_at/closed_at)
        $dateField = $this->determineDateField(
            $statusGroup,
            $statusLike,
            $status,
            $request->get('date_field') // optional override
        );

        $query = Complaint::query()->with('user');

        // Pencarian bebas (HINDARI LIKE ke kolom terenkripsi)
        if ($q !== '') {
            $digits = preg_replace('/\D+/', '', $q); // ambil digit saja (untuk telp exact via hash)

            $query->where(function (Builder $w) use ($q, $digits) {
                $w->where('code', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%")
                  ->orWhere('province_name', 'like', "%{$q}%")
                  ->orWhere('regency_name', 'like', "%{$q}%")
                  ->orWhere('district_name', 'like', "%{$q}%")
                  ->orWhereHas('user', function (Builder $u) use ($q) {
                      $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                  });

                // Cari nomor telepon via blind index (EXACT)
                if ($digits && strlen($digits) >= 6) {
                    $w->orWhere('reporter_phone_hash', hash('sha256', $digits));
                }
            });
        }

        // Filter status
        if ($statusGroup === 'active') {
            $query->whereIn('status', ['submitted','in_review','follow_up']);
        } elseif ($statusGroup === 'closed_all') {
            $query->where('status', 'like', 'closed%');
        } elseif (!empty($statusLike)) {
            $query->where('status', 'like', $statusLike);
        } elseif (!empty($status)) {
            $query->where('status', $status);
        }

        // Rentang tanggal
        if (!empty($from)) {
            $query->whereDate($dateField, '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate($dateField, '<=', $to);
        }

        return [$query, $dateField];
    }

    /**
     * Tentukan field tanggal yang dipakai filter & sorting.
     */
    protected function determineDateField(?string $statusGroup, ?string $statusLike, ?string $status, ?string $override): string
    {
        // Jika user override, hormati jika valid
        if (in_array($override, ['created_at', 'updated_at', 'closed_at'], true)) {
            return $override;
        }

        // Keluarga "closed*" → gunakan updated_at sebagai tanggal selesai
        if (
            $statusGroup === 'closed_all' ||
            (!empty($statusLike) && Str::startsWith($statusLike, 'closed')) ||
            (!empty($status) && Str::startsWith($status, 'closed'))
        ) {
            return 'updated_at';
        }

        // Default
        return 'created_at';
    }
}
