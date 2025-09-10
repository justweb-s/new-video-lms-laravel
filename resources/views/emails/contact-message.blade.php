<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo messaggio contatti</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', 'Source Sans Pro', sans-serif; background:#f7f7f7; padding:20px;">
    <div style="max-width:700px;margin:0 auto;background:#ffffff;border:1px solid #eee;border-radius:10px;overflow:hidden;">
        <div style="background:#36583d;color:#fff;padding:16px 20px;">
            <h2 style="margin:0;font-weight:700;letter-spacing:.5px;">Nuovo messaggio dal form Contatti</h2>
        </div>
        <div style="padding:20px;">
            <p style="margin:0 0 14px;">Hai ricevuto un nuovo messaggio dal sito EMY WORKOUT.</p>
            <div style="background:#f9fafb;border:1px solid #eee;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                <p style="margin:0 0 6px;"><strong>Nome:</strong> {{ $data['name'] }}</p>
                <p style="margin:0 0 6px;"><strong>Email:</strong> {{ $data['email'] }}</p>
                @if(!empty($data['phone']))
                <p style="margin:0 0 6px;"><strong>Telefono:</strong> {{ $data['phone'] }}</p>
                @endif
                @if(!empty($data['subject']))
                <p style="margin:0 0 6px;"><strong>Oggetto:</strong> {{ $data['subject'] }}</p>
                @endif
                <p style="margin:0 0 6px;"><strong>IP:</strong> {{ $data['ip'] ?? '-' }}</p>
            </div>
            <div>
                <p style="margin:0 0 8px;"><strong>Messaggio:</strong></p>
                <div style="white-space:pre-wrap; line-height:1.5;">{{ $data['message'] }}</div>
            </div>
        </div>
        <div style="background:#f6e849;color:#36583d;padding:12px 20px;font-size:12px;text-align:center;">
            <div>Questo messaggio è stato inviato dal form contatti del sito.</div>
        </div>
    </div>
</body>
</html>
