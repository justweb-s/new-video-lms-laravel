<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvenuto/a su {{ config('app.name') }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', 'Source Sans Pro', sans-serif; background:#f7f7f7; padding:20px;">
    <div style="max-width:700px;margin:0 auto;background:#ffffff;border:1px solid #eee;border-radius:10px;overflow:hidden;">
        <div style="background:#36583d;color:#fff;padding:16px 20px;">
            <h2 style="margin:0;font-weight:700;letter-spacing:.5px;">Benvenuto/a, {{ $user->name }}!</h2>
        </div>
        <div style="padding:20px;">
            <p style="margin:0 0 14px;">Grazie per esserti registrato/a su {{ config('app.name') }}. Siamo felici di averti con noi.</p>
            <p style="margin:0 0 14px;">Ora puoi accedere alla tua area personale e iniziare subito a esplorare i nostri corsi.</p>
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('dashboard') }}" style="background-color: #f6e849; color: #36583d; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Vai alla tua Dashboard</a>
            </div>
            <p style="margin:0 0 14px;">Se hai domande, non esitare a contattarci.</p>
            <p>Buona formazione!</p>
        </div>
        <div style="background:#f6e849;color:#36583d;padding:12px 20px;font-size:12px;text-align:center;">
            <div>Email inviata automaticamente da {{ config('app.name') }}.</div>
        </div>
    </div>
</body>
</html>
