<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class WelcomePageTest extends TestCase
{
    /** @test */
    public function guest_can_view_welcome_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    /** @test */
    public function welcome_page_loads_successfully()
    {
        $response = $this->get(route('welcome'));

        $response->assertStatus(200);
        $response->assertSeeText('PoLuv', false); // Case insensitive
    }

    /** @test */
    public function authenticated_user_is_redirected_to_home()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('home'));
    }
}

