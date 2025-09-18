<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Console\Command;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class TestOrderConfirmationEmail extends Command
{
    protected $signature = 'test:order-email {email} {course_id}';
    protected $description = 'Invia un\'email di test di conferma ordine';

    public function handle()
    {
        $email = $this->argument('email');
        $courseId = $this->argument('course_id');
    
        // Crea un utente temporaneo se non esiste
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Utente di Test',
                'password' => bcrypt('password'),
            ]
        );
    
        $course = Course::findOrFail($courseId);
    
        $payment = new Payment();
        $payment->user_id = $user->id;
        $payment->amount_total = 10000; // 100.00 EUR in centesimi
        $payment->currency = 'EUR';
        $payment->course_id = $course->id;
        $payment->status = 'paid';
        $payment->provider = 'test';
        $payment->save();
    
        try {
            Mail::to($user)->send(new OrderConfirmationMail($user, collect([$payment])));
            $this->info('Email inviata con successo!');
            $this->info('Controlla la tua casella di posta o il log di Mailtrap.');
        } catch (\Exception $e) {
            $this->error('Errore durante l\'invio dell\'email: ' . $e->getMessage());
        }
    }
}