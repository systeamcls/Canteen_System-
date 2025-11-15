# Welcome Modal Feature

## Overview

This feature implements a **Welcome Modal** that users must go through before accessing any Filament panel. It prevents direct URL access to panels like `/admin`, `/tenant`, or `/cashier`, ensuring all users are presented with a panel selection interface first.

## Features

### 1. **Forced Panel Selection**
- Users cannot directly access panels via URL
- All authenticated users are redirected to a welcome modal first
- After selection, users are taken to their chosen panel

### 2. **Role-Based Panel Access**
- Only shows panels that the user has permission to access
- Validates user roles before displaying panel options
- Logs out unauthorized users who attempt to access restricted panels

### 3. **Session-Based Tracking**
- Welcome modal is shown once per session
- Session tracking prevents repeated modal displays
- Remembers intended URL and redirects after modal completion

### 4. **Enhanced Security**
- Double-layer authorization check (middleware + User model)
- Automatic logout for unauthorized access attempts
- Flash messages for better user experience

## How It Works

### Flow Diagram

```
User Login
    ↓
[Authentication]
    ↓
Welcome Modal Middleware Check
    ↓
Is session 'welcome_modal_completed'?
    ├── NO → Redirect to Welcome Modal
    │         ↓
    │    Display Available Panels
    │         ↓
    │    User Selects Panel
    │         ↓
    │    Mark session as completed
    │         ↓
    │    Redirect to chosen panel
    │
    └── YES → Check Panel Authorization
              ↓
         Has Required Role?
              ├── YES → Allow Access
              └── NO → Logout & Redirect to Login
```

## Implementation Details

### 1. Middleware: `EnsureWelcomeModalCompleted`

**Location**: `app/Http/Middleware/EnsureWelcomeModalCompleted.php`

**Purpose**:
- Intercepts all panel requests
- Validates role-based access
- Enforces welcome modal completion

**Key Features**:
- Skips API routes, logout, and modal routes
- Validates user roles for each panel
- Stores intended URL for post-modal redirect
- Logs out unauthorized users

### 2. Controller: `WelcomeModalController`

**Location**: `app/Http/Controllers/WelcomeModalController.php`

**Methods**:

#### `show()`
- Displays the welcome modal
- Determines available panels based on user roles
- Returns view with user and panel data

#### `complete(Request $request)`
- Marks welcome modal as completed in session
- Redirects to intended URL or default panel
- Handles panel selection logic

#### `getUserAvailablePanels($user)`
- Returns array of panels user can access
- Checks roles: admin, tenant, cashier
- Validates tenant active status

#### `getDefaultPanelUrl($user)`
- Determines default redirect based on user's primary role
- Priority: Admin → Cashier → Tenant
- Logs out if no valid role found

### 3. View: `welcome-modal.blade.php`

**Location**: `resources/views/welcome-modal.blade.php`

**Features**:
- Beautiful, modern UI with Alpine.js animations
- Responsive design with Tailwind CSS
- Dark mode support
- Dynamic panel cards based on user permissions
- Gradient effects and hover animations
- No access message for users without permissions

**Panel Information Displayed**:
- Admin Panel: Full system administration
- Tenant Panel: Manage your stall
- Cashier Panel: Point of sale system

### 4. Routes

**Location**: `routes/web.php`

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/welcome-modal', [WelcomeModalController::class, 'show'])
        ->name('welcome.modal');
    Route::post('/welcome-modal/complete', [WelcomeModalController::class, 'complete'])
        ->name('welcome.complete');
});
```

### 5. Panel Providers

**Updated Files**:
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/TenantPanelProvider.php`
- `app/Providers/Filament/CashierPanelProvider.php`

**Change**: Added `EnsureWelcomeModalCompleted` to `authMiddleware` array

```php
->authMiddleware([
    Authenticate::class,
    // Other middleware...
    \App\Http\Middleware\EnsureWelcomeModalCompleted::class,
])
```

### 6. Middleware Registration

**Location**: `bootstrap/app.php`

```php
$middleware->alias([
    // Other middleware...
    'welcome.modal' => \App\Http\Middleware\EnsureWelcomeModalCompleted::class,
]);
```

## Role-Based Access Matrix

| Role    | Admin Panel | Tenant Panel | Cashier Panel |
|---------|-------------|--------------|---------------|
| Admin   | ✅          | ❌           | ✅            |
| Tenant  | ❌          | ✅*          | ❌            |
| Cashier | ✅          | ❌           | ✅            |

\* Tenant must also have `is_active = true`

## Security Features

### 1. **Dual Authorization**
- Middleware check before panel access
- User model `canAccessPanel()` method validation

### 2. **Role Validation**
- Admin panel: Requires `admin` or `cashier` role
- Tenant panel: Requires `tenant` role + active status
- Cashier panel: Requires `cashier` or `admin` role

### 3. **Automatic Logout**
- Users attempting unauthorized access are logged out
- Flash message explains the reason
- Prevents unauthorized exploration

### 4. **Session Management**
- Modal completion tracked per session
- Intended URL preserved during redirect
- Clean session invalidation on logout

## User Experience

### First-Time Panel Access
1. User logs in via Filament login page
2. Redirected to welcome modal automatically
3. Sees available panels based on their role
4. Selects desired panel
5. Redirected to chosen panel
6. Can switch panels by accessing different URLs

### Subsequent Access (Same Session)
1. User directly accesses panel URL
2. Middleware checks session flag
3. Access granted immediately (no modal)

### New Session
1. Welcome modal shown again
2. Process repeats from first-time access

### Unauthorized Access Attempt
1. User tries to access unauthorized panel
2. Middleware detects unauthorized access
3. User is logged out immediately
4. Flash message displayed
5. Redirected to login page

## Benefits

### For Users
- Clear panel selection interface
- No confusion about available options
- Better understanding of their access level
- Prevents accidental access errors

### For Administrators
- Enhanced security layer
- Better access control
- Audit trail of panel access
- Prevents URL guessing attacks

### For System
- Centralized authorization logic
- Consistent user experience
- Easy to extend with new panels
- Session-based efficiency

## Customization

### Adding a New Panel

1. **Update Welcome Modal Controller**:
   ```php
   if ($user->hasRole('new_role')) {
       $panels[] = [
           'name' => 'New Panel',
           'url' => '/new-panel',
           'description' => 'Description here',
           'icon' => 'heroicon-o-icon-name',
       ];
   }
   ```

2. **Update Middleware**:
   ```php
   if ($request->is('new-panel/*') || $request->is('new-panel')) {
       if (!$user->hasRole('new_role')) {
           Auth::logout();
           session()->flash('error', 'No permission for new panel.');
           return redirect()->route('login');
       }
   }
   ```

3. **Update Default URL Logic**:
   ```php
   if ($user->hasRole('new_role')) {
       return '/new-panel';
   }
   ```

### Disabling Welcome Modal

To disable for specific users or scenarios, update middleware:

```php
// Skip welcome modal for specific users
if ($user->hasPermission('skip_welcome_modal')) {
    return $next($request);
}
```

### Customizing Modal Appearance

Edit `resources/views/welcome-modal.blade.php`:
- Change colors in Tailwind classes
- Modify gradient effects
- Update icon SVGs
- Adjust animations

## Troubleshooting

### Modal Shows Every Request
- Check session configuration
- Ensure `session()->put('welcome_modal_completed', true)` is called
- Verify session middleware is loaded

### Unauthorized Access Errors
- Verify user has correct role in database
- Check `is_active` flag for tenants
- Review middleware logic in `EnsureWelcomeModalCompleted.php`

### Redirect Loop
- Ensure welcome modal routes are excluded from middleware
- Check for conflicting middleware in panel providers
- Verify route names match controller

### Panel Not Showing
- Confirm user has required role
- Check tenant active status
- Review `getUserAvailablePanels()` logic

## Testing

### Test Cases

1. **Admin User**
   - Should see Admin and Cashier panels
   - Should access both successfully

2. **Tenant User (Active)**
   - Should see Tenant panel only
   - Should access successfully

3. **Tenant User (Inactive)**
   - Should see no panels
   - Should see "No Access" message

4. **Cashier User**
   - Should see Admin and Cashier panels
   - Should access both successfully

5. **Direct URL Access**
   - Should redirect to welcome modal first
   - Should remember intended URL
   - Should redirect after completion

6. **Unauthorized Access**
   - Should logout user
   - Should show error message
   - Should redirect to login

## Future Enhancements

- [ ] Remember user's last panel preference
- [ ] Add panel usage analytics
- [ ] Implement custom welcome messages per role
- [ ] Add onboarding tour option
- [ ] Multi-step welcome process
- [ ] Panel search/filter for many panels
- [ ] Custom panel icons per tenant

## Support

For issues or questions, please check:
- Middleware logs in `storage/logs/laravel.log`
- User roles in database `model_has_roles` table
- Session data in configured session driver

## Version History

- **v1.0.0** (2025-11-15): Initial implementation
  - Welcome modal with role-based panel selection
  - Session-based tracking
  - Enhanced security with dual authorization
  - Beautiful UI with dark mode support
