<?php

namespace Modules\Languages\Seeders;

use Illuminate\Database\Seeder;
use Modules\Languages\Models\Language;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'slug' => 'en',
                'direction' => 'ltr',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                // arabic
                'name' => 'Arabic',
                'slug' => 'ar',
                'direction' => 'rtl',
                'is_default' => false,
                'is_active' => true,
            ]
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
