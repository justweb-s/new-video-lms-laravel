<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseExpirationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Enrollment $enrollment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Enrollment $enrollment)
    {
        // Carica esplicitamente le relazioni per evitare LazyLoadingException in coda
        $this->enrollment = $enrollment->loadMissing(['user', 'course']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Il tuo corso sta per scadere!')
            ->view('emails.courses.expiration');
    }
}
