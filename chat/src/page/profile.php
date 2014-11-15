<?php
namespace shogchat\page;

use Github\Client;
use shogchat\database\Users;
use shogchat\session\SessionStore;

class profile extends Page{
    public function showPage(){
        $user = SessionStore::getCurrentSession();
        $user["sessions"] = array_values($user["sessions"]);
        if(isset($_POST['new-password'])){
            if(strlen($_POST['new-password']) >= 6){
                Users::changePassword($user["_id"], $_POST['new-password']);
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "changesuccess" => true
                    ])
                ]);
            }
            else{
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "changefailed" => true
                    ])
                ]);
            }
        }
        elseif(isset($_GET['reloadrepos'])){
            try {
                $client = new Client();
                $client->authenticate($user['token'], Client::AUTH_HTTP_TOKEN);
                $repos = [];
                foreach ($client->api('current_user')->repositories('member', 'updated', 'desc') as $repo) {
                    $repos[] = [
                        "name" => $repo["full_name"],
                        "isPrivate" => $repo["private"]
                    ];
                }
                Users::updateRepos($user['_id'], $repos);
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "reposupdated" => true
                    ])
                ]);
            }
            catch(\Exception $e){
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "repoupdatefailed" => true
                    ])
                ]);
            }
        }
        else{
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Profile",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "user" => $user
                ])
            ]);
        }
    }
    public function hasPermission(){
        return SessionStore::hasSession();
    }
}