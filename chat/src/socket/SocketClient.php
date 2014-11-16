<?php
namespace shogchat\socket;

use Ratchet\ConnectionInterface;
use shogchat\database\Users;

class SocketClient{
    /** @var ConnectionInterface  */
    private $connectionInterface;
    private $authenticated = false;
    private $user;
    private $channels;
    public function __construct(ConnectionInterface $connectionInterface){
        $this->connectionInterface = $connectionInterface;
        $this->channels = [];
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnectionInterface(){
        return $this->connectionInterface;
    }
    public function authenticate($payload){
        if(isset($payload["key"])){
            $data = explode("$$", $payload["key"]);
            $user = Users::getUser($data[0]);
            if($user !== false){
                if(isset($user["sessions"][$data[1]]) && $user["sessions"][$data[1]]["ip"] ===  $this->connectionInterface->remoteAddress){
                    $this->user = $user;
                    $this->authenticated = true;
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
        else{
            return false;
        }
    }
    public function addChannel($name){
        $this->channels[] = $name;
    }
    public function isMemberOf($name){
        return in_array($name, $this->channels);
    }
    /**
     * @return boolean
     */
    public function isAuthenticated(){
        return $this->authenticated;
    }

    /**
     * @return mixed
     */
    public function getUser(){
        return $this->user;
    }
}