<?php
namespace shogchat\socket;

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
}