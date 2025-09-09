<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-2 z-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <p class="cookie-consent__message">
                {!! trans('cookie-consent::texts.message') !!}
            </p>
            <div class="flex items-center gap-2">
                <button type="button" class="js-cookie-consent-deny cookie-consent__deny">
                    {{ trans('cookie-consent::texts.deny') }}
                </button>
                <button type="button" class="js-cookie-consent-preferences cookie-consent__preferences">
                    {{ trans('cookie-consent::texts.preferences') }}
                </button>
                <button type="button" class="js-cookie-consent-agree cookie-consent__agree">
                    {{ trans('cookie-consent::texts.agree') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preferences Modal -->
<div id="ccBackDrop" class="cc-modal-backdrop"></div>
<div id="ccModal" class="cc-modal">
    <div class="cc-card">
        <div class="cc-header">
            <span>Preferenze Cookie</span>
            <button type="button" id="js-cookie-consent-close-preferences" class="cc-btn">✕</button>
        </div>
        <div class="cc-body">
            <div class="space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-md" style="background:#f3f4f6">
                    <input type="checkbox" checked disabled>
                    <div>
                        <div class="font-semibold">Cookie tecnici necessari</div>
                        <div class="text-sm text-gray-600">Necessari al funzionamento del sito e per fornire le funzionalità essenziali.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-md" style="background:#fffbe6; border-left:3px solid #f4e648;">
                    <input id="ccPrefAnalytics" type="checkbox">
                    <div>
                        <div class="font-semibold">Cookie analitici/profilazione</div>
                        <div class="text-sm text-gray-700">Utilizzati per statistiche e personalizzazione. Abilita solo se accetti.</div>
                    </div>
                </div>
                <div class="text-sm">
                    Consulta la <a href="/privacy-policy" target="_blank" rel="noopener" style="color:#36583c; text-decoration:underline;">Privacy Policy</a> e la <a href="/cookie-policy" target="_blank" rel="noopener" style="color:#36583c; text-decoration:underline;">Cookie Policy</a> per maggiori informazioni.
                </div>
            </div>
        </div>
        <div class="cc-actions">
            <button type="button" id="js-cookie-consent-save-preferences" class="cc-btn cc-btn-secondary">Salva preferenze</button>
        </div>
    </div>
</div>
