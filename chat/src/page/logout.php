<?php
namespace shogchat\page;

use shogchat\session\SessionStore;

class logout extends Page{
    public function showPage(){
        SessionStore::destroySession();
        (new index())->showPage("You are now logged out.");
    }
    public function hasPermission(){
        return SessionStore::hasSession();
    }
}