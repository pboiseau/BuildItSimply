<?php

/**
*	Autoload by composer
**/
require('vendor/autoload.php');

// $f3 = require('lib/base.php');
// require('vendor/twig/twig/lib/Twig/Autoloader.php');

$f3 = Base::instance();

Twig_Autoloader::register();

$f3->set('TWIG', new Twig_Environment(
		new Twig_Loader_Filesystem('app/views'), [
			'debug' => false,
			'cache' => 'assets/cache/',
			'auto_reload' => true
		]
));

/**
*	Load framework configuration
**/
$f3->config('app/config/config.ini');
$f3->config('app/config/routes.ini');
$f3->config('app/config/maps.ini');
$f3->config('app/config/redirects.ini');

/**
*	Load Eloquent ORM
**/
$capsule = new Database();

$f3->run();
