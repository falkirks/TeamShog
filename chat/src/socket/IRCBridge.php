<?php
namespace shogchat\socket;

class IRCBridge{
    private $socket;
    /** @var IRCClient[] */
    private $clients;
    private $chatServer;
    public function __construct(ChatServer $chatServer){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($this->socket, "0.0.0.0", 6668);
        socket_listen($this->socket, 25);
        socket_set_nonblock($this->socket);
        $this->clients = [];
        $this->chatServer = $chatServer;
        $this->chatServer->bindToIRC($this);
    }
    public function acceptConnection(){
        if ($client = @socket_accept($this->socket)) {
            socket_getpeername($client, $ip);
            $host = $this->dns_timeout($ip);
            $client = new IRCClient($host, $ip, $client, $this);
            $client->send("NOTICE AUTH :*** Found your hostname." . "\r\n");
            $this->clients[] = $client;

        }
    }
    public function readConnections(){
        foreach($this->clients as $client){
            $read = $client->read();
            if($read != false){
                $read = explode("\n", $read);
                foreach($read as $msg){
                    if(!empty($msg)) {
                        $client->handleMessage($msg);
                    }
                }
            }
        }
    }
    public function closeSockets(){
        foreach($this->clients as $client){
            $client->close();
        }
        socket_close($this->getSocket());
    }
    public function dns_timeout($ip) {
        $res = `nslookup -timeout=3 -retry=1 $ip`;
        if (preg_match('/\nName:(.*)\n/', $res, $out)) {
            return trim($out[1]);
        } else {
            return $ip;
        }
    }

    /**
     * @return resource
     */
    public function getSocket(){
        return $this->socket;
    }
    public function closeClient(IRCClient $client){
        socket_close($client->getSocket());
        unset($this->clients[array_search($client, $this->clients)]);
    }
    /**
     * @return IRCClient[]
     */
    public function getClients(){
        return $this->clients;
    }
    public function sendMessageToChannel($msg, $from, $chan){
        foreach($this->clients as $client){
            if($client->isMemberOf($chan)){
                if(!$from instanceof ChatServer || $from != $client) {
                    $client->sendMessage($msg, $from, "#$chan");
                }
            }
        }
    }

    /**
     * @return ChatServer
     */
    public function getChatServer(){
        return $this->chatServer;
    }

}