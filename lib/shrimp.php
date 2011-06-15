<?php

// Configuration Variables
define('ROOT', dirname(dirname(__FILE__)));
ini_set('display_errors','On');

function get($route, $callback) { Shrimp::register($route, $callback, 'GET'); }
function post($route, $callback) { Shrimp::register($route, $callback, 'POST'); }
function put($route, $callback) { Shrimp::register($route, $callback, 'PUT'); }
function delete($route, $callback) { Shrimp::register($route, $callback, 'DELETE'); }

class Shrimp {
    public static $route_found = false;
    public $route = '';
    public $segments = '';
    public $method = '';
    public $view = '';
    public $content = '';
    public $vars = array();

    public function __construct() {
        $this->route = $this->get_route();
        $this->segments = explode('/', trim($this->route, '/'));
        $this->method = $this->get_method();
    }

    // Sets variables to be used in this request
    public function set($index, $value) {
        $this->vars[$index] = $value;
    }

    // Renders the view and takes all variables set in
    // $vars and renders them out
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

    public static function run() {
        if (!static::$route_found) {
            echo 'Route not defined!';
        }
    }

    public static function register($route, $callback, $type) { 
        $shrimp = static::instance();

        if (static::$route_found || ( ! preg_match('@^'.$route.'$@uD', $shrimp->route) || $shrimp->method != $type)) {
            return false;
        }
        static::$route_found = true;
        echo $callback($shrimp);
    }

    public function segment($num) {
        return isset($this->segments[$num - 1]) ? $this->segments[$num - 1] : null;
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