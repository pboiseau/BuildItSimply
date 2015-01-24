<?php

$f3 = require('lib/base.php');
require('vendor/twig/twig/lib/Twig/Autoloader.php');

Twig_Autoloader::register();

$twig = new Twig_Environment(new Twig_Loader_Filesystem('app/view/'), [
	'debug' => false,
	'cache' => 'assets/cache/',
	'auto_reload' => true
]);

// Load app configuration
$f3->config('app/config/config.ini');
$f3->config('app/config/routes.ini');
$f3->config('app/config/maps.ini');
$f3->config('app/config/redirects.ini');


$f3->run();
