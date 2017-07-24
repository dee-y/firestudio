<?php

function debugger($message = '') {
    $injector = firestudio\helpers\injector::instance();
    $debugPanel = $injector->get(firestudio\fire::INJECTOR_DEBUG_PANEL);
    $trace = debug_backtrace();
    $debug = (object) [
        'trace' => $trace[0],
        'message' => $message
    ];
    $debugPanel->debuggers[] = $debug;
}
