<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $projectsData = [
            [
                'name' => 'بناء مسجد',
                'description' => 'مشروع لبناء مسجد متكامل لخدمة المجتمع المحلي.',
                'target_amount' => 50000,
                'collected_amount' => 15000,
                'progress' => 30,
                'status' => 'in_progress',
                'image' => null,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->addMonths(4)->toDateString(),
            ],
            [
                'name' => 'حفر بئر',
                'description' => 'تأمين مصدر مياه نظيف لقرية محتاجة.',
                'target_amount' => 20000,
                'collected_amount' => 20000,
                'progress' => 100,
                'status' => 'completed',
                'image' => null,
                'start_date' => now()->subMonths(4)->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
            ],
            [
                'name' => 'كفالة يتيم',
                'description' => 'دعم شامل للأطفال الأيتام بالرعاية والتعليم.',
                'target_amount' => 15000,
                'collected_amount' => 5000,
                'progress' => 33.33,
                'status' => 'in_progress',
                'image' => null,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
            ],
        ];

        foreach ($projectsData as $data) {
            Project::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        $donations = [
            ['project_name' => 'بناء مسجد', 'donor_name' => 'أحمد', 'amount' => 5000, 'is_anonymous' => false, 'payment_method' => 'bank'],
            ['project_name' => 'بناء مسجد', 'donor_name' => null, 'amount' => 2000, 'is_anonymous' => true, 'payment_method' => 'cash'],
            ['project_name' => 'حفر بئر', 'donor_name' => 'سارة', 'amount' => 5000, 'is_anonymous' => false, 'payment_method' => 'online'],
            ['project_name' => 'كفالة يتيم', 'donor_name' => 'محمد', 'amount' => 1500, 'is_anonymous' => false, 'payment_method' => 'bank'],
            ['project_name' => 'كفالة يتيم', 'donor_name' => null, 'amount' => 2500, 'is_anonymous' => true, 'payment_method' => 'cash'],
        ];

        foreach ($donations as $donation) {
            $project = Project::where('name', $donation['project_name'])->first();
            if (! $project) {
                continue;
            }

            Donation::create([
                'project_id' => $project->id,
                'donor_name' => $donation['donor_name'],
                'amount' => $donation['amount'],
                'is_anonymous' => $donation['is_anonymous'],
                'payment_method' => $donation['payment_method'],
            ]);

            $project->collected_amount += $donation['amount'];
            $project->progress = round(($project->collected_amount / $project->target_amount) * 100, 2);
            if ($project->progress >= 100) {
                $project->status = 'completed';
            } elseif ($project->progress > 0) {
                $project->status = 'in_progress';
            }
            $project->progress = min($project->progress, 100);
            $project->save();
        }
    }
}
