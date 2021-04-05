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
        $user = app(CreatesNewUsers::class)->create([
            'name' => 'Erik Porsche',
            'email' => 'porsche@mikroskop-center.de',
            'password' => 'admin1234',
            'password_confirmation' => 'admin1234',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? true : false,

        ]);

        //create a second user for testing
        app(CreatesNewUsers::class)->create([
            'name' => 'Richard Demming',
            'email' => 'demming@mikroskop-center.de',
            'password' => 'admin1234',
            'password_confirmation' => 'admin1234',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? true : false,
        ]);

        //invite him to our team
        Mail::fake();
        app(InvitesLocationMembers::class)->invite(
            $user,
            $user->ownedLocations()->first(),
            'demming@mikroskop-center.de',
            'admin'
        );

        //print link to accept team invitation
        $url = URL::signedRoute('location-invitations.accept', [
            'invitation' => tap($user->ownedLocations()->first())->locationInvitations()->first(),
        ]);

        echo "Accept invitation url: \n $url \n";

    }
}
