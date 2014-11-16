<?php
namespace shogchat\socket;

class IRCClient{
    private $socket;
    private $ip;
    private $host;
    private $nick;
    private $ident;
    private $buf;
    private $realName;

    public function __construct($host, $ip, $socket){
        $this->host = $host;
        $this->ip = $ip;
        $this->socket = $socket;
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
    }
    public function close(){
        socket_close($this->getSocket());
    }
}