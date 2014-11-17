<?php
use Ratchet\Server\IoServer;
use shogchat\PageRouter;
use shogchat\socket\ChatServer;
use shogchat\socket\Logger;

require 'vendor/autoload.php';
define("MAIN_PATH", realpath(__DIR__));
if(php_sapi_name() === 'cli'){
    Logger::info("Starting socket server...");
    try {
        $chat = new ChatServer();
        $server = IoServer::factory(
            new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer(
                    $chat
                )
            ),
            8080
        );

        $irc = new \shogchat\socket\IRCBridge($chat);
        while(is_resource($irc->getSocket())){
            $server->loop->tick();
            $irc->acceptConnection();
            $irc->readConnections();
        }
        $irc->closeSockets();
    }
    catch(Exception $e){
        Logger::error($e->getMessage());
        exit(1);
    }
}
else{
    if(@fsockopen("localhost", 8080)) {
        PageRouter::route();
    }
    else{
        exit("Websocket server is offline.");
    }
}