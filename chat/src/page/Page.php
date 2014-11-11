<?php
namespace shogchat\page;
abstract class Page{
    protected $engine;
    protected function getTemplate(){
        $path = explode("\\", get_class($this));
        return file_get_contents(MAIN_PATH . "/tpl/" . end($path) . ".html");
    }
    protected function getTemplateSnip($name){
        return file_get_contents(MAIN_PATH . "/tpl/snips/$name.html");
    }
    protected function getTemplateEngine(){
        if($this->engine == null){
            $this->engine = new \Mustache_Engine();
        }
        return $this->engine;
    }
    abstract public function showPage();
    abstract public function hasPermission();
}