<?php
namespace water\page;
use water\database\Domains;
use water\domains\DomainCache;
use water\session\SessionStore;

class vote extends Page{
    public function showPage(){
        $user = SessionStore::getCurrentSession();
        if(!empty($_GET["dir"]) && !empty($_GET["domain"]) && !empty($_GET["doc"]) && !empty($_GET["sentence"])){
            $doc = DomainCache::getDocument($_GET["domain"], $_GET["doc"]);
            if($doc !== false && isset($doc["summary"][$_GET["sentence"]])){
                if($_GET["dir"] === "up"){
                    if(in_array($user['_id'], $doc["summary"][$_GET["sentence"]]["up"])){
                        echo "5";
                    }
                    else {
                        if (($pos = array_search($user['_id'], $doc["summary"][$_GET["sentence"]]["down"])) !== false) {
                            unset($doc["summary"][$_GET["sentence"]]["down"][$pos]);
                        }
                        $doc["summary"][$_GET["sentence"]]["up"][] = $user["_id"];
                        Domains::setDocument($_GET["domain"], $_GET["doc"], $doc);
                        die(json_encode(vote::formatArray($doc["summary"])));
                    }
                }
                elseif($_GET["dir"] === "down"){
                    if(in_array($user['_id'], $doc["summary"][$_GET["sentence"]]["down"])){
                        echo "4";
                    }
                    else {
                        if (($pos = array_search($user['_id'], $doc["summary"][$_GET["sentence"]]["up"])) !== false) {
                            unset($doc["summary"][$_GET["sentence"]]["up"][$pos]);
                        }
                        $doc["summary"][$_GET["sentence"]]["down"][] = $user["_id"];
                        Domains::setDocument($_GET["domain"], $_GET["doc"], $doc);
                        die(json_encode(vote::formatArray($doc["summary"])));
                    }
                }
                else{
                    echo "3";
                }
            }
            else{
                echo "2";
            }
        }
        else{
            echo "1";
        }
    }
    public static function formatArray(array $arr){
        $out = [];
        foreach($arr as $item){
            $out[] = [$item["up"], $item["down"]];
        }
        return $out;
    }
    public function hasPermission(){
        return SessionStore::hasSession();
    }
}
