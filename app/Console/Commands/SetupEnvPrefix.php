<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Helpers\EnvHelper;

class SetupEnvPrefix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:setup-prefix
                            {--prefix= : Custom prefix (leave empty for random)}
                            {--length=6 : Length of random prefix}
                            {--force : Force overwrite existing prefix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up environment variable prefix for enhanced security';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        // Check if prefix already exists
        $currentPrefix = env('ENV_PREFIX');
        if ($currentPrefix && !$this->option('force')) {
            $this->warn("ENV_PREFIX already set to: {$currentPrefix}");

            if (!$this->confirm('Do you want to change it?', false)) {
                $this->info('Cancelled.');
                return 0;
            }
        }

        // Get or generate prefix
        $prefix = $this->option('prefix');

        if (!$prefix) {
            $length = $this->option('length') ?? 6;
            $prefix = EnvHelper::generatePrefix($length);
            $this->info("Generated random prefix: {$prefix}");
        } else {
            $this->info("Using custom prefix: {$prefix}");
        }

        // Validate prefix (alphanumeric only)
        if (!preg_match('/^[A-Z0-9]+$/', $prefix)) {
            $this->error('Prefix must contain only uppercase letters and numbers!');
            return 1;
        }

        // Update .env file
        $envContent = File::get($envPath);

        // Check if ENV_PREFIX exists
        if (preg_match('/^ENV_PREFIX=.*/m', $envContent)) {
            // Replace existing
            $envContent = preg_replace(
                '/^ENV_PREFIX=.*/m',
                "ENV_PREFIX={$prefix}",
                $envContent
            );
            $this->info('Updated ENV_PREFIX in .env');
        } else {
            // Add at the top
            $envContent = "ENV_PREFIX={$prefix}\n\n" . $envContent;
            $this->info('Added ENV_PREFIX to .env');
        }

        // Save .env file
        File::put($envPath, $envContent);

        $this->newLine();
        $this->info('✅ Environment prefix set up successfully!');
        $this->newLine();

        // Show next steps
        $this->comment('Next steps:');
        $this->line('1. Run: php artisan env:migrate-prefix to migrate existing variables');
        $this->line('2. Clear config cache: php artisan config:clear');
        $this->line('3. Restart your application');

        $this->newLine();
        $this->comment('Usage in code:');
        $this->line("  EnvHelper::get('DB_PASSWORD')  // Instead of env('DB_PASSWORD')");

        return 0;
    }
}
