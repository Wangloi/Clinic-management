<?php
/**
 * Security utilities: secure sessions, CSRF tokens, and security headers
 */

/**
 * Determine if the request is HTTPS.
 */
function is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return true;
    if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) return true;
    // Check common proxy headers (only if you trust your proxy!)
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') return true;
    return false;
}

/**
 * Start a hardened session with strict cookie parameters and fingerprint checks.
 */
function start_secure_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = is_https();

    // Use strict session cookie settings
    $params = [
        'lifetime' => 0,
        'path' => '/',
        'domain' => 'localhost',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params($params);
    } else {
        // Fallback for older PHP (no samesite support via API)
        session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
    }

    session_start();

    // Create and validate a session fingerprint (helps mitigate hijacking)
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // Avoid breaking mobile networks with changing IPs: only first 2 octets for IPv4
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $parts = explode('.', $ip);
        $ipFingerprint = $parts[0] . '.' . $parts[1] . '.0.0';
    } else {
        // For IPv6 or unknown, keep minimal stability
        $ipFingerprint = substr($ip, 0, 4);
    }

    $fingerprint = hash('sha256', $ua . '|' . $ipFingerprint);

    if (!isset($_SESSION['__fingerprint'])) {
        $_SESSION['__fingerprint'] = $fingerprint;
        $_SESSION['__created_at'] = time();
    } elseif (!hash_equals($_SESSION['__fingerprint'], $fingerprint)) {
        // Fingerprint changed: regenerate session instead of blocking
        session_unset();
        $_SESSION['__fingerprint'] = $fingerprint;
        $_SESSION['__created_at'] = time();
    }

    // Periodically rotate the session ID (e.g., every 10 minutes)
    if (!isset($_SESSION['__rotated_at']) || (time() - (int)$_SESSION['__rotated_at']) > 600) {
        session_regenerate_id(true);
        $_SESSION['__rotated_at'] = time();
    }
}

/**
 * Set baseline security headers. Call early in the request lifecycle.
 */
function set_security_headers(): void {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    // MIME sniffing protection
    header('X-Content-Type-Options: nosniff');
    // Conservative referrer policy
    header('Referrer-Policy: no-referrer-when-downgrade');
    // Basic Content Security Policy
    // Adjust if you rely on additional external resources
    $csp = [
        "default-src 'self'",
        "script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data:",
        "connect-src 'self' https://cdn.jsdelivr.net",
        "frame-ancestors 'none'",
        "base-uri 'self'",
        "form-action 'self'"
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
}

/**
 * Get or create a CSRF token bound to the session.
 */
function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_secure_session();
    }
    if (empty($_SESSION['__csrf'])) {
        $_SESSION['__csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['__csrf'];
}

/**
 * Return a hidden input field with the CSRF token for embedding in forms.
 */
function csrf_input_field(string $name = 'csrf_token'): string {
    $token = csrf_token();
    return '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate a provided CSRF token against the session token.
 */
function verify_csrf_token(?string $token, string $name = 'csrf_token'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_secure_session();
    }
    $sessionToken = $_SESSION['__csrf'] ?? '';

    if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
        http_response_code(400);
        exit('Invalid CSRF token.');
    }
}

/**
 * Enforce POST-only access for state-changing endpoints.
 */
function require_post(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method Not Allowed');
    }
}

/**
 * Convenience: Require POST and validate CSRF from the standard field.
 */
function require_post_with_csrf(string $field = 'csrf_token'): void {
    require_post();
    $token = $_POST[$field] ?? null;
    verify_csrf_token($token, $field);
}
?>
