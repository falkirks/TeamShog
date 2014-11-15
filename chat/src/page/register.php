<?php
namespace shogchat\page;

class register extends Page{
    public function showPage(){
        if(isset($_POST["register-data"])){
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Register",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "error" => "Oh no! Something went awry with your registration."
                ])
            ]);
        }
        else {
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Register",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [])
            ]);
        }
        //echo $this->getTemplateEngine()->render($this->getTemplate(), []);
    }
    public function hasPermission(){
        return true;
    }

}