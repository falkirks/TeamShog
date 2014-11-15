<?php
namespace shogchat\page;

class login extends Page{
    public function showPage(){
        if(isset($_POST["login-username"]) && isset($_POST["login-password"])){
            //TODO do checking and give session cookie
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