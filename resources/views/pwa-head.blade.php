<head>
     <!-- PWA Manifest -->
     <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#000000">
        <meta name="background-color" content="#ffffff">
        <!-- PWA Icons (optional, for iOS support) -->
        <link rel="apple-touch-icon" sizes="72x72" href="/images/icons/icon.png">

        <!-- Register Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/pwabuilder-sw.js');
                });
            }
        </script>

</head>