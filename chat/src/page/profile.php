<?php
namespace shogchat\page;

use Github\Client;
use shogchat\database\Channels;
use shogchat\database\Users;
use shogchat\session\SessionStore;

class profile extends Page{
    public function showPage(){
        $user = SessionStore::getCurrentSession();
        $sessions = [];
        foreach($user["sessions"] as $id => $data){
            $data["id"] = $id;
            $sessions[] = $data;
        }
        $user["sessions"] = $sessions;
        if(isset($_POST['new-password'])){
            if(strlen($_POST['new-password']) >= 6){
                Users::changePassword($user["_id"], $_POST['new-password']);
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "message" => "Your password has been changed."
                    ])
                ]);
            }
            else{
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "error" => "Failed to change password. Are you sure it's six chars or longer?"
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
                Channels::addChannels($repos);
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "message" => "Your repositories have been reloaded."
                    ])
                ]);
            }
            catch(\Exception $e){
                echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                    "title" => "Profile",
                    "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                        "user" => $user,
                        "error" => "Failed to update repositories."
                    ])
                ]);
            }
        }
        elseif(isset($_GET["changetoken"])){
            echo "This feature is still being worked on.";
        }
        elseif(isset($_GET["destroysession"])){
            Users::deleteSession($user['_id'], $_GET["destroysession"]);
            echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                "title" => "Profile",
                "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                    "user" => $user,
                    "message" => "The session has been closed. The client is now logged out."
                ])
            ]);
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