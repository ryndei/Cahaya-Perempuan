<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    /**
     * Halaman indeks: daftar pengaduan + filter + kartu ringkas.
     */
    public function index(Request $request): View
    {
        // Kartu/stat ringkas di dashboard admin
        $counts = [
            'baru'   => Complaint::where('status', 'submitted')->count(),
            'aktif'  => Complaint::whereIn('status', ['submitted','in_review','follow_up'])->count(),
            'follow_up' => Complaint::where('status', 'follow_up')->count(),
            // tanpa kolom closed_at → pakai updated_at sebagai tanggal selesai
            'selesai_bulan_ini' => Complaint::where('status','like','closed%')
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        // Batasi per_page agar tidak terlalu besar
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10,20,50,100], true) ? $perPage : 10;

        // Build query + info dateField yang dipakai
        [$query, $dateField] = $this->buildFilteredQuery($request);

        // Urutan: pakai dateField aktif, lalu id. Eager-load activity terakhir & causer.
        $complaints = $query
            ->with(['lastStatusActivity.causer'])  // ⬅️ untuk "diubah oleh siapa & kapan"
            ->orderByDesc($dateField)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        // Untuk mempertahankan nilai filter di view
        $q           = trim((string) $request->get('q', ''));
        $status      = $request->get('status');
        $statusGroup = $request->get('status_group');
        $statusLike  = $request->get('status_like');
        $from        = $request->get('from');
        $to          = $request->get('to');

        return view('dashboard.admin.complaints.index', compact(
            'complaints', 'q', 'status', 'statusGroup', 'statusLike', 'from', 'to', 'perPage', 'counts'
        ));
    }

    /**
     * Detail pengaduan.
     */
    public function show(Complaint $complaint): View
    {
        if (
            $complaint->user_id !== Auth::id() &&
            !(Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin']))
        ) {
            abort(403);
        }

        return view('dashboard.admin.complaints.show', compact('complaint'));
    }

    /**
     * Update status pengaduan (pakai whitelist dari Model).
     * Logging perubahan status ditangani oleh Spatie di Model (tidak di controller) → hindari duplikasi log.
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
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
     * Export CSV sesuai filter aktif (selalu konsisten dengan index).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        // Query hasil filter yang sama dengan index()
        [$query, $dateField] = $this->buildFilteredQuery($request);

        // Delimiter (default comma)
        $delimiter = $request->get('delimiter') === 'semicolon' ? ';' : ',';

        // Label resmi status dari Model
        $statusLabels = Complaint::statusLabels();

        $filename = 'complaints-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query, $statusLabels, $delimiter) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 agar rapi di Excel Windows
            fwrite($out, "\xEF\xBB\xBF");

            // Header
            fputcsv($out, [
                'Kode','Kategori','Deskripsi','Nama Pelapor','No. HP Pelapor','Umur Pelapor',
                'Disabilitas (Ya/Tidak)','Pekerjaan Pelapor','Provinsi','Kab/Kota','Kecamatan',
                'Alamat Spesifik','Nama Pelaku','Umur Pelaku','Pekerjaan Pelaku',
                'Akun Pelapor','Email Akun','Status','Dibuat',
            ], $delimiter);

            // Data
            $query->orderByDesc('id')->chunk(500, function ($rows) use ($out, $statusLabels, $delimiter) {
                foreach ($rows as $c) {
                    fputcsv($out, [
                        $this->csvSafe($c->code ?? $c->id),
                        $this->csvSafe($c->category),
                        $this->csvSafe(preg_replace("/\r|\n/", ' ', \Illuminate\Support\Str::limit($c->description, 2000))),
                        $this->csvSafe($c->reporter_name),
                        $this->csvSafe($c->reporter_phone),
                        $c->reporter_age,
                        is_null($c->reporter_is_disability) ? '' : ($c->reporter_is_disability ? 'Ya' : 'Tidak'),
                        $this->csvSafe($c->reporter_job),
                        $this->csvSafe($c->province_name ?: $c->province_code),
                        $this->csvSafe($c->regency_name  ?: $c->regency_code),
                        $this->csvSafe($c->district_name ?: $c->district_code),
                        $this->csvSafe($c->reporter_address),
                        $this->csvSafe($c->perpetrator_name),
                        $c->perpetrator_age,
                        $this->csvSafe($c->perpetrator_job),
                        $this->csvSafe(optional($c->user)->name),
                        $this->csvSafe(optional($c->user)->email),
                        $this->csvSafe($statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status)),
                        optional($c->created_at)?->format('d-m-Y H:i'),
                    ], $delimiter);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type'  => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
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

        // Pencarian bebas
        if ($q !== '') {
            $query->where(function (Builder $w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%")
                  ->orWhere('reporter_name', 'like', "%{$q}%")
                  ->orWhere('reporter_phone', 'like', "%{$q}%")
                  ->orWhere('reporter_address', 'like', "%{$q}%")
                  ->orWhere('reporter_job', 'like', "%{$q}%")
                  ->orWhere('perpetrator_name', 'like', "%{$q}%")
                  ->orWhere('perpetrator_job', 'like', "%{$q}%")
                  ->orWhereHas('user', function (Builder $u) use ($q) {
                      $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                  });
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
        // Jika user memaksa override, hormati selama termasuk whitelist
        if (in_array($override, ['created_at', 'updated_at', 'closed_at'], true)) {
            return $override;
        }

        // Jika filter yang dipakai keluarga "closed*" → gunakan updated_at (tanggal selesai)
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

    /**
     * Lindungi CSV dari formula injection di Excel (nilai yg mulai =, +, -, @).
     */
    protected function csvSafe($value): string
    {
        $s = (string) ($value ?? '');
        return preg_match('/^[=\-+@]/', $s) ? "'".$s : $s;
    }
}
