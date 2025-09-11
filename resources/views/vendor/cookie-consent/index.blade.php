@if(config('cookie-consent.enabled') && ! $alreadyConsentedWithCookies)

    @include('cookie-consent::dialogContents')

@endif
