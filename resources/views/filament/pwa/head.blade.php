<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d6efd">
<link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js');
    });
}
</script>