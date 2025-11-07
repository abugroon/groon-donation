<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedAdminUser();
        $projects = $this->seedProjects();
        $this->seedDonations($projects);
    }

    /**
     * Create the default administrator account.
     */
    private function seedAdminUser(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );
    }

    /**
     * Seed the projects table with demo records.
     *
     * @return array<string, Project>
     */
    private function seedProjects(): array
    {
        Storage::disk('public')->makeDirectory('projects');

        $placeholderImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=');

        $projectsData = [
            [
                'slug' => 'build-mosque',
                'name' => 'بناء مسجد',
                'description' => 'مشروع يهدف إلى بناء مسجد جديد لخدمة المجتمع المحلي.',
                'target_amount' => 50000,
                'start_date' => Carbon::now()->subMonths(2)->toDateString(),
                'end_date' => Carbon::now()->addMonths(4)->toDateString(),
            ],
            [
                'slug' => 'dig-well',
                'name' => 'حفر بئر',
                'description' => 'توفير مياه نظيفة لقرية محتاجة من خلال حفر بئر عميق.',
                'target_amount' => 20000,
                'start_date' => Carbon::now()->subMonth()->toDateString(),
                'end_date' => Carbon::now()->addMonths(2)->toDateString(),
            ],
            [
                'slug' => 'sponsor-orphan',
                'name' => 'كفالة يتيم',
                'description' => 'تأمين احتياجات طفل يتيم لمدة عام كامل.',
                'target_amount' => 12000,
                'start_date' => Carbon::now()->subWeeks(3)->toDateString(),
                'end_date' => Carbon::now()->addMonths(6)->toDateString(),
            ],
        ];

        $projects = [];

        foreach ($projectsData as $data) {
            $slug = $data['slug'];
            $imagePath = "projects/{$slug}.png";

            if (! Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->put($imagePath, $placeholderImage);
            }

            $projects[$slug] = Project::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'target_amount' => $data['target_amount'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'image' => $imagePath,
                'collected_amount' => 0,
                'progress' => 0,
                'status' => 'open',
            ]);
        }

        return $projects;
    }

    /**
     * Seed the donations table and update project progress.
     *
     * @param  array<string, Project>  $projects
     */
    private function seedDonations(array $projects): void
    {
        $donations = [
            ['project' => 'build-mosque', 'donor_name' => 'محمد علي', 'amount' => 10000, 'is_anonymous' => false, 'payment_method' => 'bank'],
            ['project' => 'build-mosque', 'donor_name' => null, 'amount' => 7500, 'is_anonymous' => true, 'payment_method' => 'cash'],
            ['project' => 'dig-well', 'donor_name' => 'Aisha', 'amount' => 5000, 'is_anonymous' => false, 'payment_method' => 'bank'],
            ['project' => 'dig-well', 'donor_name' => 'Omar', 'amount' => 3500, 'is_anonymous' => false, 'payment_method' => 'card'],
            ['project' => 'sponsor-orphan', 'donor_name' => null, 'amount' => 4000, 'is_anonymous' => true, 'payment_method' => 'bank'],
        ];

        foreach ($donations as $index => $donationData) {
            $project = $projects[$donationData['project']] ?? null;

            if (! $project) {
                continue;
            }

            $donation = Donation::create([
                'project_id' => $project->id,
                'donor_name' => $donationData['donor_name'],
                'amount' => $donationData['amount'],
                'is_anonymous' => $donationData['is_anonymous'],
                'payment_method' => $donationData['payment_method'],
                'created_at' => Carbon::now()->subDays(5 - $index),
            ]);

            $project->collected_amount += $donation->amount;
            $project->progress = $project->target_amount > 0
                ? round(min(100, ($project->collected_amount / $project->target_amount) * 100), 2)
                : 0;
            $project->status = $project->progress >= 100 ? 'completed' : ($project->progress > 0 ? 'in_progress' : 'open');
            $project->save();
        }
    }
}
