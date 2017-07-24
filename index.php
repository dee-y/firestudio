<?php
require_once __DIR__ . '/autoload.php';

$firestudio = new firestudio\fire();
$firestudio->run();

$debugger = $firestudio->injector->get(firestudio\fire::INJECTOR_DEBUG_PANEL);
$debugger->renderPanel();
