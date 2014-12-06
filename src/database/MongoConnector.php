<?php
namespace water\database;

use water\config\Config;

/*
 * I understand that this is ugly, but it was fast and should work okay.
 */
class MongoConnector{
    /** @var  \MongoDB */
    private static $db;

    public static function connect(){
        if(getenv("MONGOLAB_URI") !== false){
            $m = new \MongoClient(getenv("MONGOLAB_URI"));
            $arr = explode("/", getenv("MONGOLAB_URI")); //For strict
            MongoConnector::$db = $m->selectDB(end($arr)); //TODO
        }
        else {
            $m = new \MongoClient(Config::getConfig()["connections"][Config::getConfig()["database"]]["uri"]);
            MongoConnector::$db = $m->selectDB(Config::getConfig()["connections"][Config::getConfig()["database"]]["database"]);
        }
    }
    public static function getUserCollection(){
        if(MongoConnector::$db == null){
            MongoConnector::connect();
        }
        return MongoConnector::$db->selectCollection("users");
    }
    public static function getDomainsCollection(){
        if(MongoConnector::$db == null){
            MongoConnector::connect();
        }
        return MongoConnector::$db->selectCollection("domains");
    }
}