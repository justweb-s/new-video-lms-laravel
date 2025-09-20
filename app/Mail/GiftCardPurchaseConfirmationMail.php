<?php

namespace App\Mail;

use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GiftCardPurchaseConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public GiftCard $giftCard;

    public function __construct(GiftCard $giftCard)
    {
        $this->giftCard = $giftCard;
    }

    public function build()
    {
        $courseName = $this->giftCard->course->name ?? config('app.name');
        return $this->subject('Conferma acquisto Gift Card - ' . $courseName)
            ->view('emails.giftcards.purchase-confirmation');
    }
}
