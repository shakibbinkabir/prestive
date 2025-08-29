````
# Security Hardening

This app emits standard security headers. You can toggle them via `.env`.

## Headers applied

- Content-Security-Policy (CSP)
  - default-src 'self'
  - script-src 'self' https://cdn.tailwindcss.com https://unpkg.com 'unsafe-inline'
  - style-src 'self' 'unsafe-inline'
  - img-src 'self' data: blob: (configurable)
  - connect-src 'self'
  - frame-ancestors 'none'
- X-Frame-Options: DENY
- Referrer-Policy: no-referrer
- X-Content-Type-Options: nosniff
- Permissions-Policy: camera=(), microphone=(), geolocation=()
- Strict-Transport-Security (HSTS): only in `APP_ENV=production` and when HTTPS is detected; `HSTS_MAX_AGE` controls max-age.

## Env toggles

- `SECURE_HEADERS=true|false` — master switch (default true in production)
- `CSP_ENABLED=true|false` — enable/disable CSP (default true in production)
- `CSP_IMG_ALLOW_DATA=true|false` — whether to allow `data:` images (default true)
- `HSTS_MAX_AGE=31536000` — HSTS max-age when enabled

## Notes

- In local development you may disable CSP (`CSP_ENABLED=false`) if CDN script/style inlining causes issues.
- Always deploy behind HTTPS to benefit from HSTS.
- Avoid including sensitive internal fields in PDFs and CSVs; by default we exclude `draft_data` and admin-only notes.

````
