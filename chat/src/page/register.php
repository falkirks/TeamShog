<?php
namespace shogchat\page;

use Github\Client;

class register extends Page{
    public function showPage(){
        if(isset($_POST["register-data"]) && isset($_POST["register-password"])){
            try{
                $client = new Client();
                $client->authenticate($_POST["register-data"], Client::AUTH_HTTP_TOKEN);
                $client->api('current_user')->follow()->all();
                if(count($_POST["register-password"]) >= 6) {
                    //TODO insert into database
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "Register",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "success" => true
                        ])
                    ]);
                }
                else{
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "Register",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "error" => "Passwords must be at least 6 characters long."
                        ])
                    ]);
                }
            }
            catch(\Exception $e) {
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Register",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "error" => "Oh no! Your registration data doesn't appear valid."
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