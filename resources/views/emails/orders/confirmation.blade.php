<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Ordine - {{ config('app.name') }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', 'Source Sans Pro', sans-serif; background:#f7f7f7; padding:20px;">
    <div style="max-width:700px;margin:0 auto;background:#ffffff;border:1px solid #eee;border-radius:10px;overflow:hidden;">
        <div style="background:#36583d;color:#fff;padding:16px 20px;">
            <h2 style="margin:0;font-weight:700;letter-spacing:.5px;">Grazie per il tuo acquisto, {{ $user->name }}!</h2>
        </div>
        <div style="padding:20px;">
            <p style="margin:0 0 14px;">Abbiamo ricevuto il tuo ordine e lo abbiamo elaborato con successo. Ecco un riepilogo del tuo acquisto:</p>
            
            <div style="border: 1px solid #eee; border-radius: 8px; margin-bottom: 20px;">
                @php $total = 0; @endphp
                @foreach($payments as $payment)
                <div style="padding: 10px 15px; border-bottom: 1px solid #eee;">
                    <span>{{ $payment->course->name ?? 'Corso non disponibile' }}</span>
                    <strong style="float: right;">€ {{ number_format($payment->amount_total / 100, 2, ',', '.') }}</strong>
                </div>
                @php $total += $payment->amount_total; @endphp
                @endforeach
                <div style="padding: 10px 15px; background: #f9fafb; font-size: 1.1em;">
                    <strong>TOTALE</strong>
                    <strong style="float: right;">€ {{ number_format($total / 100, 2, ',', '.') }}</strong>
                </div>
            </div>

            <p style="margin:0 0 14px;">Puoi accedere ai tuoi nuovi corsi direttamente dalla tua dashboard.</p>
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('dashboard') }}" style="background-color: #f6e849; color: #36583d; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Vai ai tuoi corsi</a>
            </div>
        </div>
        <div style="background:#f6e849;color:#36583d;padding:12px 20px;font-size:12px;text-align:center;">
            <div>Email inviata automaticamente da {{ config('app.name') }}.</div>
        </div>
    </div>
</body>
</html>
