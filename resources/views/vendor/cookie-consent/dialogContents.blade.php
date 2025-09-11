<div x-data="{
        open: true,
        modal: false,
        consent: {
            analytics: false
        },
        init() {
            try {
                const existingConsent = JSON.parse(document.cookie.split('; ').find(row => row.startsWith('{{ config('cookie-consent.cookie_name') }}='))?.split('=')[1] || 'null');
                if (existingConsent) {
                    this.open = false;
                }
            } catch (e) {
                // Se il cookie non è un JSON valido o non esiste, il banner rimane aperto
            }
        },
        consentWithCookies() {
            let consentValue = {
                technical: true,
                analytics: this.consent.analytics,
                timestamp: new Date().getTime()
            };
            this.setCookie('{{ config('cookie-consent.cookie_name') }}', JSON.stringify(consentValue), {{ config('cookie-consent.cookie_lifetime') }});
            this.open = false;
            this.modal = false;
            if (consentValue.analytics) {
                // Aggiungiamo un piccolo ritardo per essere sicuri che il cookie sia salvato prima del reload
                setTimeout(() => window.location.reload(), 100);
            }
        },
        consentWithAllCookies() {
            this.consent.analytics = true;
            this.consentWithCookies();
        },
        denyCookies() {
            let consentValue = {
                technical: true,
                analytics: false,
                timestamp: new Date().getTime()
            };
            this.setCookie('{{ config('cookie-consent.cookie_name') }}', JSON.stringify(consentValue), {{ config('cookie-consent.cookie_lifetime') }});
            this.open = false;
            this.modal = false;
        },
        setCookie(name, value, expirationInDays) {
            const date = new Date();
            date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
            const domain = '{{ config('session.domain') ?? request()->getHost() }}';
            document.cookie = `${name}=${value};expires=${date.toUTCString()};domain=${domain};path=/;{{ config('session.secure') ? 'secure;' : '' }}{{ config('session.same_site') ? 'samesite='.config('session.same_site') : '' }}`;
        }
    }"
     x-show="open"
     x-cloak
     class="cookie-consent-container">

    <!-- Banner Principale -->
    <div class="js-cookie-consent cookie-consent" x-show="!modal" x-transition>
        <span class="cookie-consent__message">
            Questo sito utilizza cookie tecnici e, previo consenso, cookie analitici per migliorare l’esperienza. Leggi la nostra <a href="{{ route('cookie-policy') }}" target="_blank" rel="noopener">Cookie Policy</a>.
        </span>

        <div class="cookie-consent__actions">
            <button class="cookie-consent__preferences" @click="modal = true">
                {{ trans('cookie-consent::texts.preferences', [], 'it') }}
            </button>
            <button class="cookie-consent__deny" @click="denyCookies">
                {{ trans('cookie-consent::texts.deny', [], 'it') }}
            </button>
            <button class="js-cookie-consent-agree cookie-consent__agree" @click="consentWithAllCookies">
                {{ trans('cookie-consent::texts.agree', [], 'it') }}
            </button>
        </div>
    </div>

    <!-- Modale Preferenze -->
    <div class="cc-modal-backdrop" x-show="modal" x-transition.opacity style="display: block;"></div>
    <div class="cc-modal" x-show="modal" x-transition style="display: flex;">
        <div class="cc-card">
            <div class="cc-header">
                <h3>{{ trans('cookie-consent::texts.modal_title', [], 'it') }}</h3>
                <button @click="modal = false" class="text-white font-bold text-xl">&times;</button>
            </div>
            <div class="cc-body">
                <p class="mb-4">{{ trans('cookie-consent::texts.modal_body', [], 'it') }}</p>

                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold">{{ trans('cookie-consent::texts.modal_technical_title', [], 'it') }}</h4>
                        <p class="text-sm text-gray-600">{{ trans('cookie-consent::texts.modal_technical_description', [], 'it') }}</p>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3">
                        <div>
                            <h4 class="font-bold">{{ trans('cookie-consent::texts.modal_analytics_title', [], 'it') }}</h4>
                            <p class="text-sm text-gray-600">{{ trans('cookie-consent::texts.modal_analytics_description', [], 'it') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="consent.analytics" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-green-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="cc-actions">
                <button class="cc-btn cc-btn-primary" @click="consentWithCookies">{{ trans('cookie-consent::texts.modal_save', [], 'it') }}</button>
                <button class="cc-btn cc-btn-secondary" @click="consentWithAllCookies">{{ trans('cookie-consent::texts.modal_accept_all', [], 'it') }}</button>
            </div>
        </div>
    </div>
</div>