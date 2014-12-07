<?php
namespace water\page;

use water\session\SessionStore;

class about extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();

        echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
            "title" => "About",
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