<?php
namespace water\domains;
use water\database\Domains;

class DomainCache{
    public static function getDomain($name){
        $domain = Domains::getDomain($name);
        if($domain !== false){
            //TODO check "added"
        }
        else{

        }
    }
    public static function getDocument($name){
        //TODO
    }
}