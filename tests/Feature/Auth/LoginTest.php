<?php

use App\Models\User;
use App\Models\Journal;
use Illuminate\Support\Facades\Hash;

it('renders the login screen', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('can login with valid credentials (email)', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $this->assertAuthenticated();
    
    // AuthController redirects to journal selection if no journal context and user has multiple/none
    $response->assertRedirect(route('journal.select'));
});

it('can login with valid credentials (username)', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
        'username' => 'testuser',
    ]);

    $response = $this->post('/login', [
        'email' => 'testuser', // AuthController uses 'email' field for both email/username
        'password' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('journal.select'));
});

it('fails login with incorrect password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong_password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors();
});

it('fails login with disabled account', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
        'email' => 'disabled@example.com',
        'disabled' => true,
    ]);

    $response = $this->post('/login', [
        'email' => 'disabled@example.com',
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertSessionHas('warning', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
});

it('redirects authenticated users away from login screen', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect(route('dashboard'));
});
