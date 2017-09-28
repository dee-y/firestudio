<?php

function debugger($message = '')
{
    $injector = Fire\Studio\Helper\Injector::instance();
    $debugPanel = $injector->get(Fire\Studio::INJECTOR_DEBUG_PANEL);
    $trace = debug_backtrace();
    $debug = (object) [
        'trace' => $trace[0],
        'message' => $message
    ];
    $debugPanel->debuggers[] = $debug;
}
