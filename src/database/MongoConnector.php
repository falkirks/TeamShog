<?php
namespace water\database;

use water\config\Config;
use water\socket\Logger;

/*
 * I understand that this is ugly, but it was fast and should work okay.
 */
class MongoConnector{
    /** @var  \MongoDB */
    private static $db;

    public static function connect(){
        if(getenv("MONGOLAB_URI") !== false){
            $m = new \MongoClient(getenv("MONGOLAB_URI"));
            MongoConnector::$db = $m->selectDB($m->listDBs()[0]); //TODO
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
    public static function getDataCollection(){
        if(MongoConnector::$db == null){
            MongoConnector::connect();
        }
        return MongoConnector::$db->selectCollection("data");
    }
}