<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\Hash;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin & Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@projectpilot.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'dmitry@projectpilot.com'],
            [
                'name' => 'Dmitry Ivaniuk',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        $dev1 = User::firstOrCreate(
            ['email' => 'alexy@projectpilot.com'],
            [
                'name' => 'Alexy Torenov',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $dev2 = User::firstOrCreate(
            ['email' => 'julia@projectpilot.com'],
            [
                'name' => 'Julia Venchees',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $dev3 = User::firstOrCreate(
            ['email' => 'john@projectpilot.com'],
            [
                'name' => 'John Doenson',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // Also ensure default test user exists
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Sample Projects
        $p1 = Project::create([
            'name' => 'UI Redesign for CRM System',
            'description' => 'Complete overhaul of the CRM interface including responsive layouts, theme styling and dashboard widgets.',
            'status' => 'in_progress',
            'priority' => 'high',
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'due_date' => now()->addDays(15)->format('Y-m-d'),
            'user_id' => $admin->id,
        ]);

        $p2 = Project::create([
            'name' => 'Marketing Strategy & Analytics',
            'description' => 'Develop digital marketing funnel analytics, lead tracking and automated reporting modules.',
            'status' => 'in_progress',
            'priority' => 'medium',
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'due_date' => now()->addDays(20)->format('Y-m-d'),
            'user_id' => $manager->id,
        ]);

        $p3 = Project::create([
            'name' => 'Mobile App API Integration',
            'description' => 'REST API endpoints for mobile authentication, project status tracking, and push notifications.',
            'status' => 'pending',
            'priority' => 'high',
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'user_id' => $admin->id,
        ]);

        $p4 = Project::create([
            'name' => 'E-Commerce Platform Upgrade',
            'description' => 'Migrate payment gateway to Stripe & PayPal v2 APIs and improve checkout flow performance.',
            'status' => 'completed',
            'priority' => 'low',
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'due_date' => now()->subDays(2)->format('Y-m-d'),
            'user_id' => $manager->id,
        ]);

        // Attach Members
        $p1->members()->sync([$admin->id, $manager->id, $dev1->id, $dev2->id]);
        $p2->members()->sync([$manager->id, $dev2->id, $dev3->id]);
        $p3->members()->sync([$admin->id, $dev1->id, $dev3->id]);
        $p4->members()->sync([$manager->id, $dev2->id]);

        // Create Sample Tasks for Project 1
        Task::create([
            'title' => 'Design dashboard wireframes & mockups',
            'description' => 'Create responsive wireframes for dashboard and analytics panels.',
            'status' => 'completed',
            'priority' => 'high',
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'due_date' => now()->subDays(2)->format('Y-m-d'),
            'project_id' => $p1->id,
            'assigned_to' => $dev1->id,
        ]);

        Task::create([
            'title' => 'Integrate Bootstrap 4 Admin Theme',
            'description' => 'Implement theme assets, sidebar layout components and CSS styles into Blade layout.',
            'status' => 'in_progress',
            'priority' => 'high',
            'start_date' => now()->subDays(2)->format('Y-m-d'),
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'project_id' => $p1->id,
            'assigned_to' => $dev2->id,
        ]);

        Task::create([
            'title' => 'User Authentication & Roles Setup',
            'description' => 'Configure Admin, Manager and User access roles with middleware protection.',
            'status' => 'pending',
            'priority' => 'medium',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'project_id' => $p1->id,
            'assigned_to' => $admin->id,
        ]);

        // Create Sample Tasks for Project 2
        Task::create([
            'title' => 'Set up Google Analytics API connection',
            'description' => 'Fetch visitor counts and pageview statistics via GA4 API.',
            'status' => 'in_progress',
            'priority' => 'medium',
            'start_date' => now()->subDays(3)->format('Y-m-d'),
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'project_id' => $p2->id,
            'assigned_to' => $dev3->id,
        ]);

        Task::create([
            'title' => 'Create lead conversion reporting dashboard',
            'description' => 'Visual charts for conversion funnel metrics.',
            'status' => 'pending',
            'priority' => 'low',
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'due_date' => now()->addDays(12)->format('Y-m-d'),
            'project_id' => $p2->id,
            'assigned_to' => $dev2->id,
        ]);
    }
}
