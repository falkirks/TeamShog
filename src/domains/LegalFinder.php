<?php
namespace water\domains;

class LegalFinder{
    public static function getLegalURL($url){
        $file = file_get_contents($url);
        return ($file !== false ? LegalFinder::getLegal($file) : false);
    }
    public static function getLegal($text){
        //TODO
    }
}