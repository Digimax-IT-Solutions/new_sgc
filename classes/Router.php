<?

// Router.php

class Router {
    private $routes = [];

    public function addRoute($url, $handler) {
        $this->routes[$url] = $handler;
    }

    public function route($url) {
        if (array_key_exists($url, $this->routes)) {
            return $this->routes[$url];
        } else {
            return '404.php';
        }
    }
}