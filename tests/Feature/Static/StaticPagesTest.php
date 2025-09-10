<?php

namespace Tests\Feature\Static;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('static.home', absolute: false));
        $response->assertOk();
    }

    public function test_about_page_is_accessible(): void
    {
        $response = $this->get(route('static.about', absolute: false));
        $response->assertOk();
    }

    public function test_contact_page_is_accessible(): void
    {
        $response = $this->get(route('static.contact', absolute: false));
        $response->assertOk();
    }

    public function test_workout_online_page_is_accessible(): void
    {
        $response = $this->get(route('static.workout-online', absolute: false));
        $response->assertOk();
    }

    public function test_workout_in_studio_page_is_accessible(): void
    {
        $response = $this->get(route('static.workout-in-studio', absolute: false));
        $response->assertOk();
    }

    public function test_privacy_policy_page_is_accessible(): void
    {
        $response = $this->get(route('privacy-policy', absolute: false));
        $response->assertOk();
    }

    public function test_cookie_policy_page_is_accessible(): void
    {
        $response = $this->get(route('cookie-policy', absolute: false));
        $response->assertOk();
    }
}
