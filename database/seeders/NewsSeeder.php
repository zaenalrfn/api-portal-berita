<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            News::create([
                'user_id' => 1, // admin pembuat berita
                'title' => "Berita Contoh $i",
                'slug' => Str::slug("Berita Contoh $i"),
                'content' => "Ini adalah isi lengkap berita contoh nomor $i.",
                'thumbnail' => null,
                'published_at' => now(),
            ]);
        }
    }
}
