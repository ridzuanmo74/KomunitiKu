<?php

namespace Tests\Feature;

use App\Models\Association;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CommitteeFeeCrudTest extends TestCase
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

    public function test_bendahari_can_create_update_and_delete_fee_for_own_association(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $association = Association::create(['name' => 'Persatuan Bendahari', 'code' => 'PB-CRUD']);
        $user->associations()->attach($association->id);

        $this->actingAs($user)
            ->post('/committee/fees', [
                'name' => 'Yuran Bulanan',
                'amount' => '45.50',
                'frequency' => 'monthly',
                'due_day' => 15,
                'is_active' => 1,
            ])
            ->assertRedirect('/committee/fees/settings');

        $fee = Fee::query()->where('association_id', $association->id)->first();
        $this->assertNotNull($fee);

        $this->actingAs($user)
            ->patch("/committee/fees/{$fee->id}", [
                'name' => 'Yuran Sekali',
                'amount' => '120.00',
                'frequency' => 'one_time',
                'is_active' => 0,
            ])
            ->assertRedirect('/committee/fees/settings');

        $fee->refresh();
        $this->assertSame('Yuran Sekali', $fee->name);
        $this->assertSame('120.00', $fee->amount);
        $this->assertSame('one_time', $fee->frequency);
        $this->assertNull($fee->due_day);
        $this->assertFalse($fee->is_active);

        $this->actingAs($user)
            ->delete("/committee/fees/{$fee->id}")
            ->assertRedirect('/committee/fees/settings');

        $this->assertDatabaseMissing('fees', ['id' => $fee->id]);
    }

    public function test_fee_settings_page_shows_table_and_action_buttons(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $association = Association::create(['name' => 'Persatuan UI', 'code' => 'PUI-CRUD']);
        $user->associations()->attach($association->id);

        Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Ujian UI',
            'amount' => 20.00,
            'frequency' => 'yearly',
            'due_day' => null,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/committee/fees/settings')
            ->assertOk()
            ->assertSee('Tambah Yuran Baharu')
            ->assertSee('Nama Yuran')
            ->assertSee('Kekerapan')
            ->assertSee('Tindakan')
            ->assertSee('Kemaskini')
            ->assertSee('Padam');
    }

    public function test_bendahari_cannot_modify_fee_from_other_association(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $associationA = Association::create(['name' => 'Persatuan A', 'code' => 'PA-CRUD']);
        $associationB = Association::create(['name' => 'Persatuan B', 'code' => 'PB-CRUD']);

        $user->associations()->attach($associationA->id);

        $foreignFee = Fee::create([
            'association_id' => $associationB->id,
            'name' => 'Yuran Luar',
            'amount' => 30.00,
            'frequency' => 'monthly',
            'due_day' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->patch("/committee/fees/{$foreignFee->id}", [
                'name' => 'Cubaan Kemaskini',
                'amount' => 35.00,
                'frequency' => 'monthly',
                'due_day' => 20,
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete("/committee/fees/{$foreignFee->id}")
            ->assertForbidden();
    }

    public function test_fee_create_validation_errors_redirect_back_with_errors(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $association = Association::create(['name' => 'Persatuan Validasi', 'code' => 'PV-CRUD']);
        $user->associations()->attach($association->id);

        $this->actingAs($user)
            ->from('/committee/fees/settings')
            ->post('/committee/fees', [
                'name' => '',
                'amount' => '0',
                'frequency' => 'monthly',
                'due_day' => '',
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasErrors(['name', 'amount', 'due_day']);

        $this->actingAs($user)
            ->from('/committee/fees/settings')
            ->post('/committee/fees', [
                'name' => 'Yuran Tahunan',
                'amount' => '120.00',
                'frequency' => 'yearly',
                'due_day' => 10,
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasNoErrors();
    }

    public function test_member_role_cannot_access_committee_fee_crud_endpoints(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ahli');

        $association = Association::create(['name' => 'Persatuan Ahli', 'code' => 'PAH-CRUD']);
        $user->associations()->attach($association->id);

        $fee = Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Ahli',
            'amount' => 12.00,
            'frequency' => 'yearly',
            'due_day' => null,
            'is_active' => true,
        ]);

        $this->actingAs($user)->post('/committee/fees', [
            'name' => 'Yuran',
            'amount' => '10.00',
            'frequency' => 'yearly',
        ])->assertForbidden();

        $this->actingAs($user)->patch("/committee/fees/{$fee->id}", [
            'name' => 'Yuran Ubah',
            'amount' => '11.00',
            'frequency' => 'yearly',
            'is_active' => 1,
        ])->assertForbidden();

        $this->actingAs($user)->delete("/committee/fees/{$fee->id}")->assertForbidden();
    }
}
