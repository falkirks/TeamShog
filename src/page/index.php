<?php
namespace water\page;

use water\domains\LegalFinder;
use water\session\SessionStore;

class index extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();
        echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
            "title" => "Welcome!",
            "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                "message" => $message,
                "user" => $user
            ])
        ]);
    }
    public function hasPermission(){
        return true;
    }
}