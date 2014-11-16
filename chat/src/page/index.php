<?php
namespace shogchat\page;

use shogchat\session\SessionStore;

class index extends Page{
    public function showPage($message = false){
            $user = SessionStore::getCurrentSession();
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Welcome!",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => ($user === false ? false : $user ),
                    "chat" => ($user === false ? false : $this->getTemplateEngine()->render($this->getTemplateSnip("chat"), [
                        "session" => str_replace("\\", "$$", $_SESSION['login-data'])
                    ]))
                ])
            ]);
    }
    public function hasPermission(){
        return true;
    }
}