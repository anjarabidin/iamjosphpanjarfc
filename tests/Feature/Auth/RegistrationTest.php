<?php

use App\Models\User;
use App\Models\Journal;
use Illuminate\Support\Facades\Hash;

it('renders the registration screen', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('can register a new user with valid data', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    // Create a journal first as registration requires selecting at least one journal
    $journal = Journal::factory()->create(['enabled' => true]);

    $response = $this->post('/register', [
        'given_name' => 'John',
        'family_name' => 'Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'affiliation' => 'University of Testing',
        'country' => 'ID',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'privacy_consent' => true,
        'journals' => [$journal->id],
    ]);

    $this->assertAuthenticated();
    
    // Check if user exists in database
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'username' => 'johndoe',
    ]);

    // OJS Style redirect to journal selection
    $response->assertRedirect(route('journal.select'));
});

it('fails registration with password mismatch', function () {
    $journal = Journal::factory()->create(['enabled' => true]);

    $response = $this->post('/register', [
        'given_name' => 'John',
        'family_name' => 'Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'affiliation' => 'University of Testing',
        'country' => 'ID',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'wrong_password',
        'privacy_consent' => true,
        'journals' => [$journal->id],
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('fails registration with duplicate email', function () {
    $existingUser = User::factory()->create(['email' => 'duplicate@example.com']);
    $journal = Journal::factory()->create(['enabled' => true]);

    $response = $this->post('/register', [
        'given_name' => 'Jane',
        'family_name' => 'Doe',
        'email' => 'duplicate@example.com',
        'username' => 'janedoe',
        'affiliation' => 'University of Testing',
        'country' => 'ID',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'privacy_consent' => true,
        'journals' => [$journal->id],
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('redirects authenticated users away from registration screen', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/register');

    $response->assertRedirect(route('dashboard'));
});
