<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_page_is_available()
    {
        $this->get('/login')->assertStatus(200);
    }
}
