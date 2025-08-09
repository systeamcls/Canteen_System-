# Security Configuration Guide

This document outlines the security measures implemented in the Laravel Canteen System.

## Environment Security

### .env File Protection
- Comprehensive `.gitignore` entries prevent environment file exposure
- Enhanced `.env.example` template with security-focused variables
- Multiple environment file patterns excluded (`.env.*`, backups, etc.)

### Environment Variables
Add these security-focused variables to your `.env` file:

```bash
# Session Security
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Authentication Security
AUTH_PASSWORD_RESET_EXPIRE=15
AUTH_PASSWORD_TIMEOUT=1800
AUTH_LOGIN_THROTTLE_ATTEMPTS=5
AUTH_LOGIN_THROTTLE_DECAY=300

# Security Headers
SECURITY_HEADERS_ENABLED=true
CONTENT_SECURITY_POLICY_ENABLED=true
HTTPS_ENFORCE=true

# Rate Limiting
RATE_LIMIT_AUTH_ATTEMPTS=10
RATE_LIMIT_AUTH_DECAY=300
RATE_LIMIT_API_ATTEMPTS=60
RATE_LIMIT_API_DECAY=60

# Redis Security
REDIS_SCHEME=tls
REDIS_TLS_CERT_VERIFY=true

# Sanctum Token Security
SANCTUM_TOKEN_EXPIRATION=60
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

## API Token Security

### Sanctum Configuration
- API tokens automatically hashed before storage (Laravel Sanctum default)
- Token expiration set to 60 minutes by default
- Plain text tokens only displayed once after creation
- Secure token generation and validation built-in

### Token Best Practices
- Tokens are never stored in plain text
- Users see tokens only once during creation
- Expired tokens are automatically invalidated
- Token permissions can be granularly controlled

## Authentication Security

### Password Security
- Password reset token lifetime: **15 minutes** (reduced from 60)
- Password confirmation timeout: **30 minutes** (reduced from 3 hours)
- Fortify handles login throttling (5 attempts per minute)

### Rate Limiting
- Authentication endpoints: 10 attempts per 5 minutes
- API endpoints: 60 attempts per minute
- Rate limiting applied to login, register, password reset routes

## Security Headers

The `SecurityHeaders` middleware applies:

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy` (disables sensitive browser features)
- `Content-Security-Policy` (configurable CSP)
- `Strict-Transport-Security` (HTTPS enforcement in production)

## Session Security

- Session encryption enabled by default
- Secure cookies in production
- HTTP-only cookies prevent XSS
- SameSite protection against CSRF

## Redis Security

- TLS encryption support for Redis connections
- Certificate verification enabled
- Secure credential handling
- Connection pooling with authentication

## Middleware Stack

### Security Middleware Applied
1. `SecurityHeaders` - Applied globally
2. `RateLimitAuth` - Applied to web routes
3. `CheckUserType` - Custom application logic

### Route Protection
- Authentication required for sensitive operations
- Rate limiting on auth endpoints
- CSRF protection on state-changing requests

## Production Checklist

Before deploying to production:

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure `HTTPS_ENFORCE=true`
4. Set secure session cookies: `SESSION_SECURE_COOKIE=true`
5. Configure Redis with TLS: `REDIS_SCHEME=tls`
6. Review and adjust rate limits as needed
7. Verify CSP policy matches your application needs
8. Ensure SSL certificates are properly configured

## Monitoring and Logs

- Failed authentication attempts are logged
- Rate limit violations are tracked
- Security header violations can be monitored via CSP reporting
- Session hijacking attempts logged via secure cookie settings

## Testing

Run security tests with:
```bash
php artisan test --filter SecurityMiddlewareTest
```

The test suite verifies:
- Security headers are properly applied
- Content Security Policy is configured
- Rate limiting functions correctly
- Session security is enforced