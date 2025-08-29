<?php
declare(strict_types=1);

namespace App\Core;

class RateLimiter
{
    private string $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../storage/logs/ratelimit.json';
    }

    public function allow(string $key, int $limitPerMinute = 60): bool
    {
        $now = time();
        $data = $this->loadData();
        
        // Clean old entries (older than 1 minute)
        $data = array_filter($data, fn($entry) => ($now - $entry['time']) < 60);
        
        // Count current requests for this key
        $count = count(array_filter($data, fn($entry) => $entry['key'] === $key));
        
        if ($count >= $limitPerMinute) {
            return false;
        }
        
        // Add current request
        $data[] = ['key' => $key, 'time' => $now];
        
        $this->saveData($data);
        return true;
    }

    private function loadData(): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $content = file_get_contents($this->logFile);
        return json_decode($content, true) ?? [];
    }

    private function saveData(array $data): void
    {
        file_put_contents($this->logFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}