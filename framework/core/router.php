<?
    class router {
        private $routes = [];

        public function process() {
            $_request = htmlspecialchars(filter_var(urldecode($_SERVER['REQUEST_URI']), FILTER_SANITIZE_URL), ENT_QUOTES, 'UTF-8');
            $_method = $_SERVER['REQUEST_METHOD'];

            foreach ($this->routes as $route) {
                $pattern = str_replace('/', '\/', $route['path']);
                $pattern = preg_replace('/:(\w+)/', '(?<$1>[^\/]+)', $pattern);
                $pattern = '/^' . $pattern . '\/?$/';
                $parameters = [];

                if (preg_match($pattern, $_request, $matches)) {
                    if (!in_array(strtolower($_method), array_map('strtolower', $route['methods'])))
                        return header('Location: /404');
                    
                    $handler = explode('@', $route['handler']);
                    $class = !empty($handler[0]) && class_exists($handler[0]) ? $handler[0] : '_404_controller';
                    $instance = new $class();
                    $method = !empty($handler[1]) && method_exists($instance, $handler[1]) ? $handler[1] : 'index';

                    if ($class !== '_404_controller')
                        foreach ($matches as $key => $value)
                            if (!is_numeric($key) && !empty($key) && !empty($value))
                                $parameters[$key] = $value;

                    return call_user_func_array([$instance, $method], [$parameters]);
                }
            }

            return header('Location: /404');
        }

        public function register(array $methods, string $path, string $handler) {
            $this->routes[] = [
                'path' => $path,
                'methods' => $methods,
                'handler' => $handler
            ];

        }

        public function routes() {
            return $this->routes;
        }
    }
?>