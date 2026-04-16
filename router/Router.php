<?php

namespace App\Router;

class Router
{
    private $routes = array();

    public function get($path, $callback)
    {
        if (!isset($this->routes['GET'])) {
            $this->routes['GET'] = array();
        }
        $this->routes['GET'][$path] = $callback;
        return $this;
    }

    public function post($path, $callback)
    {
        if (!isset($this->routes['POST'])) {
            $this->routes['POST'] = array();
        }
        $this->routes['POST'][$path] = $callback;
        return $this;
    }

    public function run()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $path = $url === '' ? '/' : '/' . $url;

        // Chercher la route exacte
        if (isset($this->routes[$method][$path])) {
            $this->executeCallback($this->routes[$method][$path]);
            return;
        }

        // Chercher une route paramétrée
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                $params = $this->matchRoute($route, $path);
                if ($params !== false) {
                    $this->executeCallback($callback, $params);
                    return;
                }
            }
        }

        http_response_code(404);
        echo 'Page not found';
        exit;
    }

    private function matchRoute($pattern, $path)
    {
        if (strpos($pattern, '{') === false) {
            return $pattern === $path ? array() : false;
        }

        $regex = preg_replace('/\{(\w+)\}/', '([^\/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return false;
        }

        preg_match_all('/\{(\w+)\}/', $pattern, $paramNames);
        $params = array();

        for ($i = 0; $i < count($paramNames[1]); $i++) {
            $params[$paramNames[1][$i]] = $matches[$i + 1];
        }

        return $params;
    }

    private function executeCallback($callback, $params = array())
    {
        if (is_callable($callback)) {
            call_user_func_array($callback, array_values($params));
            return;
        }

        if (is_string($callback) && strpos($callback, '@') !== false) {
            $parts = explode('@', $callback, 2);
            $controller = $parts[0];
            $method = $parts[1];

            $class = null;
            foreach (array('App\\Controller\\', 'App\\Controllers\\') as $ns) {
                $fullClass = $ns . $controller;
                if (class_exists($fullClass)) {
                    $class = $fullClass;
                    break;
                }
            }

            if ($class === null) {
                http_response_code(500);
                echo "Controleur introuvable: $controller";
                exit;
            }

            $instance = new $class();
            if (!method_exists($instance, $method)) {
                http_response_code(500);
                echo "Methode introuvable: $method dans $class";
                exit;
            }

            call_user_func_array(array($instance, $method), array_values($params));
            return;
        }

        if (is_array($callback) && count($callback) === 2) {
            list($obj, $method) = $callback;
            if (is_object($obj) && method_exists($obj, $method)) {
                call_user_func_array($callback, array_values($params));
                return;
            }
        }

        http_response_code(500);
        echo 'Callback invalide';
        exit;
    }
}
