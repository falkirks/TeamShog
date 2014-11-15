<?php
namespace shogchat\session;
/*
 * This class will manage session cookied and allow the opening a session via
 * a user object and getting a user by there session ID.
 */
use shogchat\database\Users;

class SessionStore{
    public static function hasSession(){
        if(isset($_SESSION['login-data'])){
            $data = explode("\\", $_SESSION['login-data']);
            $user = Users::getUser($data[0]);
            if($user !== false){
                if(isset($user["sessions"][$data[1]]) && $user["sessions"][$data[1]]["ip"] ===  $_SERVER['REMOTE_ADDR']){
                    return true;
                }
                else{
                    SessionStore::destroySession();
                    return false;
                }
            }
            else{
                SessionStore::destroySession();
                return false;
            }
        }
        else{
            return false;
        }
    }
    public static function getCurrentSession(){
        if(SessionStore::hasSession()){
            $data = explode("\\", $_SESSION['login-data']);
            return Users::getUser($data[0]);
        }
        else{
            return false;
        }
    }
    public static function createSession($user){
        SessionStore::destroySession();
        $_SESSION['login-data'] = $user . "\\" . Users::addSession($user, $_SERVER['REMOTE_ADDR']);
    }
    public static function destroySession(){
        if(isset($_SESSION['login-data'])) {
            $data = explode("\\", $_SESSION['login-data']);
            Users::deleteSession($data[0], $data[1]);
            unset($_SESSION['login-data']);
        }
    }
}