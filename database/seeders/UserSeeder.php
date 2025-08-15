<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use RuntimeException;
use Exception;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!env('DEFAULT_ADMIN_PASSWORD') || !env('DEFAULT_USER_PASSWORD')) {
            throw new RuntimeException('Default passwords must be set in .env file');
        }
        DB::beginTransaction();
        try {
            $admin_password = Hash::make(env('DEFAULT_ADMIN_PASSWORD'));
            $user_password = Hash::make(env('DEFAULT_USER_PASSWORD'));

            $users = [
                [
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'email' => 'root@gmail.com',
                    'phone_number' => '254746055487',
                    'password' => $admin_password,
                    'email_verified_at' => '2025-05-14 10:00:00',
                    'role' => 0,
                ],
                [
                    'first_name' => 'Admin',
                    'last_name' => 'Account',
                    'email' => 'admin@gmail.com',
                    'phone_number' => '254746055487',
                    'password' => $admin_password,
                    'email_verified_at' => '2025-05-14 10:00:00',
                    'role' => 1,
                ],
                [
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'user@gmail.com',
                    'phone_number' => '254746055487',
                    'password' => $user_password,
                    'email_verified_at' => '2025-05-14 10:00:00',
                ],
            ];

            foreach($users as $user) {
                User::create($user);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
