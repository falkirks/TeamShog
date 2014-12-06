<?php
namespace water\page;

use water\database\Domains;
use water\domains\DomainCache;
use water\PageRouter;
use water\session\SessionStore;

class view extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();
        if(count(PageRouter::getPath()) == 0){
            (new index())->showPage("You must specify a domain.");
        }
        else if(count(PageRouter::getPath()) == 1) {
            /*
             * Display list
             */
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "View",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => $user,
                    "textview" => false,
                    "domain" => $this->transformDomainArray(DomainCache::getDomain(PageRouter::getPath()[0])),
                    "document" => false,
                    "isDraft" => false
                ])
            ]);
        }
        elseif(count(PageRouter::getPath()) == 2){
            /*
             * Display side by side view
             */
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "View",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => $user,
                    "textview" => true,
                    "domain" => $this->transformDomainArray(DomainCache::getDomain(PageRouter::getPath()[0])),
                    "document" => DomainCache::getDocument(PageRouter::getPath()[0], PageRouter::getPath()[1]),
                    "isDraft" => false
                ])
            ]);
        }
        else{
            /*
             * Display side by side view of a user draft
             */
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "View",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => $user,
                    "textview" => true,
                    "domain" => $this->transformDomainArray(DomainCache::getDomain(PageRouter::getPath()[0])),
                    "document" => Domains::getDraft(PageRouter::getPath()[0], PageRouter::getPath()[1], PageRouter::getPath()[2]),
                    "isDraft" => true
                ])
            ]);
        }
    }
    public function transformDomainArray($arr){
        if(is_array($arr)) {
            foreach ($arr["documents"] as $i => $item) {
                $arr["documents"][$i]["id"] = $i;
            }
            return $arr;
        }
        else{
            return false;
        }
    }
    public function hasPermission(){
        return true;
    }
}