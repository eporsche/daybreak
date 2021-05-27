<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = \config('auth.admin.admin_auth_email');
        $password = \config('auth.admin.admin_auth_password');

        app(CreatesNewUsers::class)->create([
            'name' => 'Admin User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? true : false,
        ]);
    }
}
