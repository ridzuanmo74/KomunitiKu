<?php

namespace Tests\Feature;

use App\Models\Association;
use App\Models\State;
use App\Models\User;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AssociationRegistryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['super_admin', 'jawatankuasa', 'ahli'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $this->seed(StateSeeder::class);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validAssociationStorePayload(array $overrides = []): array
    {
        $state = State::query()->firstOrFail();

        return array_merge([
            'name' => 'Persatuan Ujian Payload',
            'code' => 'PL-'.substr(str_replace('.', '', uniqid('', true)), 0, 12),
            'is_active' => '1',
            'established_date' => '2020-06-01',
            'address' => 'No 10 Jalan Ujian, Taman Contoh',
            'postcode' => '40000',
            'state_id' => $state->id,
            'phone' => '03-12345678',
            'official_email' => 'payload@example.test',
        ], $overrides);
    }

    public function test_super_admin_can_create_and_update_association_with_state_and_coordinates(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $state = State::query()->where('code', 'SGR')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), [
                'name' => 'Persatuan Ujian',
                'code' => 'PU-REG-001',
                'description' => 'Deskripsi',
                'is_active' => '1',
                'ros_registration_number' => 'ROS-123',
                'established_date' => '2020-01-15',
                'address' => 'No 1 Jalan Ujian',
                'postcode' => '40000',
                'city' => 'Shah Alam',
                'state_id' => $state->id,
                'phone' => '03-12345678',
                'official_email' => 'info@persatuan.test',
                'latitude' => '3,0738',
                'longitude' => '101,5183',
            ])
            ->assertRedirect();

        $association = Association::query()->where('code', 'PU-REG-001')->firstOrFail();
        $this->assertSame($state->id, (int) $association->state_id);
        $this->assertNotNull($association->latitude);
        $this->assertEqualsWithDelta(3.0738, (float) $association->latitude, 0.0001);

        $this->actingAs($admin)
            ->put(route('committee.associations.update', $association), [
                'name' => 'Persatuan Ujian Dikemaskini',
                'code' => 'PU-REG-001',
                'description' => 'Deskripsi',
                'is_active' => '0',
                'ros_registration_number' => 'ROS-999',
                'established_date' => '2020-01-15',
                'address' => 'No 22 Jalan Kemas Kini',
                'postcode' => '40100',
                'city' => 'Shah Alam',
                'state_id' => $state->id,
                'phone' => '03-87654321',
                'official_email' => 'hq@persatuan.test',
                'latitude' => '',
                'longitude' => '',
            ])
            ->assertRedirect(route('committee.associations.info', ['association' => $association->id]));

        $association->refresh();
        $this->assertFalse($association->is_active);
        $this->assertNull($association->latitude);
        $this->assertNull($association->longitude);
    }

    public function test_super_admin_cannot_store_with_invalid_state_id(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'name' => 'X',
                'code' => 'X-001',
                'state_id' => 999999,
            ]))
            ->assertSessionHasErrors('state_id');
    }

    public function test_super_admin_cannot_store_latitude_without_longitude(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'name' => 'Y',
                'code' => 'Y-001',
                'latitude' => '1.5',
                'longitude' => '',
            ]))
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_jawatankuasa_cannot_access_association_mutations(): void
    {
        $committee = User::factory()->create();
        $committee->assignRole('jawatankuasa');

        $association = Association::create(['name' => 'P', 'code' => 'P-001']);

        $this->actingAs($committee)
            ->get(route('committee.associations.create'))
            ->assertForbidden();

        $this->actingAs($committee)
            ->post(route('committee.associations.store'), ['name' => 'Z', 'code' => 'Z-001'])
            ->assertForbidden();

        $this->actingAs($committee)
            ->get(route('committee.associations.edit', $association))
            ->assertForbidden();

        $this->actingAs($committee)
            ->put(route('committee.associations.update', $association), [
                'name' => 'Z2',
                'code' => 'P-001',
            ])
            ->assertForbidden();

        $this->actingAs($committee)
            ->delete(route('committee.associations.destroy', $association))
            ->assertForbidden();
    }

    public function test_jawatankuasa_can_view_association_info(): void
    {
        $committee = User::factory()->create();
        $committee->assignRole('jawatankuasa');

        $state = State::query()->firstOrFail();
        $association = Association::create([
            'name' => 'Persatuan J',
            'code' => 'PJ-REG-001',
            'state_id' => $state->id,
        ]);
        $committee->associations()->attach($association->id);

        $this->actingAs($committee)
            ->get(route('committee.associations.info'))
            ->assertOk()
            ->assertSee('Persatuan J', false)
            ->assertSee($state->name, false);
    }

    public function test_super_admin_can_delete_association_without_relations(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $association = Association::create(['name' => 'Padam', 'code' => 'PD-001']);

        $this->actingAs($admin)
            ->delete(route('committee.associations.destroy', $association))
            ->assertRedirect(route('committee.associations.info'));

        $this->assertFalse(Association::query()->whereKey($association->id)->exists());
    }

    public function test_super_admin_cannot_delete_association_with_members(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $member = User::factory()->create();
        $member->assignRole('ahli');

        $association = Association::create(['name' => 'P2', 'code' => 'P2-001']);
        $member->associations()->attach($association->id);

        $this->actingAs($admin)
            ->from(route('committee.associations.info', ['association' => $association->id]))
            ->delete(route('committee.associations.destroy', $association))
            ->assertRedirect(route('committee.associations.info', ['association' => $association->id]))
            ->assertSessionHasErrors('association');

        $this->assertTrue(Association::query()->whereKey($association->id)->exists());
    }

    public function test_super_admin_association_info_search_filters_results(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        Association::create(['name' => 'Zebra One', 'code' => 'Z1-001']);
        Association::create(['name' => 'Yankee Two', 'code' => 'Y2-001']);

        $zebra = Association::query()->where('code', 'Z1-001')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('committee.associations.info', [
                'q' => 'Zebra',
                'association' => $zebra->id,
            ]))
            ->assertOk()
            ->assertSee('Zebra One', false)
            ->assertDontSee('Yankee Two', false);
    }

    public function test_super_admin_association_info_pagination_and_invalid_per_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        for ($i = 1; $i <= 11; $i++) {
            Association::create([
                'name' => sprintf('Pag %02d', $i),
                'code' => sprintf('PG-%02d', $i),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('committee.associations.info', ['per_page' => 10]))
            ->assertOk()
            ->assertSee('Pag 01', false)
            ->assertDontSee('Pag 11', false);

        $this->actingAs($admin)
            ->get(route('committee.associations.info', ['per_page' => 10, 'page' => 2]))
            ->assertOk()
            ->assertSee('Pag 11', false);

        $this->actingAs($admin)
            ->get(route('committee.associations.info', ['per_page' => 999, 'page' => 2]))
            ->assertOk()
            ->assertSee('Pag 11', false);
    }

    public function test_super_admin_association_info_pagination_preserves_query_string(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        for ($i = 1; $i <= 11; $i++) {
            Association::create([
                'name' => sprintf('List %02d', $i),
                'code' => sprintf('LS-%02d', $i),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('committee.associations.info', ['q' => 'List', 'per_page' => 10]))
            ->assertOk()
            ->assertSee('per_page=10', false)
            ->assertSee(rawurlencode('q').'=', false);
    }

    public function test_jawatankuasa_association_query_selects_detail_when_member_of_two(): void
    {
        $committee = User::factory()->create();
        $committee->assignRole('jawatankuasa');

        $state = State::query()->firstOrFail();
        $alpha = Association::create([
            'name' => 'Alpha Club',
            'code' => 'AC-001',
            'city' => 'CityAlphaUnique',
            'state_id' => $state->id,
        ]);
        $beta = Association::create([
            'name' => 'Beta Club',
            'code' => 'BC-001',
            'city' => 'CityBetaUnique',
            'state_id' => $state->id,
        ]);
        $committee->associations()->attach([$alpha->id, $beta->id]);

        $this->actingAs($committee)
            ->get(route('committee.associations.info'))
            ->assertOk()
            ->assertSee('CityAlphaUnique', false);

        $this->actingAs($committee)
            ->get(route('committee.associations.info', ['association' => $beta->id]))
            ->assertOk()
            ->assertSee('CityBetaUnique', false)
            ->assertSee('Beta Club', false);
    }

    public function test_super_admin_can_view_create_association_form(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $firstState = State::query()->orderBy('name')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('committee.associations.create'))
            ->assertOk()
            ->assertSee(__('Daftar persatuan baharu'), false)
            ->assertSee(__('Simpan'), false)
            ->assertSee(__('Nama'), false)
            ->assertSee(__('Kod'), false)
            ->assertSee($firstState->name, false);
    }

    public function test_guest_is_redirected_to_login_when_visiting_create_association_form(): void
    {
        $this->get(route('committee.associations.create'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_super_admin_cannot_store_duplicate_association_code(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        Association::create(['name' => 'Existing', 'code' => 'DUP-001']);

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'name' => 'New Name',
                'code' => 'DUP-001',
            ]))
            ->assertSessionHasErrors('code');
    }

    public function test_super_admin_cannot_store_without_required_fields(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), [])
            ->assertSessionHasErrors([
                'name',
                'code',
                'established_date',
                'address',
                'postcode',
                'state_id',
                'phone',
                'official_email',
            ]);
    }

    public function test_super_admin_can_store_minimal_association(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'name' => 'Minimal Persatuan',
                'code' => 'MIN-001',
            ]));

        $association = Association::query()->where('code', 'MIN-001')->firstOrFail();

        $response->assertRedirect(route('committee.associations.info', ['association' => $association->id]));

        $this->assertDatabaseHas('associations', [
            'id' => $association->id,
            'code' => 'MIN-001',
            'name' => 'Minimal Persatuan',
        ]);
    }

    public function test_super_admin_cannot_store_established_date_after_today(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'code' => 'FD-'.substr(str_replace('.', '', uniqid('', true)), 0, 10),
                'established_date' => now()->addDay()->format('Y-m-d'),
            ]))
            ->assertSessionHasErrors('established_date');
    }

    public function test_super_admin_cannot_store_invalid_postcode(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'code' => 'PC-'.substr(str_replace('.', '', uniqid('', true)), 0, 10),
                'postcode' => '4000',
            ]))
            ->assertSessionHasErrors('postcode');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'code' => 'PC-'.substr(str_replace('.', '', uniqid('', true)), 0, 10),
                'postcode' => 'ABCDE',
            ]))
            ->assertSessionHasErrors('postcode');
    }

    public function test_super_admin_cannot_store_invalid_official_email(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'code' => 'EM-'.substr(str_replace('.', '', uniqid('', true)), 0, 10),
                'official_email' => 'not-an-email',
            ]))
            ->assertSessionHasErrors('official_email');
    }

    public function test_super_admin_cannot_store_invalid_phone(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('committee.associations.store'), $this->validAssociationStorePayload([
                'code' => 'PH-'.substr(str_replace('.', '', uniqid('', true)), 0, 10),
                'phone' => '0123456',
            ]))
            ->assertSessionHasErrors('phone');
    }
}
