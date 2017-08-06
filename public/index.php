<?php
@date_default_timezone_set('Africa/Monrovia');
//echo date('Y-m-d H:i:s');
//echo date_default_timezone_get();die;
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
