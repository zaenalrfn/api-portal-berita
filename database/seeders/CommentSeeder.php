<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run()
    {
        // User id=2 komentar di berita id=1
        Comment::create([
            'news_id' => 1,
            'user_id' => 2,
            'comment' => 'Artikel yang sangat menarik!',
        ]);

        Comment::create([
            'news_id' => 1,
            'user_id' => 2,
            'comment' => 'Terima kasih sudah berbagi informasinya.',
        ]);
    }
}
