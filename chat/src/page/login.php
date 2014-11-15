<?php
namespace shogchat\page;

class login extends Page{
    public function showPage(){
        echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
            "title" => "Login",
            "content" => $this->getTemplateEngine()->render($this->getTemplate(), [])
        ]);
    }
    public function hasPermission(){
        return true;
    }

}