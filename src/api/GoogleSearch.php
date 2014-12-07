<?php
namespace water\api;

class GoogleSearch{
    const API_URL = "https://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=";
    public static function searchData($query){
        $url = GoogleSearch::API_URL . urlencode($query) . "&userip=" . $_SERVER["REMOTE_ADDR"];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, "http://falkirks.koding.io");
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body, true);
    }
    public static function getTopResultDomain($query){
        $data = GoogleSearch::searchData($query);
        var_dump($data);
        if(!empty($data["results"])){
            return parse_url($data["results"][0]["unescapedUrl"])["host"];
        }
        return false;
    }
}