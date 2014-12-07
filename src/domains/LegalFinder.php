<?php
namespace water\domains;

use water\api\Aylien;

class LegalFinder{
    public static $legalwords = [
        "terms",
        "privacy",
        "policy",
        "conditions",
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
            if($link instanceof \DOMNode) {
                $path = $link->attributes->getNamedItem("href")->value;
                foreach (LegalFinder::$legalwords as $word) {
                    if (strpos(strtolower($link->textContent), $word) !== false) {
                        $path = strpos($path, '/') === 0 ? $url . $path : $path; //Handle relative links
                        $text = LegalFinder::getTextURL($path);
                        if ($text === false) continue;
                        $params = array(
                            'text' => $text,
                            'title' => $link->textContent,
                        );
                        $summary = Aylien::call_api('summarize', $params);
                        //TODO:Put array into $final
                        $finalsummary = implode(" ", $summary->sentences);
                        $final[] = [
                            "name" => $link->textContent,
                            "url" => $path,
                            "text" => $text,
                            "updated" => time(),
                            "summary" => $finalsummary, //Hopefully works
                            "active" => true,
                            "words" => LegalFinder::getWordFrequency($text)
                        ];
                        break;
                    }
                }
            }
        }
        return $final;

    }
    public static function getUpdatedDoc($url){
        $text = LegalFinder::getTextURL($url);
        $params = array(
            'text' => $text
        );
        $summary = Aylien::call_api('summarize', $params);
        $finalsummary = utf8_decode(implode(" ",$summary->sentences));
        if($text !== false){
            return [
                "text" => $text,
                "summary" => $finalsummary, //Hopefully works
                "updated" => time(),
                "active" => true,
                "words" => LegalFinder::getWordFrequency($text)
            ];
        }
        else{
            return false;
        }
    }
    public static function getTextURL($url){
        $params = array(
            'url' => $url,
        );
        $text = Aylien::call_api('extract', $params);
        if(!($text->article === NULL)){
            $finaltext = str_replace("\n", "", $text->article);
            $finaltext = utf8_decode($finaltext);
            return $finaltext;
        }else{
            $html = file_get_contents($url);
            if(($pos = strpos($html, "</head>")) !== false){
                $html = substr($html, $pos+7);
            }
            $html = preg_replace("`<a\b[^>]*>(.*?)</a>`", "", $html);
            $html = preg_replace("`<script\b[^>]*>(.*?)</script>`", "", $html);
            $html = preg_replace("`<select\b[^>]*>(.*?)</select>`", "", $html);
            $html = strip_tags($html);
            $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
            $html = utf8_decode($html);
            return $html;
        }
    }
    public static function getWordFrequency($string){
        $n_words = preg_match_all('/([a-zA-Z]|\xC3[\x80-\x96\x98-\xB6\xB8-\xBF]|\xC5[\x92\x93\xA0\xA1\xB8\xBD\xBE]){4,}/', $string, $match_arr);
        $arr = $match_arr[0];
        $ret =  [];
        foreach($arr as $word){
            if(isset($ret[$word])){
                $ret[$word]++;
            }
            else{
                $ret[$word] = 1;
            }
        }
        return $ret;
    }
}
