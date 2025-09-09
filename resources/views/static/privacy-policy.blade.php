@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <div class="prose max-w-none">
                <h2 style="color:#36583c">Informazioni sulla Privacy</h2>
                <p>
                    La tutela dei tuoi dati personali è importante. In questa pagina descriviamo come raccogliamo e trattiamo i
                    tuoi dati quando utilizzi la piattaforma. Per qualunque richiesta puoi contattarci tramite i canali di supporto
                    indicati nel sito.
                </p>

                <h3 style="color:#36583c">Dati trattati</h3>
                <ul>
                    <li>Dati di account (nome, email) e attività sulla piattaforma necessari all’erogazione dei servizi.</li>
                    <li>Dati tecnici di navigazione (indirizzo IP, user agent) per sicurezza e manutenzione.</li>
                </ul>

                <h2 style="color:#36583c">Cookie Policy</h2>
                <p>
                    Utilizziamo cookie tecnici necessari al funzionamento della piattaforma e, previo consenso, cookie di
                    profilazione/analitici per migliorare l’esperienza d’uso. I cookie possono essere gestiti tramite il banner
                    di consenso o dalle impostazioni del browser.
                </p>

                <h3 style="color:#36583c">Tipologie di cookie</h3>
                <ul>
                    <li><strong>Cookie tecnici</strong>: necessari per fornire le funzionalità di base del sito (autenticazione, sicurezza, preferenze).</li>
                    <li><strong>Cookie analitici e di profilazione</strong>: utilizzati per statistiche d’uso e personalizzazione, attivati solo col tuo consenso.
                    </li>
                </ul>

                <div class="mt-6 p-4 rounded-lg" style="background:#fffbe6; border-left: 4px solid #f4e648;">
                    <p class="m-0">
                        Puoi modificare o revocare il consenso ai cookie in qualsiasi momento cancellando i cookie dal tuo browser. Alla visita successiva,
                        il banner ti verrà nuovamente mostrato.
                    </p>
                </div>

                <h3 class="mt-8" style="color:#36583c">Contatti</h3>
                <p>Per maggiori informazioni o richieste relative alla privacy, contattaci tramite i riferimenti presenti sul sito.</p>
        </div>
    </div>
</div>
@endsection
