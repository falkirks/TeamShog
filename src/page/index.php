<?php
namespace water\page;

use water\api\Aylien;
use water\api\GoogleSearch;
use water\domains\LegalFinder;
use water\PageRouter;
use water\session\SessionStore;

class index extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();

        if(!empty($_POST["name"])) {
            if(strlen($_POST["name"]) < 3){
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Welcome!",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "message" => "Search term must be at least 3 characters long",
                        "user" => $user
                    ])
                ]);
            }else{
                $domain = GoogleSearch::getTopResultDomain($_POST["name"]);
                //TODO make this less hacky
                if($domain !== false) {
                    PageRouter::setPath([$domain]);
                    (new view())->showPage();
                }
                else{
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "WaterTOS",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "message" => "No results could be found.",
                            "user" => $user
                        ])
                    ]);
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