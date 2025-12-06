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
    /**
     * @OA\Get(
     * path="/api/news",
     * tags={"News"},
     * summary="Dapatkan semua berita",
     * description="Mengambil daftar berita terbaru (Public)",
     * @OA\Response(response="200", description="List Berita berhasil diambil")
     * )
     */
    public function index(Request $request)
    {
        $news = News::with('user', 'comments.user')->latest('created_at')->paginate(10);

        $news->getCollection()->transform(function ($item) {
            $item->thumbnail_url = $item->thumbnail
                ? config('app.url') . '/storage/' . $item->thumbnail
                : null;
            return $item;
        });

        return response()->json([
            'message' => 'Daftar berita',
            'status' => true,
            'data' => $news
        ], 200);
    }

    public function getUserNews(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => false,
            ], 401);
        }

        $perPage = (int) $request->query('per_page', 5);
        $perPage = $perPage > 0 ? $perPage : 5;

        // Query news milik user, terbaru dulu
        $query = News::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        $paginated = $query->paginate($perPage);

        // Map tiap item supaya thumbnail jadi URL lengkap (jika ada)
        $items = $paginated->getCollection()->map(function (News $n) {
            $thumb = null;
            if (!empty($n->thumbnail)) {
                // jika kamu menyimpan 'thumbnails/filename.png' di storage/app/public
                // Storage::url akan menghasilkan '/storage/thumbnails/filename.png'
                $thumb = Storage::url($n->thumbnail); // contoh "/storage/thumbnails/..."
                // Jika perlu full URL (include app url), kamu bisa gunakan asset():
                $thumb = asset($thumb);
            }

            return [
                'id' => $n->id,
                'user_id' => $n->user_id,
                'title' => $n->title,
                'slug' => $n->slug,
                'content' => $n->content,
                'thumbnail' => $thumb,
                'created_at' => $n->created_at ? $n->created_at->toDateTimeString() : null,
                'updated_at' => $n->updated_at ? $n->updated_at->toDateTimeString() : null,
            ];
        })->toArray();

        $response = [
            'message' => 'Daftar berita pengguna',
            'status' => true,
            'data' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'data' => $items,
            ],
        ];

        return response()->json($response);
    }

    public function getTotalUserNews(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => false,
            ], 401);
        }
        $totalUserNews = News::where('user_id', $user->id)->count();
        return response()->json([
            'message' => 'Total berita pengguna',
            'status' => true,
            'total' => $totalUserNews
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     * path="/api/news",
     * tags={"News"},
     * summary="Buat berita baru",
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="title", type="string", example="Judul Berita Heboh"),
     * @OA\Property(property="content", type="string", example="Isi konten berita..."),
     * @OA\Property(property="image", type="string", format="binary", description="File gambar upload"),
     * )
     * )
     * ),
     * @OA\Response(response="201", description="Berita berhasil dibuat"),
     * @OA\Response(response="401", description="Unauthenticated")
     * )
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
    /**
     * @OA\Get(
     * path="/api/news/{id}",
     * tags={"News"},
     * summary="Lihat detail berita",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID Berita",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response="200", description="Detail Berita")
     * )
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
