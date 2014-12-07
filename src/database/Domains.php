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
    public static function addDocument($domain, $name, $url, $text, $summarized){
        MongoConnector::getDomainsCollection()->update(["_id" => $domain], [
            '$push' => [
                "documents" => [
                    "name" => $name,
                    "updated" => time(),
                    "url" => $url,
                    "text" => $text,
                    "summary" => $summarized,
                    "active" => true,
                    "drafts" => []
                ]
            ]
        ]);
    }
    public static function getDraft($domain, $id, $draftid){
        $doc = Domains::getDocument($domain, $id);
        if($doc !== false){
            if(isset($doc["drafts"][$draftid])){
                $doc["drafts"][$draftid]["draftid"] = $draftid; // Just in case
                return $doc["drafts"][$draftid]["draftid"];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    public static function addDraft($domain, $id, $text, $summary, $user){
        $doc = Domains::getDocument($domain, $id);
        if($doc !== false){
            $doc["drafts"][] = [
                "draftid" => count($doc["drafts"]),
                "text" => $text,
                "summary" => $summary,
                "user" => $user
            ];
            Domains::setDocument($domain, $id, $doc);
        }
        else{
            return false;
        }
    }
    public static function updateDraft($domain, $id, $draftid, $text, $summary){
        $doc = Domains::getDocument($domain, $id);
        if($doc !== false && isset($doc["drafts"][$draftid])){
            Domains::setDocument($domain, $id, array_merge($doc["drafts"][$draftid], ["text" => $text, "summary" => $summary]));
            return true;
        }
        else{
            return false;
        }
    }
    public static function userHasDraftAccess($user, $domain, $id, $draftid){
        $draft = Domains::getDraft($domain, $id, $draftid);
        return $draft !== false && $draft["user"] === $user;
    }
    public static function updateDocument($domain, $id, $text, $summarized, $active = true){
        $domain = Domains::getDomain($domain);
        if($domain !== false){
            $domain["documents"][$id] = [
                "name" => $domain["documents"][$id]["name"],
                "updated" => time(),
                "url" => $domain["documents"][$id]["url"],
                "text" => $text,
                "summary" => $summarized,
                "active" => $active,
                "drafts" => $domain["documents"][$id]["drafts"]
            ];
            Domains::updateDomain($domain);
            return true;
        }
        else{
            return false;
        }
    }
    public static function setDocument($domain, $id, $document){
        $domain = Domains::getDomain($domain);
        if($domain !== false){
            $domain["documents"][$id] = $document;
            Domains::updateDomain($domain);
            return true;
        }
        else{
            return false;
        }
    }
    public static function getDocument($domain, $id){
        $domain = Domains::getDomain($domain);
        if($domain !== false){
            if(isset($domain["documents"][$id])){
                $domain["documents"][$id]["id"] = $id;
                return $domain["documents"][$id];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    public static function updateDomain($domain){
        return MongoConnector::getDomainsCollection()->update([$domain['_id']], $domain) != null ? true : var_dump("Error writing to db.") != null;
    }
    public static function getDomain($domain){
        $domain = MongoConnector::getDomainsCollection()->findOne(["_id" => $domain]);
        return $domain != null ? $domain : false;
    }
}