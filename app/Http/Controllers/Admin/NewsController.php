<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class NewsController extends Controller
{
    private const PAGINATION_LIMIT = 15;
    private const CACHE_VERSION_KEY = 'news_cache_ver';

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:news.manage']);
    }

    public function index(Request $request): View
    {
        $q      = trim((string) $request->get('q', ''));
        $status = $request->get('status', '');
        $page   = (int) $request->get('page', 1);

        // Versioned cache key (driver-agnostic, tidak perlu tagging)
        $ver = Cache::rememberForever(self::CACHE_VERSION_KEY, fn () => 1);
        $key = "news:v{$ver}:q:{$q}:st:{$status}:p:{$page}";

        $items = Cache::remember(
            $key,
            app()->isProduction() ? 300 : 0,
            function () use ($q, $status) {
                return News::query()
                    ->with('user:id,name')
                    ->when($q !== '', fn ($x) => $x->where(fn ($y) =>
                        $y->where('title', 'like', "%{$q}%")
                          ->orWhere('excerpt', 'like', "%{$q}%")
                          ->orWhere('body', 'like', "%{$q}%")
                    ))
                    ->when($status !== '', fn ($x) => $x->where('status', $status))
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->paginate(self::PAGINATION_LIMIT);
            }
        );

        return view('dashboard.admin.news.index', [
            'items'        => $items,
            'searchQuery'  => $q,
            'statusFilter' => $status,
        ]);
    }

    public function create(): View
    {
        $item = new News([
            'status'           => 'draft',
            'published_at'     => now(),
            'meta_title'       => '',
            'meta_description' => '',
        ]);

        return view('dashboard.admin.news.create', compact('item'));
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $data          = $request->validated();
        $news          = new News($data);
        $news->user_id = $request->user()->id;
        $news->slug    = News::makeUniqueSlug($data['title']);
        $news->excerpt = $this->makeExcerpt($data);

        if ($request->hasFile('cover')) {
            $news->cover_path = $this->processAndStoreImage($request->file('cover'));
        }

        if ($news->status === 'published' && empty($news->published_at)) {
            $news->published_at = now();
        }

        $news->save();
        $this->clearNewsCaches();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dibuat.');
    }

    public function edit(News $news): View
    {
        $news->load('user:id,name');
        $item = $news; // view memakai $item
        return view('dashboard.admin.news.edit', compact('item'));
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $data         = $request->validated();
        $oldCoverPath = $news->cover_path;

        if ($news->title !== $data['title']) {
            $news->slug = News::makeUniqueSlug($data['title']);
        }

        $news->fill($data);
        $news->excerpt = $this->makeExcerpt($data);

        if ($request->hasFile('cover')) {
            $news->cover_path = $this->processAndStoreImage($request->file('cover'));
            if ($oldCoverPath) {
                Storage::disk('public')->delete($oldCoverPath);
            }
        }

        if ($news->status === 'published' && empty($news->published_at)) {
            $news->published_at = now();
        }

        $news->save();
        $this->clearNewsCaches();

        return back()->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $oldCoverPath = $news->cover_path;
        $news->delete();
        if ($oldCoverPath) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        $this->clearNewsCaches();
        return back()->with('success', 'Berita berhasil dihapus.');
    }

    // ===== Helpers =====

    private function makeExcerpt(array $data): string
    {
        if (!empty($data['excerpt'])) {
            return $data['excerpt'];
        }
        return \Illuminate\Support\Str::limit(trim(strip_tags($data['body'] ?? '')), 160);
    }

    private function processAndStoreImage($uploaded): string
    {
        $fileName    = 'news-' . Str::uuid() . '.webp';
        $storagePath = 'news/' . $fileName;

        $img = Image::read($uploaded)
            ->resize(1200, 800, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            })
            ->encode(new WebpEncoder(quality: 75));

        Storage::disk('public')->put($storagePath, (string) $img);

        return $storagePath;
    }

    private function clearNewsCaches(): void
    {
        // Bump version → seluruh key "news:v{ver}:*" otomatis basi tanpa butuh tags
        if (Cache::has(self::CACHE_VERSION_KEY)) {
            Cache::increment(self::CACHE_VERSION_KEY);
        } else {
            Cache::forever(self::CACHE_VERSION_KEY, 1);
        }
    }
}
