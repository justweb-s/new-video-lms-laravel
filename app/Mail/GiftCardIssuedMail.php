<?php

namespace App\Mail;

use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GiftCardIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public GiftCard $giftCard;

    public function __construct(GiftCard $giftCard)
    {
        $this->giftCard = $giftCard;
    }

    public function build()
    {
        return $this->subject('Hai ricevuto una Gift Card - ' . ($this->giftCard->course->name ?? config('app.name')))
            ->view('emails.giftcards.issued');
    }
}
