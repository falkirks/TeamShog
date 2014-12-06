<?php
namespace water\domains;

use water\api\Readability;

class LegalFinder{
    public static $legalwords = [
        "terms",
        "privacy",
        "legal",
        " tos ", //Don't match "photos"

    ];
    public static function getLegalDomain($domain){
        $file = file_get_contents("http://" . $domain);
        return ($file !== false ? LegalFinder::getLegal($file, "http://" . $domain) : false);
    }
    /*
     * Do NOT call this function to get a URL, use getLegalURL
     */
    public static function getLegal($text, $url = null){
        preg_match_all('`<a [^>]*href="(.*?)">(.*?)</a>`', $text, $matches);
        $final = [];
        foreach($matches[2] as $i => $match){
            foreach(LegalFinder::$legalwords as $word) {
                if (strstr(strtolower($match), $word) !== false){
                    $final[$matches[2][$i]] = $matches[1][$i];
                    break;
                }
            }
        }
        $new = [];
        foreach($final as $name => $path){
            $path = strpos($path, '/') === 0 ? $url . $path : $path; //Handle relative links
            $text = LegalFinder::getTextURL($path);
            if($text === false) continue;
            $new[] = [
                "name" => $name,
                "url" => $path,
                "text" => $text["content"],
                "summarized" => "" //TODO
            ];
        }
        return $new;
    }
    public function getUpdatedDoc($url){
        $text = LegalFinder::getTextURL($url);
        if($text !== false){
            return [
                "text" => $text["content"],
                "summarized" => "" //TODO
            ];
        }
        else{
            return false;
        }
    }
    public static function getTextURL($url){
        $file = file_get_contents($url);
        if($file !== false) {
            return (new Readability(file_get_contents($file)))->getContent();
        }
        else{
            return false;
        }
    }
}