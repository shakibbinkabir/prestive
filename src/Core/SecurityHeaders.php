<?php
declare(strict_types=1);

namespace App\Core;

class SecurityHeaders
{
    public static function apply(bool $isHttps): void
    {
        if (!defined('SECURE_HEADERS') || !SECURE_HEADERS) {
            return;
        }

        // Common security headers
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        // Also set via CSP, but X-Frame-Options for legacy
        header('X-Frame-Options: DENY');

        // CSP
        if (defined('CSP_ENABLED') && CSP_ENABLED) {
            $imgSrc = "'self'" . (defined('CSP_IMG_ALLOW_DATA') && CSP_IMG_ALLOW_DATA ? ' data: blob:' : '');
            $scriptSrc = "'self' https://cdn.tailwindcss.com https://unpkg.com 'unsafe-inline'"; // inline as we don't use nonces here
            $styleSrc = "'self' 'unsafe-inline'";
            $csp = [
                "default-src 'self'",
                "script-src $scriptSrc",
                "style-src $styleSrc",
                "img-src $imgSrc",
                "connect-src 'self'",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ];
            header('Content-Security-Policy: ' . implode('; ', $csp));
        }

        // HSTS only in production and HTTPS
        if (defined('APP_ENV') && APP_ENV === 'production' && $isHttps) {
            $maxAge = defined('HSTS_MAX_AGE') ? (int)HSTS_MAX_AGE : 31536000;
            header('Strict-Transport-Security: max-age=' . $maxAge . '; includeSubDomains');
        }
    }
}
