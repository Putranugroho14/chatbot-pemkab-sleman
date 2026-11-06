<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin IT Diskominfo',
                'email' => 'superadmin@slemankab.go.id',
                'password' => Hash::make('SuperAdmin123!'),
                'role' => 'super_admin',
                'dinas' => null,
            ],
            [
                'name' => 'Admin Kominfo Sleman',
                'email' => 'admin.kominfo@slemankab.go.id',
                'password' => Hash::make('AdminKominfo123!'),
                'role' => 'dinas_admin',
                'dinas' => 'kominfo',
            ],
            [
                'name' => 'Admin Pariwisata Sleman',
                'email' => 'admin.pariwisata@slemankab.go.id',
                'password' => Hash::make('AdminPariwisata123!'),
                'role' => 'dinas_admin',
                'dinas' => 'pariwisata',
            ],
            [
                'name' => 'Admin Dukcapil Sleman',
                'email' => 'admin.dukcapil@slemankab.go.id',
                'password' => Hash::make('AdminDukcapil123!'),
                'role' => 'dinas_admin',
                'dinas' => 'dukcapil',
            ],
            [
                'name' => 'Staff Kominfo Sleman',
                'email' => 'staff.kominfo@slemankab.go.id',
                'password' => Hash::make('StaffKominfo123!'),
                'role' => 'staff',
                'dinas' => 'kominfo',
            ],
            [
                'name' => 'Staff Pariwisata Sleman',
                'email' => 'staff.pariwisata@slemankab.go.id',
                'password' => Hash::make('StaffPariwisata123!'),
                'role' => 'staff',
                'dinas' => 'pariwisata',
            ],
            [
                'name' => 'Staff Dukcapil Sleman',
                'email' => 'staff.dukcapil@slemankab.go.id',
                'password' => Hash::make('StaffDukcapil123!'),
                'role' => 'staff',
                'dinas' => 'dukcapil',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}

