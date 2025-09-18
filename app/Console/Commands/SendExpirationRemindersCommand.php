<?php

namespace App\Console\Commands;

use App\Mail\CourseExpirationMail;
use App\Models\Enrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpirationRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-expiration-reminders {--days=7 : Numero di giorni prima della scadenza per inviare il promemoria.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scansiona le iscrizioni in scadenza e invia un promemoria via email agli utenti.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Avvio della scansione delle iscrizioni in scadenza...');

        $daysBefore = (int) $this->option('days');
        $targetDate = now()->addDays($daysBefore)->toDateString();

        $enrollments = Enrollment::with(['user', 'course'])
            ->where('is_active', true)
            ->whereDate('expires_at', $targetDate)
            ->get();

        if ($enrollments->isEmpty()) {
            $this->info('Nessuna iscrizione in scadenza trovata per oggi.');
            return 0;
        }

        $this->info("Trovate {$enrollments->count()} iscrizioni in scadenza tra {$daysBefore} giorni. Invio email in corso...");

        $sentCount = 0;
        foreach ($enrollments as $enrollment) {
            if ($enrollment->user && $enrollment->course) {
                try {
                                        try {
                        Mail::to($enrollment->user->email)->send(new CourseExpirationMail($enrollment));
                        $sentCount++;
                    } catch (\Throwable $e) {
                        $this->error("Impossibile inviare email a {$enrollment->user->email} per il corso {$enrollment->course->id}.");
                        Log::error("Errore invio email di scadenza: " . $e->getMessage());
                    }
                } catch (\Throwable $e) {
                    $this->error("Impossibile inviare email a {$enrollment->user->email} per il corso {$enrollment->course->id}.");
                    Log::error("Errore invio email di scadenza: " . $e->getMessage());
                }
            }
        }

        $this->info("Completato. Inviate {$sentCount} email di promemoria.");
        return 0;
    }
}
