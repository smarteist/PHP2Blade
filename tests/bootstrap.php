<?php


putenv('TESTS_PATH='.__DIR__);
putenv('LIBRARY_PATH='.dirname(__DIR__));

$vendor = dirname(__DIR__, 1).'/vendor/';
if (!is_dir($vendor)) {
    die('Please install via Composer before running tests.');
}

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', $vendor.'autoload.php');
}

require_once $vendor.'autoload.php';
unset($vendor);