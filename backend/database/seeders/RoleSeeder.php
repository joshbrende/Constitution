<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Ordinary party member or app user.',
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'Learner in ZANU PF Academy.',
            ],
            [
                'name' => 'Instructor',
                'slug' => 'instructor',
                'description' => 'ZANU PF Academy instructor (requires approval).',
            ],
            [
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Moderates opinion dialogue and annotations.',
            ],
            [
                'name' => 'Content Editor',
                'slug' => 'content_editor',
                'description' => 'Prepares draft constitutional content and library items.',
            ],
            [
                'name' => 'Approver',
                'slug' => 'approver',
                'description' => 'Approves and publishes official content and amendments.',
            ],
            [
                'name' => 'Presidium',
                'slug' => 'presidium',
                'description' => 'Presidium member; approves constitutional amendments.',
            ],
            [
                'name' => 'System Admin',
                'slug' => 'system_admin',
                'description' => 'Technical administrator for system configuration and access control.',
            ],
            [
                'name' => 'Academy Manager',
                'slug' => 'academy_manager',
                'description' => 'Manages courses, assessments, enrolments, and badges. No constitution or system config.',
            ],
            [
                'name' => 'Dialogue Moderator',
                'slug' => 'dialogue_moderator',
                'description' => 'Moderates channels and threads; lock/unlock, pin/delete messages. No content or user management.',
            ],
            [
                'name' => 'User Manager',
                'slug' => 'user_manager',
                'description' => 'View and assign roles to users. No constitution, academy, or system config.',
            ],
            [
                'name' => 'Analytics Viewer',
                'slug' => 'analytics_viewer',
                'description' => 'Read-only access to analytics and exports. For reporting without edit rights.',
            ],
            [
                'name' => 'Provincial Admin',
                'slug' => 'provincial_admin',
                'description' => 'Manage users and content scoped by province (future: province-filtered views).',
            ],
            [
                'name' => 'Audit Viewer',
                'slug' => 'audit_viewer',
                'description' => 'Read-only access to audit logs for compliance and oversight.',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role,
            );
        }
    }
}

