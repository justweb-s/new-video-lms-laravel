<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il tuo corso sta per scadere - {{ config('app.name') }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', 'Source Sans Pro', sans-serif; background:#f7f7f7; padding:20px;">
    <div style="max-width:700px;margin:0 auto;background:#ffffff;border:1px solid #eee;border-radius:10px;overflow:hidden;">
        <div style="background:#36583d;color:#fff;padding:16px 20px;">
            <h2 style="margin:0;font-weight:700;letter-spacing:.5px;">Attenzione, {{ $enrollment->user->name }}!</h2>
        </div>
        <div style="padding:20px;">
            <p style="margin:0 0 14px;">Volevamo informarti che il tuo accesso al corso <strong>"{{ $enrollment->course->name }}"</strong> sta per scadere.</p>
            <div style="background:#f9fafb;border:1px solid #eee;border-radius:8px;padding:14px 16px;margin-bottom:16px; text-align: center;">
                <p style="margin:0 0 6px;"><strong>Data di scadenza:</strong></p>
                <p style="font-size: 1.2em; font-weight: bold; margin: 0;">{{ $enrollment->expires_at->format('d/m/Y') }}</p>
            </div>
            <p style="margin:0 0 14px;">Per non perdere l'accesso ai contenuti, ti consigliamo di rinnovare la tua iscrizione.</p>
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('catalog.show', $enrollment->course) }}" style="background-color: #f6e849; color: #36583d; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Rinnova ora</a>
            </div>
        </div>
        <div style="background:#f6e849;color:#36583d;padding:12px 20px;font-size:12px;text-align:center;">
            <div>Email inviata automaticamente da {{ config('app.name') }}.</div>
        </div>
    </div>
</body>
</html>
