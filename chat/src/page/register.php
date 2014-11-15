<?php
namespace shogchat\page;

class register extends Page{
    public function showPage(){
        echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
            "title" => "Register",
            "content" => $this->getTemplateEngine()->render($this->getTemplate(), [])
        ]);
        //echo $this->getTemplateEngine()->render($this->getTemplate(), []);
    }
    public function hasPermission(){
        return true;
    }

}