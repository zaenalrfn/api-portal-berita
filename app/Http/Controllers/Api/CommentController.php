<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct()
    {
        // Hanya store, update, destroy yang perlu login
        $this->middleware('auth:api')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Comment::with('user');

        if ($request->has('news_id')) {
            $query->where('news_id', $request->news_id);
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/comments",
     * tags={"Comments"},
     * summary="Kirim Komentar",
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="news_id", type="integer", example=1),
     * @OA\Property(property="body", type="string", example="Wah artikelnya bagus!")
     * )
     * ),
     * @OA\Response(response="201", description="Komentar terkirim")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'news_id' => 'required|integer|exists:news,id',
            'comment' => 'required|string|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::create([
            'news_id' => $request->news_id,
            'user_id' => $request->user()->id,
            'comment' => $request->comment
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dibuat',
            'data' => $comment
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $comment = Comment::with('user')->find($id);

        if (! $comment) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak ditemukan'
            ], 404);
        }

        // cek policy (owner atau admin)
        $this->authorize('update', $comment);

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comment->comment = $request->comment;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil diperbarui',
            'data' => $comment
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak ditemukan'
            ], 404);
        }

        // policy
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus'
        ]);
    }
}
