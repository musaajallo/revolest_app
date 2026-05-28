<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFeedbackLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_links_feedback_and_home_drops_see_all_forms(): void
    {
        \App\Models\Page::create(['title' => 'Home', 'slug' => 'home', 'is_published' => true]);

        $contact = $this->get('/contact');
        $contact->assertOk();
        $contact->assertSee('/forms/customer-feedback');
        $contact->assertSee('Share Your Feedback');

        $home = $this->get('/');
        $home->assertOk();
        $home->assertDontSee('See all forms');
    }
}
