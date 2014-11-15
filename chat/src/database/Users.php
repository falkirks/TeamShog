<?php
namespace shogchat\database;

class Users{
    public static function createUser($name, $email, $password, $token){
        MongoConnector::getUserCollection()->insert([
            "_id" => $name,
            "email" => $email,
            "password" => md5($password), //TODO stronger hash
            "token" => $token,
            "registration" => time(),
            "lastactive" => time(),
            "channels" => [],
            "sessions" => []
        ]);
    }
    public static function checkLogin($name, $password){
        $user = MongoConnector::getUserCollection()->findOne(["_id" => $name]);
        if($user != null){
            if($user["password"] === md5($password)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
}