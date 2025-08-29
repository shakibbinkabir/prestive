<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        
        if (!isset($this->routes[$method])) {
            $this->notFound();
            return;
        }

        // Try exact match first
        if (isset($this->routes[$method][$path])) {
            $this->callHandler($this->routes[$method][$path]);
            return;
        }

        // Try pattern matching for parameters
        foreach ($this->routes[$method] as $routePath => $handler) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove full match
                $this->callHandler($handler, $matches);
                return;
            }
        }

        $this->notFound();
    }

    private function callHandler(array $handler, array $params = []): void
    {
        [$class, $method] = $handler;
        $controller = new $class();
        call_user_func_array([$controller, $method], $params);
    }

    private function notFound(): void
    {
        Response::setStatus(404);
        if (APP_DEBUG) {
            echo "<h1>404 Not Found</h1><p>Route not found</p>";
        } else {
            echo "<h1>Page Not Found</h1>";
        }
    }
}