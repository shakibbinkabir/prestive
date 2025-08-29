<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Core\DB;

function runMigrations(): void
{
    $migrationsDir = __DIR__ . '/../database/migrations';
    $logFile = __DIR__ . '/../storage/logs/migrations.log';
    
    // Ensure log directory exists
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Get list of migration files
    $migrationFiles = glob($migrationsDir . '/*.sql');
    sort($migrationFiles);
    
    // Get already run migrations
    $ranMigrations = [];
    if (file_exists($logFile)) {
        $ranMigrations = array_filter(explode("\n", file_get_contents($logFile)));
    }
    
    $db = DB::getInstance();
    
    foreach ($migrationFiles as $migrationFile) {
        $fileName = basename($migrationFile);
        
        if (in_array($fileName, $ranMigrations)) {
            echo "Skipping already run migration: $fileName\n";
            continue;
        }
        
        echo "Running migration: $fileName\n";
        
        try {
            $sql = file_get_contents($migrationFile);
            
            // Split by semicolons and execute each statement
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $db->exec($statement);
                }
            }
            
            // Log successful migration
            file_put_contents($logFile, $fileName . "\n", FILE_APPEND | LOCK_EX);
            echo "✓ Migration completed: $fileName\n";
            
        } catch (Exception $e) {
            echo "✗ Migration failed: $fileName\n";
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    echo "\nAll migrations completed successfully!\n";
}

// Run migrations
try {
    runMigrations();
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}