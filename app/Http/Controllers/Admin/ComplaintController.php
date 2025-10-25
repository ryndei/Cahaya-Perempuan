<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rule; // ✅ penting
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

// (Opsional) Laravolt jika dipakai
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Regency;
use Laravolt\Indonesia\Models\District;

class ComplaintController extends Controller
{
    public function index(Request $request): View
{
    // (Opsional) angka kartu/stat
   $counts = [
    'baru'   => \App\Models\Complaint::where('status','submitted')->count(),
    'aktif'  => \App\Models\Complaint::whereIn('status',['submitted','in_review','follow_up'])->count(),
    'follow_up' => \App\Models\Complaint::where('status','follow_up')->count(),

    // Opsi A (tanpa kolom closed_at): pakai updated_at
    'selesai_bulan_ini' => \App\Models\Complaint::where('status','like','closed%')
        ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->count(),
    ];

    $q           = trim((string) $request->get('q', ''));
    $status      = $request->get('status');          // exact (string), contoh: "closed_pa"
    $statusGroup = $request->get('status_group');    // "active" | "closed_all"
    $statusLike  = $request->get('status_like');     // contoh: "closed%"
    $from        = $request->get('from');            // yyyy-mm-dd
    $to          = $request->get('to');              // yyyy-mm-dd
    $perPage     = (int) $request->get('per_page', 10);

    $query = Complaint::query()->with('user');

    // Pencarian bebas
    if ($q !== '') {
        $query->where(function ($w) use ($q) {
            $w->where('description', 'like', "%{$q}%")
              ->orWhere('category', 'like', "%{$q}%")
              ->orWhere('reporter_name', 'like', "%{$q}%")
              ->orWhere('reporter_phone', 'like', "%{$q}%")
              ->orWhere('reporter_address', 'like', "%{$q}%")
              ->orWhere('reporter_job', 'like', "%{$q}%")
              ->orWhere('perpetrator_name', 'like', "%{$q}%")
              ->orWhere('perpetrator_job', 'like', "%{$q}%")
              ->orWhereHas('user', function ($u) use ($q) {
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

    // ===== KUNCI PERBAIKAN: tentukan field tanggal dinamis =====
    $dateField = 'created_at';

    // Jika filter yang dipakai adalah varian "closed*" (selesai),
    // maka gunakan updated_at agar menyaring berdasarkan TANGGAL DISELESAIKAN.
    if (
        $statusGroup === 'closed_all' ||
        (!empty($statusLike) && Str::startsWith($statusLike, 'closed')) ||
        (!empty($status) && Str::startsWith($status, 'closed'))
    ) {
        $dateField = 'updated_at';
    }

    // Terapkan rentang tanggal (kalau ada)
    if (!empty($from)) {
        $query->whereDate($dateField, '>=', $from);
    }
    if (!empty($to)) {
        $query->whereDate($dateField, '<=', $to);
    }
    // ===== END PERBAIKAN =====

    $complaints = $query->latest()->paginate($perPage)->withQueryString();

    return view('dashboard.admin.complaints.index', compact(
        'complaints', 'q', 'status', 'statusGroup', 'statusLike', 'from', 'to', 'perPage', 'counts'
    ));
}


    public function show(Complaint $complaint): View
    {
        if ($complaint->user_id !== Auth::id() &&
            ! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin']))) {
            abort(403);
        }

        return view('dashboard.admin.complaints.show', compact('complaint'));
    }

    /**
     * ✅ Update status pengaduan (pakai whitelist dari Model)
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
     * Export CSV sesuai filter aktif
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $q       = trim((string) $request->get('q', ''));
        $status  = $request->get('status');
        $from    = $request->get('from');
        $to      = $request->get('to');

        $delimiter = $request->get('delimiter') === 'semicolon' ? ';' : ',';

        // ✅ Ambil label dari Model agar memuat status baru
        $statusLabels = Complaint::statusLabels();

        $query = Complaint::query()->with('user');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%")
                  ->orWhere('reporter_name', 'like', "%{$q}%")
                  ->orWhere('reporter_phone', 'like', "%{$q}%")
                  ->orWhere('reporter_address', 'like', "%{$q}%")
                  ->orWhere('reporter_job', 'like', "%{$q}%")
                  ->orWhere('perpetrator_name', 'like', "%{$q}%")
                  ->orWhere('perpetrator_job', 'like', "%{$q}%")
                  ->orWhereHas('user', function ($u) use ($q) {
                      $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                  });
            });
        }
        if (!empty($status)) $query->where('status', $status);
        if (!empty($from))   $query->whereDate('created_at', '>=', $from);
        if (!empty($to))     $query->whereDate('created_at', '<=', $to);

        $filename = 'complaints-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query, $statusLabels, $delimiter) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 (Excel Windows)

            fputcsv($out, [
                'Kode','Kategori','Deskripsi','Nama Pelapor','No. HP Pelapor','Umur Pelapor',
                'Disabilitas (Ya/Tidak)','Pekerjaan Pelapor','Provinsi','Kab/Kota','Kecamatan',
                'Alamat Spesifik','Nama Pelaku','Umur Pelaku','Pekerjaan Pelaku',
                'Akun Pelapor','Email Akun','Status','Dibuat',
            ], $delimiter);

            $query->latest()->chunk(500, function ($rows) use ($out, $statusLabels, $delimiter) {
                foreach ($rows as $c) {
                    fputcsv($out, [
                        $c->code ?? $c->id,
                        $c->category,
                        preg_replace("/\r|\n/", ' ', \Illuminate\Support\Str::limit($c->description, 2000)),
                        $c->reporter_name,
                        $c->reporter_phone,
                        $c->reporter_age,
                        is_null($c->reporter_is_disability) ? '' : ($c->reporter_is_disability ? 'Ya' : 'Tidak'),
                        $c->reporter_job,
                        $c->province_name ?: $c->province_code,
                        $c->regency_name  ?: $c->regency_code,
                        $c->district_name ?: $c->district_code,
                        $c->reporter_address,
                        $c->perpetrator_name,
                        $c->perpetrator_age,
                        $c->perpetrator_job,
                        optional($c->user)->name,
                        optional($c->user)->email,
                        $statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status), // ✅
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
}
