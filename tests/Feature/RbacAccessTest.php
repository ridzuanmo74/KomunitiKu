<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Association;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'jawatankuasa', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'ahli', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_manage_activity_across_associations(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $associationA = Association::create(['name' => 'A', 'code' => 'A-001']);
        $associationB = Association::create(['name' => 'B', 'code' => 'B-001']);

        $superAdmin->associations()->attach([$associationA->id, $associationB->id]);

        $this->actingAs($superAdmin)
            ->postJson('/rbac/activities', [
                'association_id' => $associationB->id,
                'title' => 'Mesyuarat Agung',
                'activity_date' => now()->addDay()->toDateTimeString(),
            ])
            ->assertCreated();
    }

    public function test_jawatankuasa_can_manage_only_own_association(): void
    {
        $jawatankuasa = User::factory()->create();
        $jawatankuasa->assignRole('jawatankuasa');

        $associationA = Association::create(['name' => 'A', 'code' => 'A-002']);
        $associationB = Association::create(['name' => 'B', 'code' => 'B-002']);

        $jawatankuasa->associations()->attach($associationA->id);

        $this->actingAs($jawatankuasa)
            ->postJson('/rbac/activities', [
                'association_id' => $associationA->id,
                'title' => 'Program A',
                'activity_date' => now()->addDay()->toDateTimeString(),
            ])
            ->assertCreated();

        $this->actingAs($jawatankuasa)
            ->postJson('/rbac/activities', [
                'association_id' => $associationB->id,
                'title' => 'Program B',
                'activity_date' => now()->addDays(2)->toDateTimeString(),
            ])
            ->assertForbidden();
    }

    public function test_ahli_cannot_manage_activity_announcement_or_fee(): void
    {
        $ahli = User::factory()->create();
        $ahli->assignRole('ahli');

        $association = Association::create(['name' => 'A', 'code' => 'A-003']);
        $ahli->associations()->attach($association->id);

        $this->actingAs($ahli)
            ->postJson('/rbac/activities', [
                'association_id' => $association->id,
                'title' => 'Tidak dibenarkan',
                'activity_date' => now()->addDay()->toDateTimeString(),
            ])
            ->assertForbidden();

        $this->actingAs($ahli)
            ->postJson('/rbac/announcements', [
                'association_id' => $association->id,
                'title' => 'Tidak dibenarkan',
                'content' => 'Kandungan',
            ])
            ->assertForbidden();

        $this->actingAs($ahli)
            ->postJson('/rbac/fees', [
                'association_id' => $association->id,
                'name' => 'Yuran Bulanan',
                'amount' => 10.50,
            ])
            ->assertForbidden();
    }

    public function test_ahli_can_record_attendance_and_payment_for_own_association(): void
    {
        $ahli = User::factory()->create();
        $ahli->assignRole('ahli');

        $association = Association::create(['name' => 'A', 'code' => 'A-004']);
        $ahli->associations()->attach($association->id);

        $activity = Activity::create([
            'association_id' => $association->id,
            'title' => 'Gotong Royong',
            'activity_date' => now()->addDay(),
            'created_by' => $ahli->id,
        ]);

        $this->actingAs($ahli)
            ->postJson('/rbac/attendances', [
                'activity_id' => $activity->id,
            ])
            ->assertCreated();

        $this->actingAs($ahli)
            ->postJson('/rbac/payments', [
                'association_id' => $association->id,
                'amount' => 50.00,
                'reference' => 'PAY-001',
            ])
            ->assertCreated();
    }

    public function test_cross_association_access_is_forbidden_for_ahli(): void
    {
        $ahli = User::factory()->create();
        $ahli->assignRole('ahli');

        $associationA = Association::create(['name' => 'A', 'code' => 'A-005']);
        $associationB = Association::create(['name' => 'B', 'code' => 'B-005']);

        $ahli->associations()->attach($associationA->id);

        $activityB = Activity::create([
            'association_id' => $associationB->id,
            'title' => 'Aktiviti B',
            'activity_date' => now()->addDay(),
            'created_by' => $ahli->id,
        ]);

        $this->actingAs($ahli)
            ->postJson('/rbac/attendances', [
                'activity_id' => $activityB->id,
            ])
            ->assertForbidden();
    }
}
