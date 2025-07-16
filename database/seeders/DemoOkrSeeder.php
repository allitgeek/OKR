<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Objective;
use App\Models\KeyResult;
use App\Models\OkrCycle;
use Carbon\Carbon;

class DemoOkrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $this->command->error('Admin user not found. Please run UserSeeder first.');
            return;
        }

        // Get or create the current OKR cycle
        $cycle = OkrCycle::first();
        if (!$cycle) {
            $now = Carbon::now();
            $cycle = OkrCycle::create([
                'name' => "Q{$now->quarter}-{$now->year}",
                'year' => $now->year,
                'quarter' => $now->quarter,
                'start_date' => $now->startOfQuarter(),
                'end_date' => $now->endOfQuarter(),
                'status' => 'active',
            ]);
        }

        // Create 3 demo objectives
        for ($i = 1; $i <= 3; $i++) {
            $objective = Objective::create([
                'title' => "Demo Objective {$i}",
                'description' => "This is a description for demo objective {$i}.",
                'user_id' => $admin->id,
                'company_id' => $admin->company_id,
                'creator_id' => $admin->id,
                'level' => 'individual',
                'start_date' => $cycle->start_date,
                'end_date' => $cycle->end_date,
                'cycle_id' => $cycle->id,
                'okr_type' => 'committed',
            ]);

            // Create 3 demo key results for each objective
            for ($j = 1; $j <= 3; $j++) {
                KeyResult::create([
                    'objective_id' => $objective->id,
                    'title' => "Demo Key Result {$j} for Objective {$i}",
                    'description' => "This is a description for demo key result {$j}.",
                    'owner_id' => $admin->id,
                    'target_value' => 100,
                    'current_value' => rand(0, 100),
                    'metric_unit' => 'percent',
                    'kr_type' => 'positive',
                    'confidence_level' => (rand(5, 10) / 10),
                    'start_date' => $cycle->start_date,
                    'due_date' => $cycle->end_date,
                ]);
            }
        }
    }
}
