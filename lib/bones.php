<?php

define('ROOT', dirname(dirname(__FILE__)));

function __autoload($classname) { 
   include_once(ROOT . "/classes/" . strtolower($classname) . ".php"); 
}

function get($route, $callback) { 
	Bones::register($route, $callback, 'GET');
}

function post($route, $callback) {
	Bones::register($route, $callback, 'POST');
}

function put($route, $callback) {
	Bones::register($route, $callback, 'PUT');
}

function delete($route, $callback) {
	Bones::register($route, $callback, 'DELETE');
}

class Bones {
	private static $instance;
    public static $route_found = false;
	public static $rendered = false;
    public $route = '';
    public $method = '';
	public $content = '';
    public $vars = array();
	public $route_segments = array();
    public $route_variables = array();

    public function __construct() {
        $this->route = $this->get_route();
        $this->route_segments = explode('/', trim($this->route, '/'));
        $this->method = $this->get_method();
    }

    public static function get_instance() {
        if (!isset(self::$instance)) {
			self::$instance = new Bones();
        }
        
        return self::$instance;
    }

    public static function register($route, $callback, $method) { 
		if (!static::$route_found) {
			$bones = static::get_instance();
            $url_parts = explode('/', trim($route, '/'));
            $matched = null;

            if (count($bones->route_segments) == count($url_parts)) {
                foreach ($url_parts as $key=>$part) {
                    if (strpos($part, ":") !== false) {
						// Contains a route variable
                        $bones->route_variables[substr($part, 1)] = $bones->route_segments[$key];
                    } else {
						// Does not contain a route variable
                        if ($part == $bones->route_segments[$key]) {
                            if (!$matched) {
								// Routes match
                                $matched = true;
                            }
                        } else {
							// Routes don't match
                            $matched = false;
                        }
                    }
                }
            } else {
				// Routes are different lengths
                $matched = false;
            }


            if (!$matched || $bones->method != $method) {
                return false;
            } else {
                static::$route_found = true;
                echo $callback($bones);
            }
		}
    }

    protected function get_route() {
		parse_str($_SERVER['QUERY_STRING'], $route);
		if ($route) {
        	return '/' . $route['request'];
		} else {
			return '/';
		}
    }

    public function make_route($path = '') {
        $url = explode("/", $_SERVER['PHP_SELF']);
        return '/' . $url[1] . '/' . $path;
    }

    protected function get_method() {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }

    public function set($index, $value) {
        $this->vars[$index] = $value;
    }

	public function form($key) {
        return $_POST[$key];
	}
	
    public function request($key) {
        return $this->route_variables[$key];
    }

	public function render($view, $layout = "layout") {
		if (!static::$rendered) {
			static::$rendered = true;
			$this->content = ROOT. '/views/' . $view . '.php';
	        foreach ($this->vars as $key => $value) {
	            $$key = $value;
	        }
	        include(ROOT. '/views/' . $layout . '.php');
		}
    }

	public function display_flash($variable = 'error') {
		if (isset($this->vars[$variable])) {
			return "<div class='alert-holder'><div class='alert-message " . $variable . "'><p>" . $this->vars[$variable] . "</p></div></div>";
		}
	}
	
	public function redirect($path = '') {
		header('Location: ' . $this->make_route($path));
	}

}