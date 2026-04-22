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
                'amount' => '0.99',
                'frequency' => 'monthly',
                'due_day' => '',
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasErrors(['name', 'amount', 'due_day']);

        $this->actingAs($user)
            ->from('/committee/fees/settings')
            ->post('/committee/fees', [
                'name' => 'Yuran Tahunan',
                'amount' => '1.00',
                'frequency' => 'yearly',
                'due_day' => 10,
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasNoErrors();
    }

    public function test_fee_name_must_be_unique_within_same_association(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $association = Association::create(['name' => 'Persatuan Unik', 'code' => 'PU-CRUD']);
        $user->associations()->attach($association->id);

        Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Komitmen',
            'amount' => 25.00,
            'frequency' => 'monthly',
            'due_day' => 7,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from('/committee/fees/settings')
            ->post('/committee/fees', [
                'name' => 'Yuran Komitmen',
                'amount' => '30.00',
                'frequency' => 'monthly',
                'due_day' => 9,
                'is_active' => 1,
                'form_context' => 'create',
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasErrors(['name'])
            ->assertSessionHasInput('form_context', 'create');

        $this->assertSame(
            1,
            Fee::query()
                ->where('association_id', $association->id)
                ->where('name', 'Yuran Komitmen')
                ->count()
        );
    }

    public function test_fee_name_can_repeat_across_different_associations(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $associationA = Association::create(['name' => 'Persatuan Satu', 'code' => 'PS-CRUD']);
        $associationB = Association::create(['name' => 'Persatuan Dua', 'code' => 'PD-CRUD']);
        $user->associations()->attach($associationA->id);

        Fee::create([
            'association_id' => $associationB->id,
            'name' => 'Yuran Standard',
            'amount' => 40.00,
            'frequency' => 'yearly',
            'due_day' => null,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from('/committee/fees/settings')
            ->post('/committee/fees', [
                'name' => 'Yuran Standard',
                'amount' => '15.00',
                'frequency' => 'monthly',
                'due_day' => 12,
                'is_active' => 1,
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fees', [
            'association_id' => $associationA->id,
            'name' => 'Yuran Standard',
            'amount' => '15.00',
        ]);
    }

    public function test_fee_update_duplicate_name_redirects_back_with_edit_modal_context(): void
    {
        $user = User::factory()->create();
        $user->assignRole('bendahari');

        $association = Association::create(['name' => 'Persatuan Kemaskini', 'code' => 'PK-CRUD']);
        $user->associations()->attach($association->id);

        $existingFee = Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Asal',
            'amount' => 25.00,
            'frequency' => 'yearly',
            'due_day' => null,
            'is_active' => true,
        ]);

        $editableFee = Fee::create([
            'association_id' => $association->id,
            'name' => 'Yuran Kedua',
            'amount' => 35.00,
            'frequency' => 'monthly',
            'due_day' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from('/committee/fees/settings')
            ->patch("/committee/fees/{$editableFee->id}", [
                'name' => $existingFee->name,
                'amount' => '35.00',
                'frequency' => 'monthly',
                'due_day' => 5,
                'is_active' => 1,
                'form_context' => 'edit',
                'editing_fee_id' => (string) $editableFee->id,
            ])
            ->assertRedirect('/committee/fees/settings')
            ->assertSessionHasErrors(['name'])
            ->assertSessionHasInput('form_context', 'edit')
            ->assertSessionHasInput('editing_fee_id', (string) $editableFee->id);
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
