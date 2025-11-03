<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;

class NewsController extends Controller
{
    private const PAGINATION_LIMIT  = 15;
    private const CACHE_VERSION_KEY = 'news_cache_ver';

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:news.manage']);
    }

    /**
     * List berita (dengan pencarian, filter status, dan cache versi).
     */
    public function index(Request $request): View
    {
        $q      = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $page   = (int) $request->get('page', 1);

        // Versi cache untuk invalidasi massal tanpa cache tags
        $ver = Cache::rememberForever(self::CACHE_VERSION_KEY, static fn () => 1);
        $key = "news:v{$ver}:q:{$q}:st:{$status}:p:{$page}";

        $ttlSeconds = app()->isProduction() ? 300 : 0;

        if ($ttlSeconds > 0) {
            $items = Cache::remember($key, $ttlSeconds, function () use ($q, $status) {
                return $this->buildIndexQuery($q, $status)->paginate(self::PAGINATION_LIMIT);
            });
        } else {
            // Tanpa cache di non-production agar development lebih nyaman
            $items = $this->buildIndexQuery($q, $status)->paginate(self::PAGINATION_LIMIT);
        }

        return view('dashboard.admin.news.index', [
            'items'        => $items,
            'searchQuery'  => $q,
            'statusFilter' => $status,
        ]);
    }

    /**
     * Form create.
     */
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

    /**
     * Simpan berita baru.
     */
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

        // Hanya 2 status: draft & published
        if ($news->status === 'draft') {
            $news->published_at = null;
        } else { // published
            $news->published_at = $news->published_at ? Carbon::parse($news->published_at) : now();
        }

        $news->save();
        $this->clearNewsCaches();

        // Alert sesuai status
        if ($news->status === 'draft') {
            Alert::info('Draft', 'Berita berhasil disimpan ke draft.');
        } else {
            Alert::success('Sukses', 'Berita berhasil dibuat.');
        }

        return redirect()->route('admin.news.index');
    }

    /**
     * Form edit.
     */
    public function edit(News $news): View
    {
        $news->load('user:id,name');
        $item = $news;

        return view('dashboard.admin.news.edit', compact('item'));
    }

    /**
     * Update berita.
     */
    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $data         = $request->validated();
        $oldCoverPath = $news->cover_path;

        if (!isset($data['title']) || trim($data['title']) === '') {
            // Pastikan title tetap ada; validator mestinya sudah menangani ini.
            $data['title'] = $news->title;
        }

        // Update slug bila judul berubah
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

        // Normalisasi status & published_at
        if ($news->status === 'draft') {
            $news->published_at = null;
        } else { // published
            $news->published_at = $news->published_at ? Carbon::parse($news->published_at) : now();
        }

        $news->save();
        $this->clearNewsCaches();

        if ($news->status === 'draft') {
            Alert::info('Draft', 'Perubahan disimpan sebagai draft.');
        } else {
            Alert::success('Sukses', 'Berita berhasil diperbarui.');
        }

        return back();
    }

    /**
     * Hapus berita.
     */
    public function destroy(News $news): RedirectResponse
    {
        $oldCoverPath = $news->cover_path;

        $news->delete();

        if ($oldCoverPath) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        $this->clearNewsCaches();

        Alert::success('Sukses', 'Berita berhasil dihapus.');

        return back();
    }

    // ================== Helpers ==================

    /**
     * Query builder untuk index (apply search & status).
     */
    private function buildIndexQuery(string $q, string $status)
    {
        return News::query()
            ->with('user:id,name')
            ->when($q !== '', fn ($x) => $x->where(function ($y) use ($q) {
                $y->where('title', 'like', "%{$q}%")
                  ->orWhere('excerpt', 'like', "%{$q}%")
                  ->orWhere('body', 'like', "%{$q}%");
            }))
            ->when($status !== '', fn ($x) => $x->where('status', $status))
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /**
     * Generate excerpt dari body/excerpt input.
     */
    private function makeExcerpt(array $data): string
    {
        if (!empty($data['excerpt'])) {
            return (string) Str::of($data['excerpt'])->squish();
        }

        return (string) Str::of($data['body'] ?? '')
            ->stripTags()
            ->squish()
            ->limit(160);
    }

    /**
     * Proses dan simpan cover sebagai WEBP 1200x800 (fit, aspect ratio, upsize).
     */
    private function processAndStoreImage(UploadedFile $uploaded): string
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

    /**
     * Invalidate seluruh cache list berita dengan menaikkan versi cache.
     */
    private function clearNewsCaches(): void
    {
        if (Cache::has(self::CACHE_VERSION_KEY)) {
            Cache::increment(self::CACHE_VERSION_KEY);
        } else {
            Cache::forever(self::CACHE_VERSION_KEY, 1);
        }
    }
}
