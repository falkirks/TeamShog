<?php
namespace water\domains;
use water\database\Domains;

class DomainCache{
    public static function getDomain($name){
        $domain = Domains::getDomain($name);
        if($domain !== false){
            //TODO check when it was "added"
            return $domain;
        }
        else{
            $docs = LegalFinder::getLegalDomain($name);
            if(!empty($docs) && $docs !== false){
                Domains::createDomain($name, $docs);
                return Domains::getDomain($name);
            }
            else{
                return false;
            }
        }
    }
    public static function getDocument($domain, $id){
        $doc = Domains::getDocument($domain, $id);
        if($doc !== false){
            /*(if($doc["updated"] + 3600*7 < time()){
                $data = LegalFinder::getUpdatedDoc($doc["url"], $doc["name"]);
                if($data !== false) {
                    $doc = array_merge($doc, $data);
                }
                else{
                    $doc["active"] = false;
                }
                Domains::setDocument($domain, $id, $doc);
            }*/
            $new = [];
            foreach($doc["words"] as $word => $count){
                $new[] = ["word" => $word, "count" => $count];
            }
            $doc["words"] = $new;
            return $doc;
        }
        else{
            return false;
        }
    }
}