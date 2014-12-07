<?php
namespace water\page;

use water\api\Aylien;
use water\domains\LegalFinder;
use water\PageRouter;
use water\session\SessionStore;

class index extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();

        if(!empty($_POST["name"])) {
            if(strlen($_POST["name"])<3){
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Login",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "message" => "Search term must be at least 3 characters long",
                        "user" => $user
                    ])
                ]);
            }else{
                //TODO:Search function in JS
                if(!filter_var($this, FILTER_VALIDATE_URL)){
                    //Not a URL
                }else{
                    //Is a URL
                }
            }
        }else{
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Welcome!",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => $message,
                    "user" => $user
                ])
            ]);
        }
        //var_dump(LegalFinder::getLegalDomain("github.com"));
    }
    public function hasPermission(){
        return true;
    }
}