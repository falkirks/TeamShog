<?php
namespace water\page;

use water\database\Users;
use water\session\SessionStore;

class register extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();
        if(!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["email"])){
            if($_POST["password"] === $_POST["rpassword"]){
                if(Users::getUser($_POST["username"]) === false) {
                    Users::createUser($_POST["username"], $_POST["email"], $_POST["password"]);
                    (new login())->showPage(); //This will do magic and log them in
                }
                else{
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "Register",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "message" => "A user already exists with that name.",
                            "user" => $user
                        ])
                    ]);
                }
            }
            else{
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Register",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "message" => "Passwords must match.",
                        "user" => $user
                    ])
                ]);
            }
        }
        else {
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Register",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => ($message === false ? false : $message),
                    "user" => $user
                ])
            ]);
        }
    }
    public function hasPermission(){
        return true;
    }
}