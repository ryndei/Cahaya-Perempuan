<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','can:news.manage']);
    }

    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));

        $items = News::query()
            ->when($q !== '', fn($w) => $w->where('title','like',"%{$q}%")->orWhere('excerpt','like',"%{$q}%"))
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        return view('dashboard.admin.news.index', compact('items','q'));
    }

    public function create(): View
    {
        $item = new News([
            'status' => 'draft',
            'published_at' => now(),
        ]);
        return view('dashboard.admin.news.create', compact('item'));
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $news = new News($data);
        $news->user_id = $request->user()->id;
        $news->slug = News::makeUniqueSlug($data['title']);
        $news->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['body']), 180);

        if ($request->hasFile('cover')) {
            $news->cover_path = $request->file('cover')->store('news', 'public');
        }

        // default published_at kalau published dan belum diisi
        if ($news->status === 'published' && empty($news->published_at)) {
            $news->published_at = now();
        }

        $news->save();

        return redirect()->route('admin.news.index')->with('status','Berita berhasil dibuat.');
    }

    public function edit(News $news): View
    {
        return view('dashboard.admin.news.edit', ['item' => $news]);
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $data = $request->validated();

        // slug update bila title berubah
        if ($news->title !== $data['title']) {
            $news->slug = News::makeUniqueSlug($data['title']);
        }

        $news->fill($data);
        $news->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['body']), 180);

        if ($request->hasFile('cover')) {
            $news->cover_path = $request->file('cover')->store('news', 'public');
        }

        if ($news->status === 'published' && empty($news->published_at)) {
            $news->published_at = now();
        }

        $news->save();

        return back()->with('status','Berita diperbarui.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();
        return back()->with('status','Berita dihapus.');
    }
}
