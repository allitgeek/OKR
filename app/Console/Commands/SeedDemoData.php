<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Team;
use App\Models\Category;
use App\Models\Objective;
use App\Models\KeyResult;
use Carbon\Carbon;

class SeedDemoData extends Command
{
    protected $signature = 'demo:seed';
    protected $description = 'Seed demo objectives and key results for analytics testing';

    public function handle()
    {
        $this->info('Starting demo data seeding...');

        // Ensure we have at least one user, team, and categories
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
            $this->info('Created demo user');
        }

        $team = Team::first();
        if (!$team) {
            $team = Team::create([
                'name' => 'Demo Team',
                'description' => 'A team for demo purposes',
                'leader_id' => $user->id,
            ]);
            $this->info('Created demo team');
        }

        // Create categories if they don't exist
        $categories = [];
        $categoryData = [
            ['name' => 'Revenue', 'slug' => 'revenue', 'description' => 'Revenue related objectives', 'color' => '#22C55E'],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'Customer satisfaction objectives', 'color' => '#3B82F6'],
            ['name' => 'Process', 'slug' => 'process', 'description' => 'Process improvement objectives', 'color' => '#F59E0B'],
            ['name' => 'Innovation', 'slug' => 'innovation', 'description' => 'Innovation and development objectives', 'color' => '#8B5CF6'],
            ['name' => 'Team', 'slug' => 'team', 'description' => 'Team development objectives', 'color' => '#EF4444'],
        ];

        foreach ($categoryData as $catData) {
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
            $categories[] = $category;
        }

        // Demo objectives data
        $objectivesData = [
            [
                'title' => 'Increase Q4 Revenue by 25%',
                'description' => 'Drive revenue growth through new customer acquisition and existing customer expansion.',
                'category' => $categories[0], // Revenue
                'key_results' => [
                    ['title' => 'Generate $500K new revenue', 'target' => 500000, 'current' => 125000], // 25%
                    ['title' => 'Acquire 50 new enterprise customers', 'target' => 50, 'current' => 25], // 50%
                    ['title' => 'Achieve 95% customer retention rate', 'target' => 95, 'current' => 95], // 100%
                ]
            ],
            [
                'title' => 'Improve Customer Satisfaction Score',
                'description' => 'Enhance customer experience and support to achieve industry-leading satisfaction scores.',
                'category' => $categories[1], // Customer
                'key_results' => [
                    ['title' => 'Reach NPS score of 70', 'target' => 70, 'current' => 17.5], // 25%
                    ['title' => 'Reduce support ticket response time to 2 hours', 'target' => 2, 'current' => 3], // 50% (inverse metric)
                    ['title' => 'Achieve 99% uptime', 'target' => 99, 'current' => 99], // 100%
                ]
            ],
            [
                'title' => 'Streamline Product Development Process',
                'description' => 'Optimize development workflows to increase delivery speed and quality.',
                'category' => $categories[2], // Process
                'key_results' => [
                    ['title' => 'Reduce average deployment time to 30 minutes', 'target' => 30, 'current' => 67.5], // 25% (inverse)
                    ['title' => 'Increase automated test coverage to 90%', 'target' => 90, 'current' => 45], // 50%
                    ['title' => 'Achieve zero critical bugs in production', 'target' => 0, 'current' => 0], // 100%
                ]
            ],
            [
                'title' => 'Launch AI-Powered Analytics Platform',
                'description' => 'Develop and launch innovative analytics platform with AI capabilities.',
                'category' => $categories[3], // Innovation
                'key_results' => [
                    ['title' => 'Complete 4 core AI features', 'target' => 4, 'current' => 1], // 25%
                    ['title' => 'Onboard 100 beta testers', 'target' => 100, 'current' => 50], // 50%
                    ['title' => 'Achieve 85% user satisfaction in beta', 'target' => 85, 'current' => 85], // 100%
                ]
            ],
            [
                'title' => 'Build High-Performance Team Culture',
                'description' => 'Foster team collaboration, skill development, and employee satisfaction.',
                'category' => $categories[4], // Team
                'key_results' => [
                    ['title' => 'Achieve 90% employee satisfaction score', 'target' => 90, 'current' => 22.5], // 25%
                    ['title' => 'Complete 100% of team training programs', 'target' => 100, 'current' => 50], // 50%
                    ['title' => 'Maintain 100% team retention rate', 'target' => 100, 'current' => 100], // 100%
                ]
            ],
        ];

        // Delete existing demo objectives to avoid duplicates
        Objective::where('title', 'like', '%Q4 Revenue%')
            ->orWhere('title', 'like', '%Customer Satisfaction%')
            ->orWhere('title', 'like', '%Product Development%')
            ->orWhere('title', 'like', '%AI-Powered Analytics%')
            ->orWhere('title', 'like', '%High-Performance Team%')
            ->delete();

        $this->info('Deleted existing demo objectives');

        // Create objectives and key results
        foreach ($objectivesData as $index => $objData) {
            $objective = Objective::create([
                'title' => $objData['title'],
                'description' => $objData['description'],
                'user_id' => $user->id,
                'team_id' => $team->id,
                'creator_id' => $user->id,
                'start_date' => Carbon::now()->startOfQuarter(),
                'end_date' => Carbon::now()->endOfQuarter(),
                'status' => 'in_progress',
                'time_period' => 'quarterly',
                'progress' => 0, // Will be calculated from key results
            ]);

            // Attach category
            $objective->categories()->attach($objData['category']);

            $this->info("Created objective: {$objData['title']}");

            // Create key results
            foreach ($objData['key_results'] as $krIndex => $krData) {
                $keyResult = KeyResult::create([
                    'title' => $krData['title'],
                    'description' => "Key result for {$objData['title']}",
                    'objective_id' => $objective->id,
                    'owner_id' => $user->id,
                    'target_value' => $krData['target'],
                    'current_value' => $krData['current'],
                    'metric_unit' => $this->getMetricUnit($krData['title']),
                    'progress' => 0, // Will be calculated
                    'status' => 'in_progress',
                    'start_date' => $objective->start_date,
                    'due_date' => $objective->end_date,
                ]);

                // Calculate progress based on current vs target
                $keyResult->calculateProgressWithoutEvents();

                $progress = $keyResult->progress;
                $status = $progress == 100 ? 'completed' : ($progress > 0 ? 'in_progress' : 'not_started');
                
                KeyResult::where('id', $keyResult->id)->update([
                    'status' => $status
                ]);

                $this->info("  - Created KR: {$krData['title']} ({$progress}% complete)");
            }

            // Update objective progress
            $objective->calculateProgressWithoutEvents();
            $objective->refresh();
            
            $objProgress = $objective->progress;
            $objStatus = $objProgress == 100 ? 'completed' : ($objProgress > 0 ? 'in_progress' : 'not_started');
            
            Objective::where('id', $objective->id)->update([
                'status' => $objStatus
            ]);

            $this->info("  Objective progress: {$objProgress}%");
            $this->newLine();
        }

        $this->info('Demo data seeding completed successfully!');
        $this->info('You can now test the analytics dashboard with realistic data.');
        
        return 0;
    }

    private function getMetricUnit($title)
    {
        if (strpos(strtolower($title), 'revenue') !== false || strpos($title, '$') !== false) {
            return 'currency';
        } elseif (strpos(strtolower($title), 'customer') !== false || strpos(strtolower($title), 'user') !== false) {
            return 'count';
        } elseif (strpos(strtolower($title), 'time') !== false || strpos(strtolower($title), 'hour') !== false || strpos(strtolower($title), 'minute') !== false) {
            return 'time';
        } elseif (strpos(strtolower($title), '%') !== false || strpos(strtolower($title), 'percent') !== false || strpos(strtolower($title), 'rate') !== false) {
            return 'percentage';
        } elseif (strpos(strtolower($title), 'feature') !== false || strpos(strtolower($title), 'bug') !== false) {
            return 'count';
        } else {
            return 'percentage';
        }
    }
} 