<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplaintController extends Controller
{
    /**
     * List pengaduan dengan filter & pencarian.
     */
    public function index(Request $request): View
    {
        $q          = trim((string) $request->get('q', ''));
        $status     = $request->get('status');
        $from       = $request->get('from'); // yyyy-mm-dd
        $to         = $request->get('to');   // yyyy-mm-dd
        $perPage    = (int) $request->get('per_page', 10);

        $query = Complaint::query()->with('user');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
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
        if (
            $complaint->user_id !== Auth::id() &&
            ! Auth::user()->hasAnyRole(['admin', 'super-admin'])
        ) {
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
     * Export CSV sesuai filter aktif.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $q          = trim((string) $request->get('q', ''));
        $status     = $request->get('status');
        $visibility = $request->get('visibility');
        $from       = $request->get('from'); // yyyy-mm-dd
        $to         = $request->get('to');   // yyyy-mm-dd

        // delimiter: default koma. kirim ?delimiter=semicolon untuk titik-koma
        $delimiterArg = $request->get('delimiter', ',');
        $delimiter = $delimiterArg === 'semicolon' ? ';' : ',';

        $statusLabels = [
            'submitted'  => 'Diajukan',
            'in_review'  => 'Ditinjau',
            'follow_up'  => 'Ditindaklanjuti',
            'closed'     => 'Selesai',
        ];

        $query = Complaint::query()->with('user');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhereHas('user', function ($u) use ($q) {
                      $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                  });
            });
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($visibility)) {
            $query->where('visibility', $visibility);
        }
        if (!empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        $filename = 'complaints-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query, $statusLabels, $delimiter) {
            $out = fopen('php://output', 'w');

            // BOM UTF-8 supaya Excel Windows menampilkan karakter dengan benar
            fwrite($out, "\xEF\xBB\xBF");

            // Header kolom (lengkap termasuk data pelapor)
            fputcsv($out, [
                'Kode',
                'Judul',
                'Kategori',
                'Nama Pelapor',
                'Telepon Pelapor',
                'Alamat Pelapor',
                'Akun Pelapor',
                'Email Akun',
                'Status',
                'Visibilitas',
                'Dibuat',
            ], $delimiter);

            // Stream per chunk agar hemat memori
            $query->latest()->chunk(500, function ($rows) use ($out, $statusLabels, $delimiter) {
                foreach ($rows as $c) {
                    fputcsv($out, [
                        $c->code ?? $c->id,
                        $c->title,
                        $c->category,
                        $c->reporter_name,
                        $c->reporter_phone,
                        $c->reporter_address,
                        optional($c->user)->name,
                        optional($c->user)->email,
                        $statusLabels[$c->status] ?? $c->status,
                        isset($c->visibility) ? str_replace('_', ' ', $c->visibility) : null,
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
