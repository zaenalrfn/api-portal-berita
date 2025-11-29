<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with('comments.user')->latest('created_at')->paginate(10);
        return response()->json([
            'message' => 'Daftar berita',
            'status' => true,
            'data' => $news
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required',
                'content' => 'required',
                'thumbnail' => 'nullable|image|max:2048',
                'published_at' => 'nullable|date',
            ]);
            $news = new News();
            $news->user_id = Auth::id();
            $news->title = $data['title'];
            $news->slug = Str::slug($data['title']);
            $news->content = $data['content'];
            if (isset($data['published_at'])) {
                $news->published_at = $data['published_at'];
            } else {
                $news->published_at = now();
            }
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('thumbnails', 'public');
                $news->thumbnail = $path;
            }
            $news->save();

            return response()->json([
                'message' => 'Berita berhasil dibuat',
                'status' => true,
            ], 201);
        } catch (\Exception $e) {
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
        $news = News::with('comments.user')->findOrFail($id);
        return response()->json([
            'message' => 'Detail berita',
            'status' => true,
            'data' => $news
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'title' => 'sometimes|required',
                'content' => 'sometimes|required',
                'thumbnail' => 'nullable|image|max:2048',
                'published_at' => 'nullable|date',
            ]);

            $news = News::findOrFail($id);

            if (isset($data['title'])) {
                $news->title = $data['title'];
                $news->slug = Str::slug($data['title']);
            }
            if (isset($data['content'])) {
                $news->content = $data['content'];
            }
            if (isset($data['published_at'])) {
                $news->published_at = $data['published_at'];
            }
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('thumbnails', 'public');
                $news->thumbnail = $path;
            }

            $news->save();

            return response()->json([
                'message' => 'Berita berhasil diperbarui',
                'status' => true,
            ]);
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
        try {
            $news = News::findOrFail($id);
            $news->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui berita',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
