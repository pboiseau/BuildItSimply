<?php

// Kickstart the framework
$f3 = require('lib/base.php');

// Load app configuration
$f3->config('app/config/config.ini');
$f3->config('app/config/routes.ini');
$f3->config('app/config/maps.ini');
$f3->config('app/config/redirects.ini');

$f3->set('AUTOLOAD', 'app/; app/controller/; app/model/');
$f3->set('UI', 'view/layout/; view/;');

$f3->run();
