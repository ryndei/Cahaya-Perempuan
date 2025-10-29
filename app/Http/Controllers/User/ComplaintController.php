<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;

class ComplaintController extends Controller
{
    /**
     * Daftar pengaduan milik user + ringkasan.
     */
    public function index(): View
    {
        $statusLabels = \App\Models\Complaint::statusLabels();

      // Warna badge
      $statusClasses = [
        'submitted'        => 'bg-slate-100 text-slate-700',
        'in_review'        => 'bg-amber-100 text-amber-800',
        'follow_up'        => 'bg-blue-100 text-blue-800',
        'closed'           => 'bg-emerald-100 text-emerald-800',
        'closed_pa'        => 'bg-emerald-100 text-emerald-800',
        'closed_pn'        => 'bg-teal-100 text-teal-800',
        'closed_mediation' => 'bg-lime-100 text-lime-800',
      ];

      // Label singkat untuk tampilan tabel (full label tampil di tooltip)
      $shortStatusLabels = [
        'submitted'        => 'Diajukan',
        'in_review'        => 'Ditinjau',
        'follow_up'        => 'Ditindaklanjuti',
        'closed'           => 'Selesai',
        'closed_pa'        => 'Selesai — PA',
        'closed_pn'        => 'Selesai — PN',
        'closed_mediation' => 'Selesai — Mediasi',
      ];

        $userId = Auth::id();

        $complaints = Complaint::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        $recent = Complaint::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.user.complaints.index', compact('complaints', 'recent', 'statusLabels', 'statusClasses', 'shortStatusLabels'));
    }

    /**
     * Form buat pengaduan.
     */
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

        // Siapkan tree wilayah jika paket Laravolt tersedia
        $provinceTree = collect();
        if (class_exists(Province::class)) {
            $provinceTree = Province::query()
                ->with([
                    'cities' => fn ($q) => $q->select('id','code','name','province_code')->orderBy('name'),
                    'cities.districts' => fn ($q) => $q->select('id','code','name','city_code')->orderBy('name'),
                ])
                ->select('id','code','name')
                ->orderBy('name')
                ->get()
                ->map(fn ($p) => [
                    'code' => $p->code,
                    'name' => $p->name,
                    'regencies' => $p->cities->map(fn ($c) => [
                        'code' => $c->code,
                        'name' => $c->name,
                        'districts' => $c->districts->map(fn ($d) => [
                            'code' => $d->code,
                            'name' => $d->name,
                        ])->values(),
                    ])->values(),
                ])->values();
        }

        return view('dashboard.user.complaints.create', compact('categories', 'provinceTree'));
    }

    /**
     * Simpan pengaduan baru.
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $this->authorize('create', Complaint::class);

        $data = $request->validated();
        unset($data['user_id'], $data['code'], $data['status'], $data['admin_note']);
        $data['status'] = Complaint::STATUS_SUBMITTED;

        try {
            Complaint::create($data);
            return redirect()->route('complaints.index')
                ->with('status', 'Pengaduan berhasil dikirim.');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pengaduan', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan saat menyimpan pengaduan. Silakan coba lagi.',
            ]);
        }
    }
    /**
     * Detail pengaduan (owner atau admin/super-admin).
     */
   public function show(Complaint $complaint): View
    {
        $this->authorize('view', $complaint);
        $statusLabels = Complaint::statusLabels();

        $badge = match ($complaint->status) {
            'submitted'        => 'bg-slate-100 text-slate-700',
            'in_review'        => 'bg-amber-100 text-amber-800',
            'follow_up'        => 'bg-blue-100 text-blue-800',
            'closed'           => 'bg-emerald-100 text-emerald-800',
            'closed_pa'        => 'bg-emerald-100 text-emerald-800',
            'closed_pn'        => 'bg-teal-100 text-teal-800',
            'closed_mediation' => 'bg-lime-100 text-lime-800',
            default            => 'bg-slate-100 text-slate-800 ring-slate-200',
        };

        // Kode & tanggal
        $displayCode   = $complaint->code ?? $complaint->id;
        $createdAtText = optional($complaint->created_at)?->translatedFormat('d F Y H:i') ?? '—';
        $updatedAtText = optional($complaint->updated_at)?->translatedFormat('d F Y H:i') ?? '—';

        // Data ringkas untuk view (supaya Blade bersih)
        $reporterName  = $complaint->reporter_name ?: (optional($complaint->user)->name ?? '—');
        $reporterPhone = $complaint->reporter_phone ?: '—';
        $reporterAge   = $complaint->reporter_age;
        $reporterJob   = $complaint->reporter_job ?: '—';
        $disabilityDisplay = is_null($complaint->reporter_is_disability) ? '—'
            : ($complaint->reporter_is_disability ? 'Ya' : 'Tidak');

        $provinceName = $complaint->province_name ?? '—';
        $cityName     = $complaint->city_name ?? ($complaint->regency_name ?? '—');
        $districtName = $complaint->district_name ?? '—';
        $addressLine  = $complaint->reporter_address ?: '—';

        $perpetratorName = $complaint->perpetrator_name ?: '—';
        $perpetratorAge  = $complaint->perpetrator_age;
        $perpetratorJob  = $complaint->perpetrator_job ?: '—';

        $reporterRows = [
            ['Nama',        $reporterName],
            ['Nomor HP',    $reporterPhone],
            ['Umur',        $reporterAge ?? '—'],
            ['Disabilitas', $disabilityDisplay],
            ['Pekerjaan',   $reporterJob],
        ];

        $locationRows = [
            ['Provinsi',   $provinceName],
            ['Kota/Kab.',  $cityName],
            ['Kecamatan',  $districtName],
            ['Alamat',     $addressLine],
        ];

        $perpetratorRows = array_values(array_filter([
            ['Nama',      $perpetratorName],
            $perpetratorAge !== null ? ['Umur', $perpetratorAge] : null,
            ['Pekerjaan', $perpetratorJob],
        ]));

        return view('dashboard.user.complaints.show', compact(
        'complaint','statusLabels','badge','displayCode','createdAtText','updatedAtText',
        'reporterRows','locationRows','perpetratorRows'
    ));
    }
}
