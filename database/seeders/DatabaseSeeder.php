<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedRolesAndUsers();
            $this->seedSettings();
            $this->seedContent();
        });

        cache()->forget('global_settings');
    }

    protected function seedRolesAndUsers(): void
    {
        $permissions = [
            'view admin dashboard',
            'manage services',
            'manage projects',
            'manage project images',
            'manage team',
            'manage contacts',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);

        $adminRole->syncPermissions($permissions);
        $editorRole->syncPermissions([
            'view admin dashboard',
            'manage services',
            'manage projects',
            'manage project images',
            'manage team',
            'manage contacts',
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@devgenfour.test'],
            [
                'name' => 'Devgenfour Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$adminRole]);

        $editor = User::updateOrCreate(
            ['email' => 'editor@devgenfour.test'],
            [
                'name' => 'Devgenfour Editor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $editor->syncRoles([$editorRole]);
    }

    protected function seedSettings(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'Devgenfour', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_tagline', 'value' => 'Digital products for ambitious brands.', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_email', 'value' => 'hello@devgenfour.com', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '+1 (555) 010-3344', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_address', 'value' => '120 Market Street, Suite 400, San Francisco, CA', 'type' => 'string', 'group' => 'company'],
            [
                'key' => 'company_social_links',
                'value' => json_encode([
                    ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/devgenfour'],
                    ['label' => 'Dribbble', 'url' => 'https://dribbble.com/devgenfour'],
                ]),
                'type' => 'json',
                'group' => 'company',
            ],
            ['key' => 'seo_meta_title', 'value' => 'Devgenfour — Bespoke Digital Products & Growth', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo_meta_description', 'value' => 'We help B2B companies ship polished web experiences, marketing sites, and digital products that convert.', 'type' => 'string', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }

    protected function seedContent(): void
    {
        $services = Service::factory()->count(6)->create()->sortBy('display_order');

        $tags = Tag::factory()->count(6)->create();

        $projects = Project::factory()->count(5)->create();

        foreach ($projects as $project) {
            ProjectImage::factory()->count(3)->create([
                'project_id' => $project->id,
            ]);

            $project->tags()->sync(
                $tags->random(rand(1, min(3, $tags->count())))->pluck('id')->all()
            );
        }

        TeamMember::factory()->count(4)->create();

        $adminId = User::where('email', 'admin@devgenfour.test')->value('id');

        ContactMessage::factory()->count(3)->create([
            'status' => 'new',
            'handled_by' => null,
            'handled_at' => null,
        ]);

        if ($adminId) {
            ContactMessage::factory()->count(2)->create([
                'status' => 'handled',
                'handled_by' => $adminId,
                'handled_at' => now()->subDays(2),
            ]);
        }

        $posts = Post::factory()->count(3)->create([
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        foreach ($posts as $post) {
            $post->tags()->sync(
                $tags->random(rand(1, min(3, $tags->count())))->pluck('id')->all()
            );
        }

        Setting::updateOrCreate([
            'key' => 'homepage_featured_services',
        ], [
            'key' => 'homepage_featured_services',
            'value' => $services->take(3)->pluck('title')->implode(', '),
            'type' => 'string',
            'group' => 'homepage',
        ]);
    }
}
