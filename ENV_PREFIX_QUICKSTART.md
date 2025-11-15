# Environment Prefix - Quick Reference Card

## 🚀 Quick Setup (2 Commands)

```bash
# 1. Generate prefix
php artisan env:setup-prefix

# 2. Migrate variables
php artisan env:migrate-prefix
```

## 💻 Usage in Code

```php
use App\Helpers\EnvHelper;

// Instead of:
$password = env('DB_PASSWORD');

// Use:
$password = EnvHelper::get('DB_PASSWORD');
```

## 📝 Common Commands

```bash
# Setup
php artisan env:setup-prefix                    # Generate random prefix
php artisan env:setup-prefix --prefix=MYAPP     # Custom prefix
php artisan env:setup-prefix --length=8         # Longer prefix

# Migration
php artisan env:migrate-prefix                  # Migrate sensitive vars
php artisan env:migrate-prefix --all            # Migrate ALL vars
php artisan env:migrate-prefix --dry-run        # Preview changes
php artisan env:migrate-prefix --keys=DB_PASSWORD --keys=API_KEY  # Specific vars

# Maintenance
php artisan config:clear                        # Clear config cache
grep ENV_PREFIX .env                            # View current prefix
```

## 🔍 Helper Methods

```php
// Get variable
EnvHelper::get('DB_PASSWORD')
EnvHelper::get('API_KEY', 'default')

// Check exists
EnvHelper::has('STRIPE_SECRET')

// Get prefix
EnvHelper::getPrefix()  // Returns: XK92

// Get all prefixed
EnvHelper::getAllPrefixed()
```

## 📂 Example .env

```env
ENV_PREFIX=XK92

# Prefixed (secure)
XK92_DB_PASSWORD=secret123
XK92_API_KEY=key456

# Non-prefixed (safe)
APP_NAME=MyApp
APP_ENV=production
```

## ✅ Checklist

- [ ] Run `php artisan env:setup-prefix`
- [ ] Run `php artisan env:migrate-prefix`
- [ ] Update code to use `EnvHelper::get()`
- [ ] Test application
- [ ] Clear caches: `php artisan config:clear`
- [ ] Remove old commented variables from .env

## 🔒 Security Tips

1. Different prefix per environment (dev, staging, prod)
2. Never log the prefix
3. Set `.env` permissions to 600
4. Don't commit `.env` to git
5. Rotate prefix every 3-6 months

## 🆘 Troubleshooting

**Variables not loading?**
```bash
php artisan config:clear
php artisan cache:clear
```

**Forgot prefix?**
```bash
grep ENV_PREFIX .env
```

**Test helper:**
```bash
php artisan tinker
>>> EnvHelper::get('DB_PASSWORD')
```

## 📖 Full Documentation

See `ENV_PREFIX_GUIDE.md` for detailed guide.
