<?php
namespace water\domains;

use Goose\Client;
use water\api\Aylien;
use water\api\Readability;

class LegalFinder{
    public static $legalwords = [
        "terms",
        "privacy",
        "legal",
        " tos ", //Don't match "photos"

    ];
    public static function getLegalDomain($domain){
        $file = @file_get_contents("http://" . $domain);
        return ($file !== false ? LegalFinder::getLegal($file, "http://" . $domain) : false);
    }
    /*
     * Do NOT call this function to get a URL, use getLegalURL
     */
    public static function getLegal($text, $url = null){
        $dom = new \DOMDocument();
        @$dom->loadHTML($text);
        $links = $dom->getElementsByTagName('a');
        $final = [];
        foreach($links as $link){
            $path = $link->attributes->getNamedItem("href")->value;
            foreach(LegalFinder::$legalwords as $word) {
                if (strpos(strtolower($link->textContent), $word) !== false){
                    $path = strpos($path, '/') === 0 ? $url . $path : $path; //Handle relative links
                    $text = LegalFinder::getTextURL($path);
                    if($text === false) continue;
                    $params = array(
                        'text' => $text["content"],
                        'title' => $link->textContent,
                    );
                    $summary = Aylien::call_api('summary', $params);
                    //TODO:Put array into $final
                    $final[] = [
                        "name" => $link->textContent,
                        "url" => $path,
                        "text" => $text,
                        "updated" => time(),
                        "summary" => "", //TODO
                        "active" => true
                    ];
                    break;
                }
            }
        }
        return $final;

    }
    public static function getUpdatedDoc($url){
        $text = LegalFinder::getTextURL($url);
        if($text !== false){
            return [
                "text" => $text,
                "summary" => "", //TODO
                "updated" => time(),
                "active" => true
            ];
        }
        else{
            return false;
        }
    }
    public static function getTextURL($url){
        $html = file_get_contents($url);
        $html = preg_replace("`<a\b[^>]*>(.*?)</a>`", "", $html);
        $html = preg_replace("`<script\b[^>]*>(.*?)</script>`", "", $html);
        $html = preg_replace("`<select\b[^>]*>(.*?)</select>`", "", $html);
        $html = strip_tags($html);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        return $html;
    }
}