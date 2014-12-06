<?php
namespace water\database;

class Domains{
    public static function createDomain($domain, $documents = []){
        MongoConnector::getDomainsCollection()->insert([
            "_id" => $domain,
            "added" => time(),
            "documents" => $documents
        ]);
    }
    public static function addDocument($domain, $name, $text, $summarized){
        MongoConnector::getDomainsCollection()->update(["_id" => $domain], [
            '$push' => [
                "documents" => [
                    "name" => $name,
                    "updated" => time(),
                    "text" => $text,
                    "summary" => $summarized
                ]
            ]
        ]);
    }
    public static function updateDocument($domain, $id, $text, $summarized){
        $domain = Domains::getDomain($domain);
        if($domain !== false){
            $domain["documents"][$id] = [
                "name" => $domain["documents"][$id]["name"],
                "updated" => time(),
                "text" => $text,
                "summarized" => $summarized
            ];
            return true;
        }
        else{
            return false;
        }
    }
    public static function updateDomain($domain){
        return MongoConnector::getDomainsCollection()->update([$domain['_id']], $domain) != null ? true : false;
    }
    public static function getDomain($domain){
        $domain = MongoConnector::getDomainsCollection()->findOne(["_id" => $domain]);
        return $domain != null ? $domain : false;
    }
}