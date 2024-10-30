<?php

/**
 * This file is part of googl-php
 *
 * https://github.com/sebi/googl-php
 *
 * googl-php is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CusminGoogl
{
    private $target;
    private $apiKey;

    private static $shortenBuffer = array();
    private static $expandedBuffer = array();


    function __construct($apiKey = null) {

        # Set Google Shortener API target
        $this->target = 'https://www.googleapis.com/urlshortener/v1/url?';

        # Set API key if available
        if ( $apiKey != null ) {
            $this->apiKey = $apiKey;
            $this->target .= 'key='.$apiKey.'&';
        }
    }

    public function shorten($url) {

        if (!empty(self::$shortenBuffer[$url]) ) {
            return self::$shortenBuffer[$url];
        }

        $response = $this->post(array(
            'longUrl' => $url
        ));

        $shorten = $response->id;

        self::$shortenBuffer[$url] = $shorten;

        return $shorten;
    }

    public function expand($url) {

        if (!empty(self::$expandedBuffer[$url]) ) {
            return self::$expandedBuffer[$url];
        }

        $response = $this->get(array(
            'shortUrl' => $url
        ));

        $expanded = $response->longUrl;

        self::$expandedBuffer = $expanded;

        return $expanded;
    }

    private function get($data){
        $response = wp_remote_get($this->target, array(
            'body' => $data
        ));
        $this->errorValidation($response);
        return json_decode($response['body']);
    }

    private function post($data){
        $response =  wp_remote_post($this->target, array(
            'body'=> json_encode($data),
            'method' => 'POST',
            'httpversion' => '1.0',
            'sslverify' => false,
            'headers' => array(
                'Content-Type' => 'application/json' ,
            ),
            'cookies' => array()
        ));

        $this->errorValidation($response);
        return json_decode($response['body']);
    }

    private function errorValidation($response){
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();

            return wp_send_json(['status' => 'error', 'message' => "Something went wrong: $error_message"]);
        }else{
            try{
                $decodedData = json_decode($response['body']);
                if($decodedData->error){
                    $error_message = $decodedData->error->message;
                    try{
                        //Try to get better error message from general Bad Request error
                        foreach((array) $decodedData->error->errors as $er){
                            if($er->reason == 'keyInvalid'){
                                $error_message = 'Invalid API Key';
                                break;
                            }else{
                                $error_message.=', '.$er->reason;
                            }
                        }

                    }catch (\Exception $e){}
                    return wp_send_json(['status' => 'error', 'message' => $error_message]);
                }
            }catch (\Exception $e){
                return wp_send_json(['status' => 'error', 'message' => "Unable to parse response: ".$response['body']]);
            }
        }
    }
}
