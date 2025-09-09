{{--
  Questo partial viene incluso solo dopo il consenso ai cookie
  (vedi includeIf('partials.analytics-consent') nei layout).
  Inserisci qui gli script di terze parti (es. Google Analytics / Tag Manager)
  che devono essere caricati SOLO dopo consenso.
--}}

{{-- Esempio GA4 (sostituisci G-XXXX con il tuo ID e rimuovi i commenti) --}}
{{--
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);} 
  gtag('js', new Date());
  gtag('config', 'G-XXXX');
</script>
--}}

{{-- Esempio Google Tag Manager (rimuovi i commenti e sostituisci GTM-XXXX) --}}
{{--
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXX');</script>
--}}
