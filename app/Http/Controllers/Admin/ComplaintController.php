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
        // Policy: admin bisa melihat daftar pengaduan
        $this->authorize('viewAny', Complaint::class);

        // Kartu/stat ringkas di dashboard admin
        $counts = [
            'baru'   => Complaint::where('status', 'submitted')->count(),
            'aktif'  => Complaint::whereIn('status', ['submitted','in_review','follow_up'])->count(),
            'follow_up' => Complaint::where('status', 'follow_up')->count(),
            'selesai_bulan_ini' => Complaint::where('status','like','closed%')
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10,20,50,100], true) ? $perPage : 10;

        // Build query + info dateField yang dipakai
        [$query, $dateField] = $this->buildFilteredQuery($request);

        $complaints = $query
            ->with(['lastStatusActivity.causer'])
            ->orderByDesc($dateField)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

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
        // Policy: siapa pun admin yang berwenang boleh melihat
        $this->authorize('view', $complaint);

        return view('dashboard.admin.complaints.show', compact('complaint'));
    }

    /**
     * Update status pengaduan.
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        // Policy: kontrol siapa yang boleh ubah status
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
     * Export CSV sesuai filter aktif.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        // Policy: hak khusus untuk ekspor
        $this->authorize('export', Complaint::class);

        [$query, $dateField] = $this->buildFilteredQuery($request);

        $delimiter = $request->get('delimiter') === 'semicolon' ? ';' : ',';
        $statusLabels = Complaint::statusLabels();
        $filename = 'complaints-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query, $statusLabels, $delimiter) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Kode','Kategori','Deskripsi','Nama Pelapor','No. HP Pelapor','Umur Pelapor',
                'Disabilitas (Ya/Tidak)','Pekerjaan Pelapor','Provinsi','Kab/Kota','Kecamatan',
                'Alamat Spesifik','Nama Pelaku','Umur Pelaku','Pekerjaan Pelaku',
                'Akun Pelapor','Email Akun','Status','Dibuat',
            ], $delimiter);

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

    protected function buildFilteredQuery(Request $request): array
    {
        $q           = trim((string) $request->get('q', ''));
        $status      = $request->get('status');
        $statusGroup = $request->get('status_group');
        $statusLike  = $request->get('status_like');
        $from        = $request->get('from');
        $to          = $request->get('to');

        $dateField = $this->determineDateField(
            $statusGroup,
            $statusLike,
            $status,
            $request->get('date_field')
        );

        $query = Complaint::query()->with('user');

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

        if ($statusGroup === 'active') {
            $query->whereIn('status', ['submitted','in_review','follow_up']);
        } elseif ($statusGroup === 'closed_all') {
            $query->where('status', 'like', 'closed%');
        } elseif (!empty($statusLike)) {
            $query->where('status', 'like', $statusLike);
        } elseif (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($from)) {
            $query->whereDate($dateField, '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate($dateField, '<=', $to);
        }

        return [$query, $dateField];
    }

    protected function determineDateField(?string $statusGroup, ?string $statusLike, ?string $status, ?string $override): string
    {
        if (in_array($override, ['created_at', 'updated_at', 'closed_at'], true)) {
            return $override;
        }

        if (
            $statusGroup === 'closed_all' ||
            (!empty($statusLike) && Str::startsWith($statusLike, 'closed')) ||
            (!empty($status) && Str::startsWith($status, 'closed'))
        ) {
            return 'updated_at';
        }

        return 'created_at';
    }

    protected function csvSafe($value): string
    {
        $s = (string) ($value ?? '');
        return preg_match('/^[=\-+@]/', $s) ? "'".$s : $s;
    }
}
