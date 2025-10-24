<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Wilayah (opsional) - hanya jika paket laravolt/indonesia terpasang
// composer require laravolt/indonesia:^0.38
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Regency;
use Laravolt\Indonesia\Models\District;

class ComplaintController extends Controller
{
    /**
     * List pengaduan dengan filter & pencarian.
     */
    public function index(Request $request): View
    {
        $q       = trim((string) $request->get('q', ''));
        $status  = $request->get('status');
        $from    = $request->get('from'); // yyyy-mm-dd
        $to      = $request->get('to');   // yyyy-mm-dd
        $perPage = (int) $request->get('per_page', 10);

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

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        $complaints = $query->latest()->paginate($perPage)->withQueryString();

        return view('dashboard.admin.complaints.index', compact(
            'complaints', 'q', 'status', 'from', 'to', 'perPage'
        ));
    }

    /**
     * Detail pengaduan.
     * Admin & super-admin boleh lihat semua; user biasa hanya miliknya.
     */
    public function show(Complaint $complaint): View
    {
        if ($complaint->user_id !== Auth::id() &&
            ! Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
            abort(403);
        }

        return view('dashboard.admin.complaints.show', compact('complaint'));
    }

    /**
     * Update status pengaduan dari tabel admin (quick update).
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $data = $request->validate([
            'status'     => 'required|in:submitted,in_review,follow_up,closed',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $complaint->update($data);

        return back()->with('status', 'Status pengaduan diperbarui.');
    }

    /**
     * Export CSV sesuai filter aktif (lengkap & efisien).
     */
    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $q       = trim((string) $request->get('q', ''));
    $status  = $request->get('status');
    $from    = $request->get('from');
    $to      = $request->get('to');

    $delimiter = $request->get('delimiter') === 'semicolon' ? ';' : ',';

    $statusLabels = [
        'submitted'  => 'Diajukan',
        'in_review'  => 'Ditinjau',
        'follow_up'  => 'Ditindaklanjuti',
        'closed'     => 'Selesai',
    ];

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

        // BOM UTF-8 untuk Excel Windows
        fwrite($out, "\xEF\xBB\xBF");

        // Header CSV — tidak ada 'title', sertakan wilayah & field baru
        fputcsv($out, [
            'Kode',
            'Kategori',
            'Deskripsi',
            'Nama Pelapor',
            'No. HP Pelapor',
            'Umur Pelapor',
            'Disabilitas (Ya/Tidak)',
            'Pekerjaan Pelapor',
            'Provinsi',
            'Kab/Kota',
            'Kecamatan',
            'Alamat Spesifik',
            'Nama Pelaku',
            'Umur Pelaku',
            'Pekerjaan Pelaku',
            'Akun Pelapor',
            'Email Akun',
            'Status',
            'Dibuat',
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
                    // pakai nama tersimpan; fallback ke kode bila nama kosong
                    $c->province_name ?: $c->province_code,
                    $c->regency_name  ?: $c->regency_code,
                    $c->district_name ?: $c->district_code,
                    $c->reporter_address,
                    $c->perpetrator_name,
                    $c->perpetrator_age,
                    $c->perpetrator_job,
                    optional($c->user)->name,
                    optional($c->user)->email,
                    $statusLabels[$c->status] ?? $c->status,
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
