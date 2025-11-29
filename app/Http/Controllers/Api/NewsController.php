<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function __construct()
    {
        // index & show publik; store/update/destroy butuh autentikasi Passport (auth:api)
        $this->middleware('auth:api')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $news = News::with('user', 'comments.user')->latest('created_at')->paginate(10);

        return response()->json([
            'message' => 'Daftar berita',
            'status' => true,
            'data' => $news
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            $news = new News();
            $news->user_id = Auth::id();
            $news->title = $data['title'];
            $news->slug = $this->makeUniqueSlug($data['title']);
            $news->content = $data['content'];
            $news->published_at = $data['published_at'] ?? now();

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('thumbnails', 'public');
                $news->thumbnail = $path;
            }

            $news->save();

            // load relations for response
            $news->load('comments.user');

            return response()->json([
                'message' => 'Berita berhasil dibuat',
                'status' => true,
                'data' => $news
            ], 201);
        } catch (\Exception $e) {
            // jangan expose stacktrace di production; log jika perlu
            return response()->json([
                'message' => 'Gagal membuat berita',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = News::with('user', 'comments.user')->find($id);

        if (! $news) {
            return response()->json([
                'message' => 'Berita tidak ditemukan',
                'status' => false
            ], 404);
        }

        return response()->json([
            'message' => 'Detail berita',
            'status' => true,
            'data' => $news
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $news = News::find($id);

        if (! $news) {
            return response()->json([
                'message' => 'Berita tidak ditemukan',
                'status' => false
            ], 404);
        }

        // Authorization: owner or admin
        $user = $request->user();
        $isOwner = $user->id === $news->user_id;
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');

        if (! $isOwner && ! $isAdmin) {
            return response()->json([
                'message' => 'Tidak memiliki izin untuk mengubah berita ini',
                'status' => false
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            if (isset($data['title']) && $data['title'] !== $news->title) {
                $news->title = $data['title'];
                $news->slug = $this->makeUniqueSlug($data['title'], $news->id);
            }

            if (isset($data['content'])) {
                $news->content = $data['content'];
            }

            if (array_key_exists('published_at', $data)) {
                $news->published_at = $data['published_at'];
            }

            if ($request->hasFile('thumbnail')) {
                // delete old thumbnail if exists
                if ($news->thumbnail && Storage::disk('public')->exists($news->thumbnail)) {
                    Storage::disk('public')->delete($news->thumbnail);
                }
                $path = $request->file('thumbnail')->store('thumbnails', 'public');
                $news->thumbnail = $path;
            }

            $news->save();

            $news->load('comments.user');

            return response()->json([
                'message' => 'Berita berhasil diperbarui',
                'status' => true,
                'data' => $news
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui berita',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::find($id);

        if (! $news) {
            return response()->json([
                'message' => 'Berita tidak ditemukan',
                'status' => false
            ], 404);
        }

        // Authorization: owner or admin
        $user = Auth::user();
        $isOwner = $user->id === $news->user_id;
        $isAdmin = $user->hasRole('admin');

        if (! $isOwner && ! $isAdmin) {
            return response()->json([
                'message' => 'Tidak memiliki izin untuk menghapus berita ini',
                'status' => false
            ], 403);
        }

        try {
            // delete thumbnail file if exists
            if ($news->thumbnail && Storage::disk('public')->exists($news->thumbnail)) {
                Storage::disk('public')->delete($news->thumbnail);
            }

            $news->delete();

            return response()->json([
                'message' => 'Berita berhasil dihapus',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus berita',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a unique slug for the news.
     * If slug exists, append numeric suffix.
     */
    protected function makeUniqueSlug(string $title, int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (true) {
            $query = News::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                return $slug;
            }

            $slug = $base . '-' . $i++;
        }
    }
}
