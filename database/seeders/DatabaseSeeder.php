<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Actions\InviteUserToLocation;
use App\Actions\Fortify\CreateNewUser;
use App\Contracts\InvitesLocationMembers;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        app(CreatesNewUsers::class)->create([
            'name' => 'Admin User',
            'email' => 'admin@daybreak.corp',
            'password' => 'admin1234',
            'password_confirmation' => 'admin1234',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? true : false,
        ]);
    }
}
