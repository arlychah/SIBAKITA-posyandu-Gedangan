<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guest_is_redirected_to_login_before_accessing_dashboard()
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_page_is_accessible_to_guests()
    {
        $this->get('/login')->assertOk();
    }
}
