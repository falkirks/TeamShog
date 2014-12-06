<?php
namespace water\page;

use water\database\Domains;
use water\domains\DomainCache;
use water\PageRouter;
use water\session\SessionStore;

class draft extends Page{
    public function showPage(){
        if(!empty($_POST['step1']) && !empty($_POST['step2'])){
            if (isset(PageRouter::getPath()[1])) {
                if (isset(PageRouter::getPath()[2])) {
                    Domains::updateDraft(PageRouter::getPath()[0], PageRouter::getPath()[1], PageRouter::getPath()[2], $_POST['step1'], $_POST['step2']);
                }
                else{
                    Domains::addDraft(PageRouter::getPath()[0], PageRouter::getPath()[1], $_POST['step1'], $_POST['step2'], SessionStore::getCurrentSession());
                }
                (new index())->showPage("Your draft has been saved.");
            }
            else{
                (new index())->showPage("You must specify a domain and a document.");
            }
        }
        else {
            $user = SessionStore::getCurrentSession();
            if (isset(PageRouter::getPath()[1])) {
                if (isset(PageRouter::getPath()[2])) {
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "View",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "user" => $user,
                            "textview" => true,
                            "domainname" => DomainCache::getDomain(PageRouter::getPath()[0])["_id"],
                            "document" => DomainCache::getDocument(PageRouter::getPath()[0], PageRouter::getPath()[1]),
                            "currentDraft" => DomainCache::getDocument(PageRouter::getPath()[0], PageRouter::getPath()[1])["drafts"][PageRouter::getPath()[2]]
                        ])
                    ]);
                }
                else {
                    echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
                        "title" => "View",
                        "content" => $this->getTemplateEngine()->render($this->getTemplate(), [
                            "user" => $user,
                            "textview" => true,
                            "domainname" => DomainCache::getDomain(PageRouter::getPath()[0])["_id"],
                            "document" => DomainCache::getDocument(PageRouter::getPath()[0], PageRouter::getPath()[1])
                        ])
                    ]);
                }
            } else {
                (new index())->showPage("You must specify a domain and a document.");
            }
        }
    }
    public function hasPermission(){
        return SessionStore::hasSession() && (isset(PageRouter::getPath()[2]) ? Domains::userHasDraftAccess(SessionStore::getCurrentSession(), PageRouter::getPath()[0], PageRouter::getPath()[1], PageRouter::getPath()[2]) : true);
    }
}