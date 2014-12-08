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
                    "title" => "WaterTOS",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "message" => "Search term must be at least 3 characters long",
                        "user" => $user
                    ]),
                    "user" => $user
                ]);
            }else{
                $domain = GoogleSearch::getTopResultDomain($_POST["name"]);
                if($domain !== false) {
                    header("Location: /view/$domain");
                    die();
                }
                else{
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "WaterTOS",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "message" => "No results could be found.",
                            "user" => $user
                        ]),
                        "user" => $user
                    ]);
                }
            }
        }else{
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "WaterTOS",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "message" => $message,
                    "user" => $user
                ]),
                "user" => $user
            ]);
        }
        //var_dump(LegalFinder::getLegalDomain("github.com"));
    }
    public function hasPermission(){
        return true;
    }
}