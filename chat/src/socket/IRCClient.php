<?php
namespace shogchat\socket;

use shogchat\database\Users;

class IRCClient{
    private $socket;
    private $ip;
    private $host;
    private $nick;
    private $ident;
    private $buf;
    private $realName;

    private $password = false;
    /** @var IRCBridge  */
    private $server;

    public function __construct($host, $ip, $socket, IRCBridge $server){
        $this->host = $host;
        $this->ip = $ip;
        $this->socket = $socket;
        $this->server = $server;
    }

    /**
     * @return mixed
     */
    public function getBuf(){
        return $this->buf;
    }

    /**
     * @param mixed $buf
     */
    public function setBuf($buf){
        $this->buf = $buf;
    }

    /**
     * @return mixed
     */
    public function getHost(){
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host){
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getIdent(){
        return $this->ident;
    }

    /**
     * @param mixed $ident
     */
    public function setIdent($ident){
        $this->ident = $ident;
    }

    /**
     * @return mixed
     */
    public function getIp(){
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip){
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getSocket(){
        return $this->socket;
    }

    /**
     * @param mixed $socket
     */
    public function setSocket($socket){
        $this->socket = $socket;
    }

    /**
     * @return mixed
     */
    public function getRealName(){
        return $this->realName;
    }

    /**
     * @param mixed $realName
     */
    public function setRealName($realName){
        $this->realName = $realName;
    }

    /**
     * @return mixed
     */
    public function getNick(){
        return $this->nick;
    }

    /**
     * @param mixed $nick
     */
    public function setNick($nick){
        $this->nick = $nick;
    }

    public function send($msg){
        socket_write($this->getSocket(), $msg);
        Logger::info("$msg sent to IRC client.");
    }
    public function read(){
        return socket_read($this->getSocket(), 2048);
    }
    public function handleMessage($msg){
        Logger::info("Got $msg");
        $msg = explode(" ", $msg);
        switch($msg[0]){
            case "PASS":
                $this->password = trim($msg[1]);
                break;
            case "NICK":
                foreach($this->server->getClients() as $client){
                    if(strtolower($client->getNick()) === $msg[1]){
                        $this->send("That nickname is already in use.");
                        break;
                    }
                }
                $this->send("Nickname set.");
                $this->setNick($msg[1]);
                break;
            case "USER":
                if($this->password) {
                    if (Users::checkLogin($msg[1], $this->password)) {
                        $this->setIdent($msg[1]);
                        $this->setRealName($msg[4]);
                        $this->send("Logged in.");
                    }
                    else{
                        $this->send("Bad password.");
                        $this->close();
                    }
                }
                else{
                    $this->send("You haven't set a password.");
                    $this->close();
                }
                break;
            default:
                Logger::info($msg[0] . " is an unrecognized IRC command.");
                break;
        }
    }
    public function close(){
        $this->server->closeClient($this);
    }
}