<?php
namespace shogchat\page;

use Github\Client;
use shogchat\database\Channels;
use shogchat\database\Users;

class register extends Page{
    public function showPage(){
        if(isset($_POST["register-data"]) && isset($_POST["register-password"])){
            try{
                $client = new Client();
                $client->authenticate($_POST["register-data"], Client::AUTH_HTTP_TOKEN);
                $user = $client->api('current_user')->show();
                $repos = [];
                foreach($client->api('current_user')->repositories('member', 'updated', 'desc') as $repo){
                    $repos[] = [
                        "name" => $repo["full_name"],
                        "isPrivate" => $repo["private"]
                    ];
                }
                if(strlen($_POST["register-password"]) >= 6) {
                    Users::createUser($user["login"], $client->api('current_user')->emails()->all()[0], $_POST["register-password"], $_POST["register-data"], $repos);
                    Channels::addChannels($repos);
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "Register",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "user" => $user
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
                        "error" => "Oh no! Your registration couldn't be completed. Do you already have an account? Is your token valid?"
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