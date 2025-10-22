<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(): View
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->latest()->paginate(10);

        return view('dashboard.user.complaints.index', compact('complaints'));
    }

    public function create(): View
    {
        return view('dashboard.user.complaints.create');
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')
                ->store('complaints', 'public');
        }

        Complaint::create($data); // user_id & code terisi otomatis

        return redirect()->route('complaints.index')
            ->with('status', 'Pengaduan berhasil dikirim.');
    }

    public function show(Complaint $complaint): View
    {
        abort_if(
            $complaint->user_id !== Auth::id()
            && ! (Auth::check() && Auth::user()->hasAnyRole(['admin','super-admin'])),
            403
        );

        return view('dashboard.user.complaints.show', compact('complaint'));
    }
}
