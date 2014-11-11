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
        $server = IoServer::factory(new ChatServer(), 8080);
        $server->run();
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