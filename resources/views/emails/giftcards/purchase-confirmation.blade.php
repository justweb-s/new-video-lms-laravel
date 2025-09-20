<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma acquisto Gift Card</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background:#f7fafc; padding:24px; color:#111827;">
    <div style="max-width:640px; margin:0 auto;">
        <div style="text-align:center; padding:12px 0 16px 0;">
            <img src="https://www.emyworkout.it/wp-content/uploads/2024/10/EMY-WORKOUT-%E2%80%A2-Loghi-Finali_LOGO1-.svg" alt="{{ config('app.name') }}" style="height:42px; display:inline-block;" />
        </div>
    </div>
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:8px; padding:24px; box-shadow:0 1px 2px rgba(0,0,0,0.06);">
        <h1 style="margin:0 0 12px 0; font-size:20px;">Grazie per il tuo acquisto!</h1>
        <p style="margin:0 0 12px 0; line-height:1.6;">Abbiamo emesso una <strong>Gift Card</strong> per il corso <strong>{{ $giftCard->course->name }}</strong>.</p>

        <div style="margin:16px 0; padding:12px 16px; background:#f9fafb; border-radius:6px; border:1px solid #e5e7eb;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:0; vertical-align:top;">
                        <p style="margin:0 0 6px 0; color:#6b7280; font-size:12px;">Codice gift card</p>
                        <p style="margin:0; font-size:18px; font-weight:700; letter-spacing:1px; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">{{ $giftCard->code }}</p>
                    </td>
                    <td style="padding:0; text-align:right; vertical-align:top;">
                        <p style="margin:0 0 6px 0; color:#6b7280; font-size:12px;">Valore</p>
                        <p style="margin:0; font-size:16px; font-weight:700;">{{ number_format($giftCard->amount/100, 2, ',', '.') }} {{ strtoupper($giftCard->currency) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin:12px 0 16px 0;">
            <p style="margin:0; line-height:1.6;">
                Destinatario: <strong>{{ $giftCard->recipient_name }}</strong>
                @if(!empty($giftCard->recipient_email))
                    &lt;<a href="mailto:{{ $giftCard->recipient_email }}" style="color:#0ea5e9; text-decoration:none;">{{ $giftCard->recipient_email }}</a>&gt;
                @endif
            </p>
            @if(!empty($giftCard->message))
                <div style="margin-top:10px; padding:12px 16px; background:#f9fafb; border-radius:6px; border:1px solid #e5e7eb;">
                    <p style="margin:0 0 6px 0; color:#6b7280; font-size:12px;">Messaggio incluso:</p>
                    <p style="margin:0; white-space:pre-wrap;">{{ $giftCard->message }}</p>
                </div>
            @endif
        </div>

        <p style="margin:0 0 14px 0; line-height:1.6;">
            Abbiamo inviato una email al destinatario con il codice e le istruzioni per il riscatto.
            Puoi anche condividere direttamente questo link:
        </p>

        <div style="margin:16px 0; text-align:center;">
            <a href="{{ route('giftcards.redeem', ['code' => $giftCard->code]) }}" style="display:inline-block; background:#0ea5e9; color:#fff; text-decoration:none; padding:12px 20px; border-radius:6px; font-weight:700; box-shadow:0 1px 1px rgba(0,0,0,.05);">Link per il riscatto</a>
        </div>

        <p style="margin:0 0 8px 0; color:#6b7280; font-size:12px;">Oppure copia e incolla questo link nel messaggio al destinatario:</p>
        <p style="margin:0 0 16px 0; font-size:12px; word-break:break-all;">
            {{ route('giftcards.redeem', ['code' => $giftCard->code]) }}
        </p>

        <p style="margin:24px 0 0 0; font-size:12px; color:#6b7280;">Se non ti aspettavi questa email, puoi ignorarla.</p>
    </div>
    <p style="max-width:640px; margin:12px auto 0; text-align:center; color:#9ca3af; font-size:12px;">&copy; {{ date('Y') }} {{ config('app.name') }}. Tutti i diritti riservati.</p>
</body>
</html>
