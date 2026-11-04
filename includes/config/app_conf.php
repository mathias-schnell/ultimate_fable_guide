<?php
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    define('APP_ROOT',      dirname(dirname(__DIR__)));
    define('INCLUDES_PATH', APP_ROOT . '/includes');
    define('DATA_PATH',     APP_ROOT . '/data');
    define('CSS_PATH',      APP_ROOT . '/assets/css');
    define('FONTS_PATH',    APP_ROOT . '/assets/fonts');
    define('ICONS_PATH',    APP_ROOT . '/assets/icons');
    define('JS_PATH',       APP_ROOT . '/assets/js');

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $relative_path = substr(realpath(APP_ROOT), strlen(realpath($_SERVER['DOCUMENT_ROOT'])));
    $base_path = trim(str_replace(DIRECTORY_SEPARATOR, '/', $relative_path), '/');
    
    define('BASE_URL',      $protocol . $host . '/' . ($base_path ? $base_path . '/' : ''));
    define('CSS_URL',       BASE_URL . 'assets/css');
    define('FONTS_URL',     BASE_URL . 'assets/fonts');
    define('ICONS_URL',     BASE_URL . 'assets/icons');
    define('JS_URL',        BASE_URL . 'assets/js');

    $css_files = [
        "reset",
        "style",
        "icons",
        "content"
    ];
    
    $order = [
        "classes", 
        "class-features",
        "heroic-skills", 
        "equipment",
        "optional-rules"
    ];

    $title_icons = [
        'svg-emerald'
    ];