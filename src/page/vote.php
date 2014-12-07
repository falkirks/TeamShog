<?php
namespace water\page;
use water\database\Domains;
use water\database\MongoConnector;
use water\domains\DomainCache;
use water\session\SessionStore;

class vote extends Page{
    public function showPage(){
        $user = SessionStore::getCurrentSession();
        if(isset($_GET["dir"]) && isset($_GET["domain"]) && isset($_GET["doc"]) && isset($_GET["sentence"])){
            $doc = DomainCache::getDocument($_GET["domain"], $_GET["doc"]);
            if($doc !== false && isset($doc["summary"][$_GET["sentence"]])){
                if($_GET["dir"] === "up"){
                    if(in_array($user['_id'], $doc["summary"][$_GET["sentence"]]["up"])){
                        echo "false";
                    }
                    else {
                        if (($pos = array_search($user['_id'], $doc["summary"][$_GET["sentence"]]["down"])) !== false) {
                            unset($doc["summary"][$_GET["sentence"]]["down"][$pos]);
                        }
                        $doc["summary"][$_GET["sentence"]]["up"][] = $user["_id"];
                        vote::updateVotes($_GET["domain"], $_GET["doc"], $doc);
                        die(json_encode(vote::formatArray($doc["summary"])));
                    }
                }
                elseif($_GET["dir"] === "down"){
                    if(in_array($user['_id'], $doc["summary"][$_GET["sentence"]]["down"])){
                        echo "false";
                    }
                    else {
                        if (($pos = array_search($user['_id'], $doc["summary"][$_GET["sentence"]]["up"])) !== false) {
                            unset($doc["summary"][$_GET["sentence"]]["up"][$pos]);
                        }
                        $doc["summary"][$_GET["sentence"]]["down"][] = $user["_id"];
                        vote::updateVotes($_GET["domain"], $_GET["doc"], $doc);
                        die(json_encode(vote::formatArray($doc["summary"])));
                    }
                }
                else{
                    echo "false";
                }
            }
            else{
                echo "false";
            }
        }
        else{
            if(isset($_GET["domain"]) && isset($_GET["doc"])) {
                $doc = DomainCache::getDocument($_GET["domain"], $_GET["doc"]);
                die(json_encode(vote::formatArray($doc["summary"])));
            }
            else{
                echo "false";
            }
        }
    }
    public static function formatArray(array $arr){
        $out = [];
        foreach($arr as $item){
            $out[] = [$item["up"], $item["down"]];
        }
        return $out;
    }
    public static function updateVotes($domain, $id, $doc){
        MongoConnector::getDomainsCollection()->update(["_id" => $domain], [
            '$set' => [
                "documents.$id" => $doc
            ]
        ]);
    }
    public function hasPermission(){
        return SessionStore::hasSession();
    }
}
