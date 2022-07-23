#!/usr/bin/env php
<?php

use App\CLI;

require_once './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', true);
define('ABSPATH', __DIR__);


// matches args in form of => command
$commands = preg_grep('/(^[^-].*)/', $argv);
// matches args in form of => -option:value or -option
$options = preg_grep('/^-(\w+)(:(\w+))?/', $argv);
// matches args in form of => --option
$switches = preg_grep('/^--(\w+)/', $argv);

(new CLI())->boot($commands, $options, $switches);