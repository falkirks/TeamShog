<?php
namespace water;

use water\page\index;
use water\page\Page;

class PageRouter{
    public static function route(){
        session_start();
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
                if(class_exists("water\\page\\$main") && is_subclass_of("water\\page\\$main", "water\\page\\Page")){
                    $page = "water\\page\\$main";
                    /** @var Page $page */
                    $page = new $page();
                    if($page->hasPermission()){
                        $page->showPage();
                    }
                    else{
                        //TODO make prettier
                        (new index())->showPage("You don't have permission to access this page.");
                    }
                }
                else{
                    (new index())->showPage();
                }
                break;
        }
    }
}