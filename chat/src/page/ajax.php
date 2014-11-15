<?php
namespace shogchat\page;

use Github\Client;
/*
 * This page is for various AJAX calls
 */
class ajax extends Page{
    public function showPage(){
        if(isset($_GET['action'])){
            switch($_GET['action']){
                case 'verifyGitHubToken':
                    try {
                        $client = new Client();
                        $client->authenticate($_GET["registerData"], Client::AUTH_HTTP_TOKEN);
                        $client->api('current_user')->follow()->all();
                        exit("true");
                    }
                    catch(\Exception $e){
                        exit("false");
                    }
                    break;

            }
        }
        else{
            exit("Action required");
        }
    }
    public function hasPermission(){
        return true;
    }

}