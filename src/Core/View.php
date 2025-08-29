<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: $view");
        }
        
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
        // If not a partial/component, wrap in layout
        if (!str_starts_with($view, 'components/')) {
            include __DIR__ . '/../Views/layout.php';
        } else {
            echo $content;
        }
    }

    public static function partial(string $view, array $data = []): void
    {
        extract($data);
        include __DIR__ . '/../Views/' . $view . '.php';
    }
}