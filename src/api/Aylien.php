<?php

namespace water\api;


class Aylien {
    public static function call_api($endpoint, $parameters) {
        $ch = curl_init('https://api.aylien.com/api/v1/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'X-AYLIEN-TextAPI-Application-Key: 19e902529384e66eb84a80695f59c2cf',
            'X-AYLIEN-TextAPI-Application-ID: 7dce084d'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        $response = curl_exec($ch);
        if($response === false){
            return curl_error($ch);
        }else {
            return json_decode($response, true);
        }
    }
} 