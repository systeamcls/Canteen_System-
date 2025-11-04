<!DOCTYPE html>
<html>

<head>
    <title>reCAPTCHA Test</title>
    <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
</head>

<body>
    <h1>reCAPTCHA Configuration Test</h1>

    <ul>
        <li><strong>Site Key:</strong> {{ $siteKey ?: 'NOT SET ❌' }}</li>
        <li><strong>Secret Key:</strong> {{ $secretKey }}</li>
        <li><strong>Site Key Exists:</strong> {{ $siteKeyExists ? 'YES ✅' : 'NO ❌' }}</li>
        <li><strong>Secret Key Exists:</strong> {{ $secretKeyExists ? 'YES ✅' : 'NO ❌' }}</li>
    </ul>

    <hr>

    <p>Open browser console (F12) and type:</p>
    <code>typeof grecaptcha</code>

    <p>Expected: "object"</p>

    <script>
        setTimeout(() => {
            console.log('grecaptcha type:', typeof grecaptcha);
            if (typeof grecaptcha !== 'undefined') {
                alert('✅ reCAPTCHA loaded successfully!');
            } else {
                alert('❌ reCAPTCHA failed to load!');
            }
        }, 2000);
    </script>
</body>

</html>
