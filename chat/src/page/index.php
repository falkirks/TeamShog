<?php
namespace shogchat\page;

class index extends Page{
    public function showPage($error = false){
        if($error === false) {
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Welcome!",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [])
            ]);
        }
        else{
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Welcome!",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "error" => $error
                ])
            ]);
        }
        //echo $this->getTemplateEngine()->render($this->getTemplate(), []);
    }
    public function hasPermission(){
        return true;
    }
}