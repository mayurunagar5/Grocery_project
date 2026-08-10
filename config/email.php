<?php
// Simple email helper with optional SendGrid HTTP API fallback

// Load local secrets if present (define('SENDGRID_API_KEY', '...'), etc.)
if (file_exists(__DIR__ . '/secrets.php')) {
    require_once __DIR__ . '/secrets.php';
}

function env(string $key, $default = null) {
    // 1) PHP constant via secrets.php
    if (defined($key)) {
        $constVal = constant($key);
        if ($constVal !== null && $constVal !== '') return $constVal;
    }
    // 2) $_ENV superglobal
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    // 3) getenv
    $value = getenv($key);
    if ($value !== false && $value !== '') return $value;
    return $default;
}

function sendEmail(string $to, string $subject, string $plainText): array
{
    $to = trim($to);
    if ($to === '') {
        return [ 'success' => false, 'message' => 'Recipient email is required' ];
    }

    // 1) Try native mail()
    $fromEmail = env('MAIL_FROM_ADDRESS', 'no-reply@freshmart.local');
    $fromName  = env('MAIL_FROM_NAME', 'FreshMart');
    $headers   = [];
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    $headers[] = 'Reply-To: ' . $fromEmail;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    try {
        $sent = @mail($to, $subject, $plainText, implode("\r\n", $headers));
        if ($sent) {
            return [ 'success' => true ];
        }
    } catch (Throwable $e) {
        // ignore and fallback
    }

    // 2) Fallback to SendGrid HTTP API if configured
    $sendgridKey = env('SENDGRID_API_KEY');
    if ($sendgridKey) {
        $payload = [
            'personalizations' => [[ 'to' => [[ 'email' => $to ]] ]],
            'from' => [ 'email' => $fromEmail, 'name' => $fromName ],
            'subject' => $subject,
            'content' => [[ 'type' => 'text/plain', 'value' => $plainText ]],
        ];
        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $sendgridKey,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);
        if ($httpCode >= 200 && $httpCode < 300) {
            return [ 'success' => true ];
        }
        return [ 'success' => false, 'message' => 'SendGrid failed (' . $httpCode . '): ' . ($response ?: $err) ];
    }

    return [ 'success' => false, 'message' => 'mail() failed and no SENDGRID_API_KEY configured' ];
}

?>


