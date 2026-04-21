<?php

namespace Tests\Feature;

use App\Models\Association;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MemberPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['super_admin', 'jawatankuasa', 'ahli', 'pengerusi', 'setiausaha', 'bendahari'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $this->seed(StateSeeder::class);
    }

    public function test_member_pages_are_accessible_for_authenticated_member(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ahli');

        $association = Association::create(['name' => 'Persatuan A', 'code' => 'PA-001']);
        $user->associations()->attach($association->id, [
            'membership_no' => 'AHLI-001',
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);

        $fee = Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Tahunan',
            'amount' => 120.00,
            'frequency' => 'yearly',
            'due_day' => null,
            'is_active' => true,
        ]);

        Payment::create([
            'association_id' => $association->id,
            'user_id' => $user->id,
            'fee_id' => $fee->id,
            'amount' => 120.00,
            'status' => 'paid',
            'paid_at' => now(),
            'reference' => 'PAY-001',
        ]);

        $this->actingAs($user)->get('/member/associations')->assertOk();
        $this->actingAs($user)->get('/member/membership/profile')->assertOk();
        $this->actingAs($user)->get('/member/membership/card')->assertOk();
        $this->actingAs($user)->get('/member/fees')->assertOk();
        $this->actingAs($user)->get('/member/invoices')->assertOk();
        $this->actingAs($user)->get('/member/payments')->assertOk();
        $this->actingAs($user)->get('/member/receipts')->assertOk();
        $this->actingAs($user)->get('/member/activities')->assertOk();
        $this->actingAs($user)->get('/member/attendances')->assertOk();
        $this->actingAs($user)->get('/member/announcements')->assertOk();
    }

    public function test_one_time_fee_is_shown_as_completed_after_payment(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ahli');

        $association = Association::create(['name' => 'Persatuan One Time', 'code' => 'POT-001']);
        $user->associations()->attach($association->id, [
            'membership_no' => 'AHLI-OT-001',
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);

        $fee = Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Pendaftaran',
            'amount' => 50.00,
            'frequency' => 'one_time',
            'due_day' => null,
            'is_active' => true,
        ]);

        Payment::create([
            'association_id' => $association->id,
            'user_id' => $user->id,
            'fee_id' => $fee->id,
            'amount' => 50.00,
            'status' => 'paid',
            'paid_at' => now(),
            'reference' => 'PAY-OT-001',
        ]);

        $this->actingAs($user)
            ->get('/member/invoices')
            ->assertOk()
            ->assertSee('Sekali sahaja')
            ->assertSee('Selesai');
    }

    public function test_member_can_switch_active_association_only_with_own_membership(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ahli');

        $associationA = Association::create(['name' => 'Persatuan A', 'code' => 'PA-002']);
        $associationB = Association::create(['name' => 'Persatuan B', 'code' => 'PB-002']);
        $associationC = Association::create(['name' => 'Persatuan C', 'code' => 'PC-002']);

        $user->associations()->attach([$associationA->id, $associationB->id]);

        $this->actingAs($user)
            ->patch('/member/associations/switch', ['association_id' => $associationB->id])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('member.active_association_id', $associationB->id);

        $this->actingAs($user)
            ->patch('/member/associations/switch', ['association_id' => $associationC->id])
            ->assertForbidden();
    }

    public function test_super_admin_cannot_access_member_portal_routes(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $association = Association::create(['name' => 'Persatuan X', 'code' => 'PX-001']);
        $superAdmin->associations()->attach($association->id);

        $this->actingAs($superAdmin)
            ->get('/member/associations')
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->patch('/member/associations/switch', ['association_id' => $association->id])
            ->assertForbidden();
    }

    public function test_super_admin_can_access_committee_pages_without_membership_pivot(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        Association::create(['name' => 'Persatuan Fallback', 'code' => 'PF-001']);

        $this->actingAs($superAdmin)
            ->get('/committee/associations/info')
            ->assertOk();
    }

    public function test_super_admin_cannot_access_committee_fee_pages(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        Association::create(['name' => 'Persatuan F', 'code' => 'PF-002']);

        $this->actingAs($superAdmin)
            ->get('/committee/fees/settings')
            ->assertForbidden();
    }

    public function test_super_admin_cannot_access_committee_membership_approvals_page(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        Association::create(['name' => 'Persatuan Approvals', 'code' => 'PA-APR']);

        $this->actingAs($superAdmin)
            ->get('/committee/associations/approvals')
            ->assertForbidden();
    }

    public function test_jawatankuasa_can_access_committee_membership_approvals_page(): void
    {
        $committee = User::factory()->create();
        $committee->assignRole('jawatankuasa');

        $association = Association::create(['name' => 'Persatuan H', 'code' => 'PH-001']);
        $committee->associations()->attach($association->id);

        $this->actingAs($committee)
            ->get('/committee/associations/approvals')
            ->assertOk();
    }

    public function test_jawatankuasa_can_access_committee_fee_pages(): void
    {
        $committee = User::factory()->create();
        $committee->assignRole('jawatankuasa');

        $association = Association::create(['name' => 'Persatuan G', 'code' => 'PG-001']);
        $committee->associations()->attach($association->id);

        $this->actingAs($committee)
            ->get('/committee/fees/settings')
            ->assertOk();
    }

    public function test_committee_pages_require_committee_or_super_admin_role(): void
    {
        $member = User::factory()->create();
        $member->assignRole('ahli');

        $committee = User::factory()->create();
        $committee->assignRole('jawatankuasa');

        $association = Association::create(['name' => 'Persatuan D', 'code' => 'PD-001']);
        $committee->associations()->attach($association->id);

        $this->actingAs($member)
            ->get('/committee/associations/info')
            ->assertForbidden();

        $this->actingAs($committee)
            ->get('/committee/associations/info')
            ->assertOk();
    }

    public function test_pengerusi_can_access_committee_membership_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('pengerusi');

        $association = Association::create(['name' => 'Persatuan P', 'code' => 'PP-001']);
        $user->associations()->attach($association->id);

        $this->actingAs($user)->get('/committee/associations/info')->assertOk();
        $this->actingAs($user)->get('/committee/associations/approvals')->assertOk();
        $this->actingAs($user)->get('/committee/fees/settings')->assertForbidden();
    }

    public function test_bendahari_can_access_committee_fee_pages_but_not_association_info(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $association = Association::create(['name' => 'Persatuan Bdh', 'code' => 'PBH-001']);
        $user->associations()->attach($association->id);

        $this->actingAs($user)->get('/committee/fees/settings')->assertOk();
        $this->actingAs($user)->get('/committee/associations/info')->assertForbidden();
    }

    public function test_setiausaha_can_access_committee_membership_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('setiausaha');

        $association = Association::create(['name' => 'Persatuan S', 'code' => 'PS-001']);
        $user->associations()->attach($association->id);

        $this->actingAs($user)->get('/committee/associations/info')->assertOk();
        $this->actingAs($user)->get('/committee/associations/approvals')->assertOk();
    }
}
