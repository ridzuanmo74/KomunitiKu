<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['super_admin', 'jawatankuasa', 'ahli', 'pengerusi', 'setiausaha', 'bendahari'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    public function test_super_admin_can_access_admin_user_and_role_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertOk();
    }

    public function test_super_admin_dashboard_includes_administration_sidebar(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('Administration'), false)
            ->assertSee(route('admin.users.index'), false)
            ->assertSee(route('admin.roles.index'), false)
            ->assertDontSee(__('Persatuan Saya'), false)
            ->assertDontSee(__('Pengurusan Yuran'), false);
    }

    public function test_jawatankuasa_dashboard_does_not_include_administration_sidebar(): void
    {
        $user = User::factory()->create();
        $user->assignRole('jawatankuasa');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(__('Administration'), false)
            ->assertSee(__('Pengurusan Yuran'), false);
    }

    public function test_bendahari_dashboard_shows_fee_management_not_association_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('Pengurusan Yuran'), false)
            ->assertDontSee(__('Pengurusan Persatuan'), false);
    }

    public function test_jawatankuasa_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('jawatankuasa');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_ahli_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ahli');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_admin_routes(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.roles.index'))
            ->assertRedirect(route('login'));
    }
}
