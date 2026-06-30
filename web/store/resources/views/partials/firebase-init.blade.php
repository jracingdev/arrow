<script data-cfasync="false">
window.__firebaseConfig = @json(config('firebase.client'));
(function () {
    var cfg = window.__firebaseConfig;
    if (cfg && cfg.apiKey && typeof firebase !== 'undefined' && !firebase.apps.length) {
        firebase.initializeApp(cfg);
    }
})();
</script>
<script data-cfasync="false" src="{{ asset('js/jquery.cookie.js') }}"></script>
<script data-cfasync="false" src="{{ asset('js/jquery.validate.js') }}"></script>
