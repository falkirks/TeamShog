<?php
namespace shogchat\config;

class Config{
    static private $config;
    static public function getConfig(){
        if(Config::$config == null){
            Config::$config = json_decode(file_get_contents(MAIN_PATH . "/private/config.json"), true);
        }
        return Config::$config;
    }
}