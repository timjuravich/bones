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
    public $segments = '';
    public $method = '';
    public $view = '';
    public $content = '';
    public $vars = array();
	public $route_variables = array();

    public function __construct() {
        $this->route = $this->get_route();
        $this->route_segments = explode('/', trim($this->route, '/'));
        $this->method = $this->get_method();

		if (DEBUG) {
			echo "<h2>Compare Against</h2>";
			var_dump($this->route_segments);
			echo "<br /><br />--------------------<br /><br />";
		}
    }

    // Sets variables to be used in this request
    public function set($index, $value) {
        $this->vars[$index] = $value;
    }

    // Sets variables to be used in this request
    public function request($key) {
        return $this->route_variables[$key];
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

		if (!static::$route_found) {
			
			$url_parts = explode('/', trim($route, '/'));
			
			if (DEBUG) {
				echo "<br />";
				self::log("Routing","Routing Start");
				var_dump($url_parts);
				echo "<br />";
			}
			
			$matched = null;

			foreach ($url_parts as $key=>$part) {
				// Find Variables
				if (strpos($part, ":") !== false) {
					$shrimp->route_variables[substr($part, 1)] = $shrimp->route_segments[$key];
					self::log("Routing","Variable found at", $key);
					self::log("Routing","Variable " . substr($part, 1) . " set as " . $shrimp->route_segments[$key]);
				} else {
					if (count($shrimp->route_segments) == count($url_parts)) {
						if ($part == $shrimp->route_segments[$key]) {
							if (!$matched) {
								self::log("Routing","Routes match");
								$matched = true;
							}
						} else {
							echo "<i>no match</i> <br />";
							self::log("Routing","Routes don't match");
							$matched = false;
						}
					} else {
						//String doesn't have the same length
						self::log("Routing","Routes are different lengths");
						$matched = false;
					}
				}
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