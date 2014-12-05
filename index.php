<?php
use water\PageRouter;

require 'vendor/autoload.php';
define("MAIN_PATH", realpath(__DIR__));
PageRouter::route();
