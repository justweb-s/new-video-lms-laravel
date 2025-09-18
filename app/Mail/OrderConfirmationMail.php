<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $payments;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Collection $payments
     */
    public function __construct(User $user, Collection $payments)
    {
        $this->user = $user;
        $this->payments = $payments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Conferma del tuo ordine su ' . config('app.name'))
            ->view('emails.orders.confirmation');
    }
}
