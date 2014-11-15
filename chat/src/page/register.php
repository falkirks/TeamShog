<?php
namespace shogchat\page;

use Github\Client;

class register extends Page{
    public function showPage(){
        if(isset($_POST["register-data"])){
            try{
                $client = new Client();
                $client->authenticate($_POST["register-data"], Client::AUTH_HTTP_TOKEN);
                $users = $client->api('current_user')->follow()->all();
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Register",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "success" => true
                    ])
                ]);
            }
            catch(\Exception $e) {
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Register",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "error" => "Oh no! Something went awry with your registration."
                    ])
                ]);
            }
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