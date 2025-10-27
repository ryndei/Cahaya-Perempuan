<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
// Wilayah (opsional)
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;

class ComplaintController extends Controller
{
    public function index(): View
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->latest()->paginate(10);

        $recent = Complaint::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.user.complaints.index', compact('complaints', 'recent'));
    }

    public function create(): View
    {
        $categories = [
            'KDRT Terhadap Anak',
            'KDRT Terhadap Istri',
            'Pelecehan Seksual',
            'Kekerasan Seksual Berbasis Online (KSBO)',
            'Kekerasan dalam Pacaran',
            'Lainnya',
        ];

        $provinsi = class_exists(Province::class)
            ? Province::select('code', 'name')->orderBy('name')->get()
            : collect();

        $cities = class_exists(City::class)
            ? City::select('code', 'name', 'province_code')->orderBy('name')->get()
                ->groupBy('province_code')
                ->map(fn ($rows) => $rows->map(fn ($c) => ['code' => $c->code, 'name' => $c->name])->values())
            : collect();

        // District merefer ke city_code
        $districts = class_exists(District::class)
            ? District::select('code', 'name', 'city_code')->orderBy('name')->get()
                ->groupBy('city_code')
                ->map(fn ($rows) => $rows->map(fn ($d) => ['code' => $d->code, 'name' => $d->name])->values())
            : collect();

        return view('dashboard.user.complaints.create', compact('categories', 'provinsi', 'cities', 'districts'));
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Hardening: abaikan input berbahaya
        unset($data['user_id'], $data['code'], $data['status'], $data['admin_note']);
        $data['status'] = Complaint::STATUS_SUBMITTED;

        try {
            // Buat record (user_id & code dipaksa di Model::creating)
            $complaint = Complaint::create($data);

            // Simpan lampiran ke DISK PRIVAT
            if ($request->hasFile('attachment')) {
                $path = Storage::disk('private')->putFile('complaints/'.$complaint->id, $request->file('attachment'));
                $complaint->attachment_path = $path;
                $complaint->save();
            }

            return redirect()->route('complaints.index')->with('status', 'Pengaduan berhasil dikirim.');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pengaduan', [
                'error'   => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan saat menyimpan pengaduan. Silakan coba lagi.',
            ]);
        }
    }

    public function show(Complaint $complaint): View
    {
        abort_if(
            $complaint->user_id !== Auth::id()
            && !(Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin'])),
            403
        );

        return view('dashboard.user.complaints.show', compact('complaint'));
    }

    public function downloadAttachment(Complaint $complaint): StreamedResponse
{
    // Batasi akses: pemilik/tuan tiket atau admin/super-admin
    $isOwner = $complaint->user_id === Auth::id();
    $isAdmin = Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin']);
    abort_unless($isOwner || $isAdmin, 403);

    // Pastikan ada path & file exist di disk privat
    if (!$complaint->attachment_path || !Storage::disk('private')->exists($complaint->attachment_path)) {
        abort(404, 'Lampiran tidak ditemukan.');
    }

    // Nama file download yang ramah (tanpa bocorkan struktur internal)
    $downloadName = 'lampiran-'.$complaint->code.'.'.pathinfo($complaint->attachment_path, PATHINFO_EXTENSION) ?: 'bin';

    return Storage::disk('private')->download($complaint->attachment_path, $downloadName);
}
}
