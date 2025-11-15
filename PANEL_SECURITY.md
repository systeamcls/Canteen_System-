# Filament Panel Security & Auto-Redirect System

## Overview

This system provides **role-based access control** and **automatic redirects** for Filament panels, preventing unauthorized access and ensuring users land on the correct dashboard based on their role.

## Key Features

### 1. **Automatic Role-Based Redirect**
After login, users are automatically redirected to their appropriate dashboard:

- **Admin** → `/admin`
- **Cashier** → `/cashier`
- **Tenant** (active) → `/tenant`
- **Customer** → `/home`

### 2. **Unauthorized Access Prevention**
Users cannot access panels they don't have permission for. Attempting to do so results in:
- Automatic logout
- Session invalidation
- Security logging
- Error message
- Redirect to login

### 3. **Security Logging**
All unauthorized access attempts are logged with:
- User ID and email
- User roles
- Attempted panel
- Full URL
- IP address
- User agent
- Timestamp

Logs are stored in: `storage/logs/laravel.log`

## Implementation Details

### Middleware: `EnsureWelcomeModalCompleted`

**Location**: `app/Http/Middleware/EnsureWelcomeModalCompleted.php`

**Function**: Intercepts all panel requests and validates user permissions.

**What it does**:
1. Checks if user is authenticated
2. Validates user has required role for requested panel
3. Logs unauthorized attempts
4. Logs out unauthorized users
5. Invalidates sessions for security
6. Regenerates tokens to prevent CSRF attacks

### Panel Access Rules

```
Admin Panel (/admin):
  ✅ Allowed: admin, cashier
  ❌ Denied: tenant, customer, guest

Tenant Panel (/tenant):
  ✅ Allowed: tenant (with is_active = true)
  ❌ Denied: admin, cashier, customer, guest, inactive tenants

Cashier Panel (/cashier):
  ✅ Allowed: cashier, admin
  ❌ Denied: tenant, customer, guest
```

### Role-Based Redirects

Each panel provider has a `homeUrl()` configuration that determines where to redirect users after login:

**AdminPanelProvider**:
```php
Admin/Cashier → /admin
Tenant → /tenant
Others → Logout & redirect to login
```

**TenantPanelProvider**:
```php
Tenant (active) → /tenant
Admin → /admin
Cashier → /cashier
Others → /home
```

**CashierPanelProvider**:
```php
Cashier/Admin → /cashier
Tenant (active) → /tenant
Others → /home
```

## Security Improvements Implemented

### 1. **Session Security**
```php
Auth::logout();
session()->invalidate();
session()->regenerateToken();
```
On unauthorized access:
- User is logged out completely
- Session is destroyed
- New CSRF token is generated

### 2. **Activity Logging**
```php
Log::warning('Unauthorized panel access attempt', [
    'user_id' => $user->id,
    'user_email' => $user->email,
    'user_roles' => $user->roles->pluck('name')->toArray(),
    'attempted_panel' => $panel,
    'url' => $request->fullUrl(),
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now()->toDateTimeString(),
]);
```

### 3. **Role Validation**
Multiple layers of authorization:
- Middleware check before panel access
- User model `canAccessPanel()` method (existing)
- Panel-specific role validation

### 4. **Active Status Check for Tenants**
Tenants must have `is_active = true` to access tenant panel:
```php
if (!$user->hasRole('tenant') || !$user->is_active) {
    // Deny access
}
```

## User Flows

### Admin User Login Flow
```
1. User logs in via /admin/login
2. Filament authenticates user
3. homeUrl() checks role → Admin
4. User redirected to /admin
5. Middleware allows access ✅
```

### Unauthorized Access Attempt Flow
```
1. Tenant user types /admin in URL
2. Middleware intercepts request
3. Checks user role → tenant
4. Logs security event
5. Logs out user
6. Invalidates session
7. Shows error message
8. Redirects to login ❌
```

### Customer Login Flow
```
1. Customer logs in (via WelcomeModal or other)
2. Has 'customer' role
3. Cannot access any Filament panel
4. Stays on customer area (/home)
```

## Monitoring Security Logs

### View Recent Unauthorized Attempts
```bash
tail -f storage/logs/laravel.log | grep "Unauthorized panel access"
```

### Count Unauthorized Attempts Today
```bash
grep "Unauthorized panel access" storage/logs/laravel-$(date +%Y-%m-%d).log | wc -l
```

### View Specific User's Attempts
```bash
grep "user_email.*[email protected]" storage/logs/laravel.log | grep "Unauthorized"
```

## Testing

### Test Cases

**1. Admin User Access**
- ✅ Can access /admin
- ✅ Can access /cashier
- ❌ Cannot access /tenant (should be logged out)

**2. Tenant User Access**
- ✅ Can access /tenant (if active)
- ❌ Cannot access /admin (should be logged out)
- ❌ Cannot access /cashier (should be logged out)

**3. Inactive Tenant**
- ❌ Cannot access /tenant
- Gets logged out

**4. Cashier User Access**
- ✅ Can access /cashier
- ✅ Can access /admin
- ❌ Cannot access /tenant (should be logged out)

**5. Customer Access**
- ❌ Cannot access any Filament panel
- Should stay on /home

## Additional Security Recommendations

### 1. **IP Whitelisting for Admin Panel** (Optional)
Add to `EnsureWelcomeModalCompleted` middleware:
```php
$allowedIps = ['192.168.1.1', '10.0.0.1'];
if ($request->is('admin/*') && !in_array($request->ip(), $allowedIps)) {
    // Deny access
}
```

### 2. **Session Timeout**
In `config/session.php`:
```php
'lifetime' => 120, // 2 hours
'expire_on_close' => true,
```

### 3. **Failed Login Monitoring**
Use Laravel's built-in throttling in Filament login pages.

### 4. **Two-Factor Authentication**
Already configured via:
- `FilamentTwoFactorAuth` (Admin Panel)
- `EnsureTwoFactorAuthenticated` (commented out for Tenant/Cashier)

### 5. **Email Notifications for Security Events**
Add to `logUnauthorizedAccess()`:
```php
Mail::to(config('mail.admin_email'))->send(
    new UnauthorizedAccessAttempt($user, $panel, $request)
);
```

## Configuration

### Disable Security Logging (Not Recommended)
In `EnsureWelcomeModalCompleted.php`, comment out:
```php
// $this->logUnauthorizedAccess($user, $panel, $request);
```

### Customize Error Messages
In `EnsureWelcomeModalCompleted.php`, update:
```php
session()->flash('error', 'Your custom message here');
```

### Skip Middleware for Specific Routes
In `EnsureWelcomeModalCompleted.php`:
```php
if ($request->is('admin/public/*')) {
    return $next($request);
}
```

## Troubleshooting

### Users Getting Logged Out Unexpectedly
- Check if user has correct role in database
- Verify `is_active` flag for tenants
- Check logs for security events

### Redirect Loop
- Verify homeUrl() logic in panel providers
- Check middleware skip conditions
- Ensure login routes are not blocked

### Logs Not Working
- Check `storage/logs` directory permissions
- Verify `LOG_CHANNEL` in `.env`
- Check log level configuration

## Database Schema Requirements

### Users Table
```sql
- id (primary key)
- email (unique)
- is_active (boolean, default: true)
```

### Roles
```sql
- admin
- tenant
- cashier
- customer
```

Managed by Spatie Laravel Permission package.

## Summary

This security system provides:
- ✅ **Automatic role-based redirects** - No manual panel selection needed
- ✅ **Unauthorized access prevention** - Can't access panels by URL
- ✅ **Security logging** - Track all unauthorized attempts
- ✅ **Session security** - Proper logout and invalidation
- ✅ **Role validation** - Multiple layers of authorization
- ✅ **Active status checks** - Inactive tenants can't log in

All security events are logged for monitoring and auditing purposes.

## Version History

- **v2.0.0** (2025-11-15): Simplified implementation
  - Removed panel selection modal
  - Added automatic role-based redirects
  - Enhanced security logging
  - Improved session management
