<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@varashherbal.com'], [
            'name' => 'Super Admin',
            'password' => 'password',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->call([
            SettingSeeder::class,
            CatalogSeeder::class,
            ContentSeeder::class,
        ]);

        // Log dari proses seeding tidak relevan untuk admin
        ActivityLog::truncate();
        ActivityLogger::log('seeded', 'Database diinisialisasi dengan data awal', logName: 'system');
    }
}
