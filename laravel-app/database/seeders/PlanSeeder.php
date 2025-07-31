<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic Plan',
            'description' => 'Basic subscription plan',
            'price' => 9.99,
            'currency' => 'USD',
        ]);

        Plan::create([
            'name' => 'Pro Plan',
            'description' => 'Pro subscription plan',
            'price' => 19.99,
            'currency' => 'USD',
        ]);

        Plan::create([
            'name' => 'Enterprise Plan',
            'description' => 'Enterprise subscription plan',
            'price' => 49.99,
            'currency' => 'USD',
        ]);
    }
}
