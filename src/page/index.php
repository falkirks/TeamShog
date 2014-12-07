<?php
namespace water\page;

use water\api\Aylien;
use water\domains\LegalFinder;
use water\session\SessionStore;

class index extends Page{
    public function showPage($message = false){
        $user = SessionStore::getCurrentSession();

        $params = array('url' => 'https://www.digitalocean.com/legal/terms/');
        $summary = Aylien::call_api('summary', $params);
        var_dump($summary);

        if(!empty($_POST["name"])) {
            
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