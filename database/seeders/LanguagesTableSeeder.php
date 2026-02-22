<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::create([
            'name' => 'English',
            'code' => 'en',
        ]);

        Language::create([
            'name' => 'Arabic',
            'code' => 'ar',
            'is_active' => 0
        ]);
    }
}