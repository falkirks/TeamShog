<?php
namespace shogchat\socket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use shogchat\database\Channels;
use shogchat\database\Users;

class ChatServer implements MessageComponentInterface{
    /** @var \SplObjectStorage SocketClient[] */
    private $clients;
    public function __construct(){
        $this->clients = new \SplObjectStorage;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn){
        $this->clients->attach($conn, new SocketClient($conn));
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn){
        $this->clients->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e){
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg){
        $json = json_decode($msg, true);
        var_dump($json);
        switch($json["type"]){
            case "message":
                if($this->clients->offsetGet($from)->isAuthenticated()){

                }
                break;
            case "channel":
                if($this->clients->offsetGet($from)->isAuthenticated()) {
                    if ($json["payload"]["verb"] == "add") {
                        $reply = $json;
                        $chan = Channels::getChannel($json["payload"]["channel"]);
                        if($chan !== null) {
                            if (!$chan["isPrivate"] || Users::canAccessChannel($this->clients->offsetGet($from)->getUser()["_id"], $json["payload"]["channel"])) {
                                $reply["payload"]["verb"] = "add";
                            } else {
                                $reply["payload"]["verb"] = "error";
                            }
                        }
                        else{
                            $reply["payload"]["verb"] = "error";
                        }
                        $from->send(json_encode($reply));
                    }
                }
                break;
            case "auth":
                $from->send(json_encode([
                    "type" => "authreply",
                    "payload" => [
                        "done" => $this->clients->offsetGet($from)->authenticate($json["payload"])
                    ]
                ]));
                break;
            default:
                Logger::warning("Bad message got.");
                break;
        }
    }

}