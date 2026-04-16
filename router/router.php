<?php

namespace Router;

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch($url, $method) {
        $url = trim($url, '/');
        $method = strtoupper($method);

        // Exact match first
        if (isset($this->routes[$method][$url])) {
            return call_user_func($this->routes[$method][$url]);
        }

        // Pattern match (e.g., games/{id})
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches);
                return call_user_func_array($callback, $matches);
            }
        }

        // 404
        http_response_code(404);
        require_once __DIR__ . '/../app/views/errors/404.php';
    }
}
