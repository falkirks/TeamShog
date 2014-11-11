<?php
namespace shogchat\page;

class index extends Page{
    public function showPage(){
        print $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
            "content" => $this->getTemplateEngine()->render($this->getTemplate())
        ]);
    }
    public function hasPermission(){
        return true;
    }
}