<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Helpers\EnvHelper;

class MigrateEnvPrefix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:migrate-prefix
                            {--keys=* : Specific keys to migrate (leave empty for all sensitive keys)}
                            {--all : Migrate ALL environment variables}
                            {--dry-run : Show what would be changed without actually changing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing environment variables to use prefix';

    /**
     * Default sensitive keys to migrate
     *
     * @var array
     */
    protected $sensitiveKeys = [
        'APP_KEY',
        'DB_PASSWORD',
        'DB_USERNAME',
        'MAIL_PASSWORD',
        'MAIL_USERNAME',
        'AWS_SECRET_ACCESS_KEY',
        'AWS_ACCESS_KEY_ID',
        'PUSHER_APP_SECRET',
        'PUSHER_APP_KEY',
        'STRIPE_SECRET',
        'STRIPE_KEY',
        'PAYMONGO_SECRET_KEY',
        'PAYMONGO_PUBLIC_KEY',
        'RECAPTCHA_SECRET_KEY',
        'RECAPTCHA_SITE_KEY',
        'SESSION_DRIVER',
        'CACHE_DRIVER',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $prefix = EnvHelper::getPrefix();

        if (empty($prefix)) {
            $this->error('ENV_PREFIX not set!');
            $this->comment('Run: php artisan env:setup-prefix first');
            return 1;
        }

        $this->info("Using prefix: {$prefix}");

        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        // Determine which keys to migrate
        $keysToMigrate = $this->option('keys');

        if (empty($keysToMigrate)) {
            if ($this->option('all')) {
                $this->warn('Migrating ALL environment variables!');
                if (!$this->confirm('Are you sure?', false)) {
                    return 0;
                }
                $keysToMigrate = $this->getAllEnvKeys($envPath);
            } else {
                $keysToMigrate = $this->sensitiveKeys;
                $this->info('Migrating default sensitive keys...');
            }
        }

        $envContent = File::get($envPath);
        $migratedCount = 0;
        $skippedCount = 0;

        $this->newLine();

        foreach ($keysToMigrate as $key) {
            // Skip ENV_PREFIX itself
            if ($key === 'ENV_PREFIX') {
                continue;
            }

            $prefixedKey = $prefix . '_' . $key;

            // Check if variable exists
            if (!preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
                $this->line("  ⊘ {$key} - not found, skipping");
                $skippedCount++;
                continue;
            }

            $value = $matches[1];

            // Check if already prefixed
            if (preg_match("/^{$prefixedKey}=/m", $envContent)) {
                $this->line("  ⊘ {$key} - already migrated");
                $skippedCount++;
                continue;
            }

            // Migrate
            if ($this->option('dry-run')) {
                $this->line("  ✓ {$key} → {$prefixedKey} (dry-run)");
            } else {
                // Add prefixed version before the original
                $envContent = preg_replace(
                    "/^({$key}=.*)$/m",
                    "{$prefixedKey}={$value}\n# $1  # ← Old version (can be removed after testing)",
                    $envContent
                );
                $this->line("  ✓ {$key} → {$prefixedKey}");
            }

            $migratedCount++;
        }

        // Save changes
        if (!$this->option('dry-run')) {
            File::put($envPath, $envContent);
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info("✅ Dry-run completed! {$migratedCount} would be migrated, {$skippedCount} skipped.");
            $this->comment('Run without --dry-run to apply changes');
        } else {
            $this->info("✅ Migration completed! {$migratedCount} migrated, {$skippedCount} skipped.");
            $this->newLine();
            $this->comment('Next steps:');
            $this->line('1. Test your application thoroughly');
            $this->line('2. If everything works, remove the old (commented) versions');
            $this->line('3. Clear config cache: php artisan config:clear');
        }

        return 0;
    }

    /**
     * Get all environment variable keys from .env file
     *
     * @param  string  $envPath
     * @return array
     */
    private function getAllEnvKeys(string $envPath): array
    {
        $content = File::get($envPath);
        preg_match_all('/^([A-Z_][A-Z0-9_]*)=/m', $content, $matches);

        return $matches[1] ?? [];
    }
}
