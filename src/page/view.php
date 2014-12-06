<?php
namespace water\page;

use water\database\Domains;
use water\PageRouter;
use water\session\SessionStore;

class view extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();
        if(count(PageRouter::getPath()) == 1) {
            /*
             * Display list
             */
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "View",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => $user,
                    "textview" => false,
                    "domain" => Domains::getDomain(PageRouter::getPath()[0]), //TODO crawl domain here
                    "document" => false
                ])
            ]);
        }
        else{
            /*
             * Display side by side view
             */
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "View",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => $user,
                    "textview" => true,
                    "domain" => Domains::getDomain(PageRouter::getPath()[0]),
                    "document" => Domains::getDocument(PageRouter::getPath()[0], PageRouter::getPath()[1])
                ])
            ]);
        }
    }
    public function hasPermission(){
        return true;
    }
}