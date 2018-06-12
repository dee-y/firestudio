<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

use Fire\Studio;

$studio = new Studio(__DIR__ . '/application.json');
$studio->run();

// $studio->injector->get('fire.studio.debug')->render();
