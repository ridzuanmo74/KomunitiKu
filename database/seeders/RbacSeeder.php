<?php

namespace Database\Seeders;

use App\Models\Association;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['super_admin', 'jawatankuasa', 'ahli', 'pengerusi', 'setiausaha', 'bendahari'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $associationA = Association::firstOrCreate(
            ['code' => 'PERSATUAN-A'],
            ['name' => 'Persatuan A', 'description' => 'Persatuan contoh A', 'is_active' => true]
        );

        $associationB = Association::firstOrCreate(
            ['code' => 'PERSATUAN-B'],
            ['name' => 'Persatuan B', 'description' => 'Persatuan contoh B', 'is_active' => true]
        );

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@komunitiku.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );
        $superAdmin->syncRoles(['super_admin']);

        $committee = User::updateOrCreate(
            ['email' => 'jawatankuasa@komunitiku.test'],
            ['name' => 'Jawatankuasa A', 'password' => Hash::make('password')]
        );
        $committee->syncRoles(['jawatankuasa']);
        $committee->associations()->syncWithoutDetaching([
            $associationA->id => ['joined_at' => now()->toDateString(), 'is_active' => true],
        ]);

        $member = User::updateOrCreate(
            ['email' => 'ahli@komunitiku.test'],
            ['name' => 'Ahli A', 'password' => Hash::make('password')]
        );
        $member->syncRoles(['ahli']);
        $member->associations()->syncWithoutDetaching([
            $associationA->id => ['joined_at' => now()->toDateString(), 'is_active' => true],
        ]);

        $pengerusi = User::updateOrCreate(
            ['email' => 'pengerusi@komunitiku.test'],
            ['name' => 'Pengerusi A', 'password' => Hash::make('password')]
        );
        $pengerusi->syncRoles(['pengerusi']);
        $pengerusi->associations()->syncWithoutDetaching([
            $associationA->id => ['joined_at' => now()->toDateString(), 'is_active' => true],
        ]);

        $setiausaha = User::updateOrCreate(
            ['email' => 'setiausaha@komunitiku.test'],
            ['name' => 'Setiausaha A', 'password' => Hash::make('password')]
        );
        $setiausaha->syncRoles(['setiausaha']);
        $setiausaha->associations()->syncWithoutDetaching([
            $associationA->id => ['joined_at' => now()->toDateString(), 'is_active' => true],
        ]);

        $bendahari = User::updateOrCreate(
            ['email' => 'bendahari@komunitiku.test'],
            ['name' => 'Bendahari A', 'password' => Hash::make('password')]
        );
        $bendahari->syncRoles(['bendahari']);
        $bendahari->associations()->syncWithoutDetaching([
            $associationA->id => ['joined_at' => now()->toDateString(), 'is_active' => true],
        ]);
    }
}
