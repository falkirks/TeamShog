<?php
namespace shogchat\database;

use shogchat\config\Config;
/*
 * I understand that this is ugly, but it was fast and should work okay.
 */
class MongoConnector{
    /** @var  \MongoDB */
    private static $db;

    public static function connect(){
        $m = new \MongoClient(Config::getConfig()["connections"][Config::getConfig()["database"]]["host"]);
        MongoConnector::$db = $m->selectDB(Config::getConfig()["connections"][Config::getConfig()["database"]]["database"]);
    }
    public static function __callStatic($name, $args){
        if(MongoConnector::$db == null){
            MongoConnector::connect();
        }
        return MongoConnector::$db->$name(...$args);
    }
    public static function getUserCollection(){
        if(MongoConnector::$db == null){
            MongoConnector::connect();
        }
        return MongoConnector::$db->selectCollection("users");
    }
    public static function getChannelCollection(){
        if(MongoConnector::$db == null){
            MongoConnector::connect();
        }
        return MongoConnector::$db->selectCollection("channels");
    }
}