<?php

// Configuration Variables
define('ROOT', dirname(dirname(__FILE__)));
define('DEBUG', false);
ini_set('display_errors','On');

function get($route, $callback) { Shrimp::register($route, $callback, 'GET'); }
function post($route, $callback) { Shrimp::register($route, $callback, 'POST'); }
function put($route, $callback) { Shrimp::register($route, $callback, 'PUT'); }
function delete($route, $callback) { Shrimp::register($route, $callback, 'DELETE'); }

class Shrimp {
    public static $route_found = false;
    public $route = '';
    public $method = '';
    public $view = '';
    public $content = '';
    public $vars = array();
    public $route_variables = array();

    public function __construct() {
        $this->route = $this->get_route();
        $this->route_segments = explode('/', trim($this->route, '/'));
        $this->method = $this->get_method();
    }

    public function set($index, $value) {
        $this->vars[$index] = $value;
    }

    public function request($key) {
        return $this->route_variables[$key];
    }

    public function render($view, $layout = "layout") {
        $this->view_content = ROOT. '/views/' . $view . '.php';
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }
        include(ROOT. '/views/' . $layout . '.php');
    }

    public static function instance() {
        static $instance = null;
        
        if ($instance === null) {
            $instance = new Shrimp();
        }
        
        return $instance;
    }

    public static function register($route, $callback, $type) { 
        if (!static::$route_found) {
            $shrimp = static::instance();    
            $url_parts = explode('/', trim($route, '/'));
            $matched = null;

            if (count($shrimp->route_segments) == count($url_parts)) {
                foreach ($url_parts as $key=>$part) {
                    if (strpos($part, ":") !== false) {
                        $shrimp->route_variables[substr($part, 1)] = $shrimp->route_segments[$key];
                        self::log("Routing","Variable found at", $key);
                        self::log("Routing","Variable " . substr($part, 1) . " set as " . $shrimp->route_segments[$key]);
                    } else {
                        if ($part == $shrimp->route_segments[$key]) {
                            if (!$matched) {
                                self::log("Routing","Routes match");
                                $matched = true;
                            }
                        } else {
                            self::log("Routing","Routes don't match");
                            $matched = false;
                        }
                    }
                }
            } else {
                self::log("Routing","Routes are different lengths");
                $matched = false;
            }
        
            self::log("Routing","Did Routes Match?", $matched);
            self::log("Routing","Routing End");

            if (!$matched || $shrimp->method != $type) {
                return false;
            } else {
                static::$route_found = true;
                echo $callback($shrimp);
            }
        } else {
            self::log("Routing","Ignoring Route", $route);
        }
    }

    public function log($domain, $string, $secondary = "") {
        if (DEBUG) {
            echo "<span class='log'><b>$domain:</b> $string $secondary<br /></span>";
        }
    }

    protected function get_method() {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }
    
    protected function get_route() {
        return '/' . substr($_SERVER['QUERY_STRING'], 8);
    }

    public function make_route($path = '') {
        $url = explode("/", $_SERVER['PHP_SELF']);
        return '/' . $url[1] . '/' . $path;
    }
    
    public function redirect($path = '') {
        header('Location: ' . $this->make_route($path));
    }
    
}

$shrimp = Shrimp::instance();