<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Negeri dan Wilayah Persekutuan Malaysia (rujukan borang).
     *
     * @var list<array{name: string, code: string}>
     */
    private const ROWS = [
        ['name' => 'Johor', 'code' => 'JHR'],
        ['name' => 'Kedah', 'code' => 'KDH'],
        ['name' => 'Kelantan', 'code' => 'KTN'],
        ['name' => 'Melaka', 'code' => 'MLK'],
        ['name' => 'Negeri Sembilan', 'code' => 'NSN'],
        ['name' => 'Pahang', 'code' => 'PHG'],
        ['name' => 'Perak', 'code' => 'PRK'],
        ['name' => 'Perlis', 'code' => 'PLS'],
        ['name' => 'Pulau Pinang', 'code' => 'PNG'],
        ['name' => 'Sabah', 'code' => 'SBH'],
        ['name' => 'Sarawak', 'code' => 'SWK'],
        ['name' => 'Selangor', 'code' => 'SGR'],
        ['name' => 'Terengganu', 'code' => 'TRG'],
        ['name' => 'Wilayah Persekutuan Kuala Lumpur', 'code' => 'KUL'],
        ['name' => 'Wilayah Persekutuan Labuan', 'code' => 'LBN'],
        ['name' => 'Wilayah Persekutuan Putrajaya', 'code' => 'PJY'],
    ];

    public function run(): void
    {
        foreach (self::ROWS as $row) {
            State::updateOrCreate(
                ['code' => $row['code']],
                ['name' => $row['name']]
            );
        }
    }
}
