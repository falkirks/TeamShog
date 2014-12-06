<?php
use water\PageRouter;
error_reporting(-1);
ini_set('display_errors', 'On');
require 'vendor/autoload.php';
define("MAIN_PATH", realpath(__DIR__));
PageRouter::route();