<?php
namespace shogchat\page;

use shogchat\database\Users;

class login extends Page{
    public function showPage(){
        if(isset($_POST["login-username"]) && isset($_POST["login-password"])){
            if(Users::checkLogin($_POST["login-username"], $_POST["login-password"])){
                //TODO give them a session and redirect
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Login",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "error" => "Logged in."
                    ])
                ]);
            }
            else{
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Login",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "error" => "Your username or password appear incorrect."
                    ])
                ]);
            }
        }
        else {
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Login",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [])
            ]);
        }
    }
    public function hasPermission(){
        return true;
    }

}