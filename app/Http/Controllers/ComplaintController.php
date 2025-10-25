<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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
        // Kategori default
        $categories = [
            'KDRT Terhadap Anak',
            'KDRT Terhadap Istri',
            'Pelecehan Seksual',
            'Kekerasan Seksual Berbasis Online (KSBO)',
            'Kekerasan dalam Pacaran',
            'Lainnya',
        ];

        // Data wilayah (aman walau paket tidak terpasang)
        $provinsi = class_exists(Province::class)
            ? Province::select('code', 'name')->orderBy('name')->get()
            : collect();

        $cities = class_exists(City::class)
            ? City::select('code', 'name', 'province_code')->orderBy('name')->get()
                ->groupBy('province_code')
                ->map(fn ($rows) => $rows->map(fn ($c) => ['code' => $c->code, 'name' => $c->name])->values())
            : collect();

        // PENTING: District merefer ke city_code (bukan regency_code)
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

    // Hardening: jangan izinkan user isi status/admin_note
    unset($data['status'], $data['admin_note']);
    $data['status'] = \App\Models\Complaint::STATUS_SUBMITTED; // redundan tapi aman

    if ($request->hasFile('attachment')) {
        $data['attachment_path'] = $request->file('attachment')->store('complaints', 'public');
    }

    Complaint::create($data); // user_id & code diisi otomatis via booted()

    return redirect()->route('complaints.index')
        ->with('status', 'Pengaduan berhasil dikirim.');
    }

    public function show(Complaint $complaint): View
    {
        abort_if(
            $complaint->user_id !== Auth::id()
            && ! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin'])),
            403
        );

        return view('dashboard.user.complaints.show', compact('complaint'));
    }
}
