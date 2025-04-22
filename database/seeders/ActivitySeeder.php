<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (User::all() as $user) {
            for ($i = 0; $i < rand(5, 20); $i++) {
                Activity::create([
                    'user_id' => $user->id,
                    'activity_date' => now()->subDays(rand(0, 365)),
                    'points' => 20,
                ]);
            }
        }
    }
}
