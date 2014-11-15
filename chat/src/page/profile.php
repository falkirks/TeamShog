<?php
namespace shogchat\page;

use shogchat\database\Users;
use shogchat\session\SessionStore;

class profile extends Page{
    public function showPage(){
        $user = SessionStore::getCurrentSession();
        $user["sessions"] = array_values($user["sessions"]);
        if(isset($_POST['new-password'])){
            if(strlen($_POST['new-password']) >= 6){
                Users::changePassword($user["_id"], $_POST['new-password']);
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "changesuccess" => true
                    ])
                ]);
            }
            else{
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "changefailed" => true
                    ])
                ]);
            }
        }
        else{
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Profile",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "user" => $user
                ])
            ]);
        }
    }
    public function hasPermission(){
        return SessionStore::hasSession();
    }
}