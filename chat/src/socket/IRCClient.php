<?php
namespace shogchat\socket;

use shogchat\database\Channels;
use shogchat\database\Users;

class IRCClient implements Client{
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

    private $authenticated = false;

    private $user;
    private $channels;
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
        Logger::info("Nick set");
        $this->nick = trim($nick);
    }

    public function sendNumerical($msg){
        $this->send(":shogchat " . $msg);

    }
    public function sendMessage($msg, $from, $to){
        $this->send(":$from PRIVMSG $to :$msg");
    }
    public function send($msg){
        Logger::info($msg);
        socket_write($this->getSocket(), $msg . "\r\n");
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
                        $this->sendNumerical("NOTICE AUTH :*** That nick is already in use..");
                        break;
                    }
                }
                $this->setNick($msg[1]);
                break;
            case "USER":
                if($this->password) {
                    if (Users::checkLogin($msg[1], $this->password)) {
                        $this->setIdent($msg[1]);
                        $this->setRealName($msg[4]);
                        $this->sendNumerical("001 {$this->nick} :Welcome to the Internet Relay Network {$this}");
                        $this->sendNumerical("002 {$this->nick} :Your host is shogchat, running version a development build.");
                        $this->sendNumerical("003 {$this->nick} :This server was created " . @date( 'r' ));

                        $this->sendNumerical("NICK " . $this->nick);

                        $this->authenticated = true;
                        $this->user = Users::getUser($msg[1]);
                    }
                    else{
                        $this->sendNumerical("464 {$this->nick} :The password you have set is incorrect.");
                        $this->close();
                    }
                }
                else{
                    $this->sendNumerical("464 {$this->nick} :You haven't set a password.");
                    $this->close();
                }
                break;
            case "JOIN":
                if($this->isAuthenticated()) {
                    $chans = explode(",", $msg[1]);
                    foreach ($chans as $chan) {
                        $chan = Channels::getChannel(trim(substr($chan, 1)));
                        if($chan != null) {
                            if (!$chan["private"]) {
                                if (!in_array($this->getUser()['_id'], $chan["banned"])) {
                                    $this->addChannel($chan["_id"]);
                                    $this->sendNumerical("332 {$this->nick} #{$chan["_id"]} :This is a public channel.");
                                } else {
                                    $this->sendNumerical("474 {$this->nick} :You are banned from this channel.");
                                }
                            } else {
                                if (Users::isRepoOwner($this->getUser()['_id'], $chan["_id"])) {
                                    $this->addChannel($chan["_id"]);
                                    $this->sendNumerical("332 {$this->nick} #{$chan["_id"]} :This is a private channel.");
                                } else {
                                    $this->sendNumerical("473 {$this->nick} :This is a private channel, you need an invite.");
                                }
                            }
                        } else{
                            $this->sendNumerical("403 {$this->nick} :That repository isn't registered with ShogChat.");
                        }
                    }
                }
                break;
            case "PRIVMSG":
                $chan = substr($msg[1], 1);
                if($this->isMemberOf($chan)){
                    if($msg[2]{0} == ":"){
                        $msg[2] = substr($msg[2], 1);
                        $msg[2] = implode(" ", array_slice($msg, 2));
                    }
                    $this->server->sendMessageToChannel($msg[2], $this, $chan);
                    $this->server->getChatServer()->sendMessageToChannel($msg[2], $this->ident, $chan);
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

    public function __toString(){
       return $this->getNick() . "!" . $this->getIdent() . "@" . $this->getHost();
    }

    public function isMemberOf($name){
        return in_array($name, $this->channels);
    }

    public function addChannel($name){
        $this->send(":{$this} JOIN #$name");
        //$this->send("MODE #$name +sn");
        $this->channels[] = $name;
    }

    /**
     * @return mixed
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function isAuthenticated(){
        return $this->authenticated;
    }

}