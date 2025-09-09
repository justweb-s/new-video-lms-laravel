@if($cookieConsentConfig['enabled'] && ! $alreadyConsentedWithCookies)

    @include('cookie-consent::dialogContents')

    <script>

        window.laravelCookieConsent = (function () {

            const COOKIE_VALUE = 1;
            const COOKIE_DENY_VALUE = 0;
            const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';

            function consentWithCookies() {
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', COOKIE_VALUE, {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            function cookieValueExists(name, value) {
                return document.cookie.split('; ').indexOf(name + '=' + value) !== -1;
            }

            function cookieExists(name) {
                return cookieValueExists(name, COOKIE_VALUE);
            }

            function cookieDeniedExists(name) {
                return cookieValueExists(name, COOKIE_DENY_VALUE);
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');

                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value
                    + ';expires=' + date.toUTCString()
                    + ';domain=' + COOKIE_DOMAIN
                    + ';path=/{{ config('session.secure') ? ';secure' : null }}'
                    + '{{ config('session.same_site') ? ';samesite='.config('session.same_site') : null }}';
            }

            if (cookieExists('{{ $cookieConsentConfig['cookie_name'] }}') || cookieDeniedExists('{{ $cookieConsentConfig['cookie_name'] }}')) {
                hideCookieDialog();
            }

            const buttons = document.getElementsByClassName('js-cookie-consent-agree');

            for (let i = 0; i < buttons.length; ++i) {
                buttons[i].addEventListener('click', consentWithCookies);
            }

            // Deny button(s)
            const denyButtons = document.getElementsByClassName('js-cookie-consent-deny');
            function denyCookies() {
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', COOKIE_DENY_VALUE, {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }
            for (let i = 0; i < denyButtons.length; ++i) {
                denyButtons[i].addEventListener('click', denyCookies);
            }

            // Preferences modal
            const prefButtons = document.getElementsByClassName('js-cookie-consent-preferences');
            const prefSave = document.getElementById('js-cookie-consent-save-preferences');
            const prefClose = document.getElementById('js-cookie-consent-close-preferences');
            const modalBackdrop = document.getElementById('ccBackDrop');
            const modal = document.getElementById('ccModal');
            function openPreferences() {
                if (modalBackdrop) modalBackdrop.style.display = 'block';
                if (modal) modal.style.display = 'flex';
            }
            function closePreferences() {
                if (modalBackdrop) modalBackdrop.style.display = 'none';
                if (modal) modal.style.display = 'none';
            }
            for (let i = 0; i < prefButtons.length; ++i) {
                prefButtons[i].addEventListener('click', openPreferences);
            }
            if (prefClose) {
                prefClose.addEventListener('click', closePreferences);
            }
            if (prefSave) {
                prefSave.addEventListener('click', function() {
                    const analytics = document.getElementById('ccPrefAnalytics');
                    const value = analytics && analytics.checked ? COOKIE_VALUE : COOKIE_DENY_VALUE;
                    setCookie('{{ $cookieConsentConfig['cookie_name'] }}', value, {{ $cookieConsentConfig['cookie_lifetime'] }});
                    closePreferences();
                    hideCookieDialog();
                });
            }

            return {
                consentWithCookies: consentWithCookies,
                hideCookieDialog: hideCookieDialog
            };
        })();
    </script>

@endif
