<?php
namespace shogchat\database;

class Channels{
    public static function addChannels($chans){
        foreach($chans as $chan) {
            Channels::addChannel($chan);
        }
    }
    public static function addChannel($chan){
        try {
            MongoConnector::getChannelCollection()->update(["_id" => $chan["name"]], [
                "_id" => $chan["name"],
                "banned" => [],
                "private" => $chan["isPrivate"]
            ], ["upsert" => true]);
        }
        catch(\Exception $e){

        }
    }
}