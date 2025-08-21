<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'Whispers in the Wind',
            'Echoes of Eternity',
            'The Last Voyage',
            'Crimson Skies',
            'Tales from the Riverbank',
            'Shattered Dreams',
            'City of Lanterns',
            'Paths Untold',
            'Garden of Shadows',
            'Beneath the Ice',
            'Threads of Fate',
            'Rise of the Phoenix',
            'The Forgotten Realm',
            'Winds of Change',
            'Labyrinth of Souls',
            'Moonlight Sonata',
            'Voices of the Deep',
            'Crown of Ash',
            'Starlit Journey',
            'Embers and Ashes'
        ];

        $books = [];

        foreach ($titles as $index => $title) {
            $books[] = [
                'title' => $title,
                'slug' => Str::slug($title),
                'category_id' => rand(1, 3),
                'author' => 'Author ' . chr(65 + $index),
                'description' => "An engaging story of {$title} filled with mystery, emotion, and unforgettable moments.",
                'isbn' => '9780000000' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'cover_image' => "https://picsum.photos/seed/book{$index}/200/300",
                'price' => rand(10, 30),
                'stock' => rand(0, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('books')->insert($books);
    }
}
