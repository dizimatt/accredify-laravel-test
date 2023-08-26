<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            'name' => 'company 1',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company1-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 2',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company2-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 3',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company3-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 4',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company4-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 5',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company5-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 6',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company6-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 7',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company7-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 8',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company8-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 9',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company9-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 10',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company10-logo.png'
        ]);
        DB::table('companies')->insert([
            'name' => 'company 11',
            'email' => Str::random(10) . '@admin.com',
            'logo' => 'company11-logo.png'
        ]);
    }
}
