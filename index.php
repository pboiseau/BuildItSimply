<?php

// Kickstart the framework
$f3 = require('lib/base.php');

// Load configuration
$f3->config('app/config/config.ini');
$f3->config('app/config/routes.ini');

$f3->set('AUTOLOAD', 'app/; app/controller/; app/model/');

$f3->run();
