<?php

$dirs = [
    __DIR__ . '/inc/helpers/',
    __DIR__ . '/inc/traits/',
    __DIR__ . '/inc/controllers/',
];

$files = [
    __DIR__ . '/inc/functions.php',
    __DIR__ . '/inc/error.php',
    __DIR__ . '/inc/view.php',
    __DIR__ . '/inc/debugPanel.php',
    __DIR__ . '/inc/controller.php',
    __DIR__ . '/inc/plugin.php',
    __DIR__ . '/inc/fire.php',
];

foreach ($dirs as $dir) {
    $rDir = new RecursiveDirectoryIterator($dir);
    $iDir = new RecursiveIteratorIterator($rDir);
    $iFiles = new RegexIterator($iDir, '/^.+\.php$/i', RegexIterator::GET_MATCH);
    foreach($iFiles as $file) {
        require_once $file[0];
    }
}

foreach ($files as $file) {
    require_once $file;
}
