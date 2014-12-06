<?php
use water\PageRouter;
error_reporting(E_ALL); //For debugging
require 'vendor/autoload.php';
define("MAIN_PATH", realpath(__DIR__));
PageRouter::route();