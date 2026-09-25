<?php

namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $scriptName = $_SERVER['SCRIPT_NAME']; 
        $basePath = str_replace('\\', '/', dirname($scriptName)); 
        $projectRoot = str_replace('\\', '/', dirname($basePath));
        
        $path = $uri;
        if (stripos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        } elseif (stripos($path, $basePath) === 0 && $basePath !== '/') {
            $path = substr($path, strlen($basePath));
        } elseif (stripos($path, $projectRoot) === 0 && $projectRoot !== '/') {
            $path = substr($path, strlen($projectRoot));
        }

        if (empty($path) || $path === '') {
            $path = '/';
        }
        
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {
            $pattern = "#^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']) . "$#";
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    return call_user_func_array($handler, array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                }
                if (is_array($handler)) {
                    $controllerName = $handler[0];
                    $methodName = $handler[1];
                    $controller = new $controllerName();
                    return call_user_func_array([$controller, $methodName], array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                }
            }
        }

        http_response_code(404);
        echo "<div style='text-align:center; padding: 4rem; font-family: sans-serif; color: #1e293b;'>
            <h1 style='font-size: 3rem; margin-bottom: 0.5rem; color: #2563eb;'>404</h1>
            <p style='font-size: 1.2rem; color: #64748b;'>Halaman tidak ditemukan di MTs RS System.</p>
            <a href='/login' style='display: inline-block; margin-top: 1rem; padding: 0.8rem 1.5rem; background: #2563eb; color: white; text-decoration: none; border-radius: 12px; font-weight: bold;'>Kembali ke Login</a>
        </div>";
    }
}
