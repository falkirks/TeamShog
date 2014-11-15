<?php
namespace shogchat;

use shogchat\page\index;

class PageRouter{
    public static function route(){
        $path = strtok(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PATH_INFO'], '?');
        $path = explode("/", ltrim($path, "/"));
        $main = array_shift($path);
        /*
         * NOTE
         *  Only add pages to the switch if you need to. In most cases they can just be autoloaded.
         */
        switch($main){
            case 'example':

                break;
            default:
                if(class_exists("shogchat\\page\\$main") && is_subclass_of("shogchat\\page\\$main", "shogchat\\page\\Page")){
                    $page = "shogchat\\page\\$main";
                    (new $page())->showPage();
                }
                else{
                    (new index())->showPage();
                }
                break;
        }
    }
}