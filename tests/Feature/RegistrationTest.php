<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Location;
use Laravel\Jetstream\Jetstream;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? true : false,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

        $this->assertDatabaseHas("accounts",[
            'name' => "Test's Account"
        ]);

        $this->assertDatabaseHas("locations",[
            'name' => "Test's Location"
        ]);

        $this->assertDatabaseHas("users",[
            'name' => "Test User"
        ]);

        //assert correct user relationships
        $user = Jetstream::findUserByEmailOrFail('test@example.com')->with(['currentLocation.account'])->firstOrFail();

        // user is assigned to new location - has role for laction?
        $this->assertInstanceOf(Location::class, $user->currentLocation);

        // location is assigned to Account
        $this->assertInstanceOf(Account::class, $user->currentLocation->account);

        // user owns new account
        $account = $user->ownedAccount;
        $this->assertInstanceOf(Account::class, $account);
    }
}
