<?php

namespace Tests\Feature\Static;

use App\Mail\ContactMessage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_valid_submission_sends_email_and_redirects_back(): void
    {
        Mail::fake();
        Setting::set('contact.recipient_email', 'admin@example.com');

        $payload = [
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'phone' => '+391234567890',
            'subject' => 'Informazioni',
            'message' => 'Vorrei informazioni sul corso.',
        ];

        $response = $this->from(route('static.contact', absolute: false))
            ->post(route('static.contact.submit', absolute: false), $payload);

        $response->assertRedirect(route('static.contact', absolute: false));
        $response->assertSessionHas('status');

        Mail::assertSent(ContactMessage::class, function ($mailable) use ($payload) {
            return $mailable->hasTo('admin@example.com')
                && $mailable->data['name'] === $payload['name']
                && $mailable->data['email'] === $payload['email'];
        });
    }

    public function test_contact_form_validation_errors(): void
    {
        $response = $this->from(route('static.contact', absolute: false))
            ->post(route('static.contact.submit', absolute: false), [
                'name' => '',
                'email' => 'not-an-email',
                'message' => '',
            ]);

        $response->assertRedirect(route('static.contact', absolute: false));
        $response->assertSessionHasErrors(['name','email','message']);
    }
}
