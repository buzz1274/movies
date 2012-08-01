<?php

	define('DS', DIRECTORY_SEPARATOR);

	if (!defined('ROOT')) {
	    define('ROOT', DS.'var'.DS.'www'.DS.'movies'.DS.'html');
	}
	
	if (!defined('APP_DIR')) {
	    define ('APP_DIR', DS.'var'.DS.'www'.DS.'movies'.DS);
	}
	
	if (!defined('CAKE_CORE_INCLUDE_PATH')) {
	    define('CAKE_CORE_INCLUDE_PATH', DS.'usr'.DS.'local'.DS.'cakephp'.DS.'lib');
	}
	
	if (!defined('WWW_ROOT')) {
	        define('WWW_ROOT', dirname(__FILE__) . DS);
	}	
	
if (!defined('CAKE_CORE_INCLUDE_PATH')) {
        if (function_exists('ini_set')) {
                ini_set('include_path', ROOT . DS . 'lib' . PATH_SEPARATOR . ini_get('include_path'));
        }
        if (!include ('Cake' . DS . 'bootstrap.php')) {
                $failed = true;
        }
} else {
        if (!include (CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'bootstrap.php')) {
                $failed = true;
        }
}

if (!defined('WWW_ROOT')) {
        define('WWW_ROOT', dirname(__FILE__) . DS);
}

	
	
if (!empty($failed)) {
        trigger_error("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
}

App::uses('Dispatcher', 'Routing');

$Dispatcher = new Dispatcher();
$Dispatcher->dispatch(new CakeRequest(), new CakeResponse(array('charset' => Configure::read('App.encoding'))));
	
	
?>	