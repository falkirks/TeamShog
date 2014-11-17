<?php
namespace shogchat\socket;

use shogchat\config\Config;

class Logger{
    public static function info($msg){
        print "[INFO] $msg\n";
    }
    public static function error($msg){
        print "[ERROR] $msg\n";
    }
    public static function warning($msg){
        print "[WARNING] $msg\n";
    }
    public static function debug($msg){
        if(Config::getConfig()["debug"]){
            print "[DEBUG] $msg\n";
        }
    }
}