<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    private static function resolveViewPath(string $view): string
    {
        // Normalize known top-level directories to correct casing
        $normalized = $view;
        if (str_starts_with(strtolower($normalized), 'admin/')) {
            $normalized = 'Admin/' . substr($normalized, strlen('admin/'));
        } elseif (str_starts_with(strtolower($normalized), 'components/')) {
            $normalized = 'Components/' . substr($normalized, strlen('components/'));
        }

        return __DIR__ . '/../Views/' . $normalized . '.php';
    }

    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = self::resolveViewPath($view);
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: $view");
        }
        
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
    // If not a partial/component, wrap in layout (handle case-insensitively for folder name)
    $isComponent = str_starts_with(strtolower($view), 'components/');
    if (!$isComponent) {
            include __DIR__ . '/../Views/layout.php';
        } else {
            echo $content;
        }
    }

    public static function partial(string $view, array $data = []): void
    {
        extract($data);
        include self::resolveViewPath($view);
    }
}