<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 12/06/2018
 * Time: 17:18
 */

class Generale
{

    public static function getBase()
    {

        $url = null;

        if(Generale::isLocal())
        {
            $url = "http://";
        }
        else
        {
            $url = "https://";
        }

        $url.=  $_SERVER["SERVER_NAME"] . "/";

        return $url;

    }

    public static function getMainName()
    {
        return "Devadra Framework";
    }

    public static function getCssVersion($type)
    {

        $res = 0;
        $manifest = self::getManifest();

        if(isset($manifest->versioning->css->{$type}))
        {
            $res = $manifest->versioning->css->{$type};
        }

        return $res;
    }

    public static function getJsVersion($type)
    {

        $res = 0;
        $manifest = self::getManifest();

        if(isset($manifest->versioning->js->{$type}))
        {
            $res = $manifest->versioning->js->{$type};
        }

        return $res;
    }


    public static function getManifest($assoc = false)
    {
        $manifest = file_get_contents("manifest.json");
        return json_decode($manifest, $assoc);
    }

    public static function getLang($reset = false)
    {

        if(!isset($_SESSION["lang"]))
        {
            $_SESSION["lang"] = 1;
            $_SESSION["langcode"] = "fr";
        }
        elseif(!isset($_SESSION["langcode"]) || $reset){

            $languageM = new Language();

            $sql = "SELECT * FROM Language WHERE id = ?";
            $params = array($_SESSION["lang"]);

            $res = $languageM->select($sql, $params, PDO::FETCH_ASSOC, "fetch");

            if(!empty($res))
            {

                $_SESSION["langcode"] = $res["code"];

            }
            else{
                $_SESSION["lang"] = 1;
                $_SESSION["langcode"] = "fr";
            }

        }

        return array("lang" => $_SESSION["lang"], "langcode" => $_SESSION["langcode"]);
    }

    public static function getTwitter()
    {
        return "https://twitter.com/";
    }

    public static function getFb()
    {
        return "https://www.facebook.com/";
    }

    public static function getTwitch()
    {
        return "https://www.twitch.tv/";
    }

    public static function getLinkedIn()
    {
        return "https://www.linkedin.com/";
    }

    public static function getDiscord()
    {
        return "https://discord.gg/";
    }

    public static function getRealIpAddr()
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function isDev()
    {

        $res = false;

        $ip = self::getRealIpAddr();

        if ($ip == "176.155.19.247" || $ip == "localhost" || $ip == "127.0.0.1" || $ip == "::1")
        {
           $res = true;
        }

        return $res;
    }

    public static function isLocal()
    {

        $ip = Generale::getRealIpAddr();

        //var_dump($_SERVER["REMOTE_ADDR"]);
        if($ip == "localhost" || $ip == "127.0.0.1" || $ip == "::1")
        {
            return true;
        }

        return false;
    }

    public static function removeEmoji($text) {

        $clean_text = "";

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        // Match Flags
        $regexDingbats = '/[\x{1F1E6}-\x{1F1FF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        // Others
        $regexDingbats = '/[\x{1F910}-\x{1F95E}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $regexDingbats = '/[\x{1F980}-\x{1F991}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $regexDingbats = '/[\x{1F9C0}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $regexDingbats = '/[\x{1F9F9}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }


    public static function initJsonRes()
    {

        return array(
            "ErrCode" => 4,
            "ErrMess" => "Error",
            "success" => false
        );

    }

    /**
     * @param $res array
     */
    public static function setJsonRes($res){
        header('Content-Type: application/json');
        echo json_encode($res);
    }

    public static function checkCaptcha($secret, $token, $min_score = 0.5)
    {

        $res = false;

        $posts = array(
            "secret" => $secret,
            "response" => $token
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $captcha = curl_exec($ch);
        curl_close ($ch);

        if($captcha != false)
        {
            $captcha = json_decode($captcha);

            if($captcha->success && $captcha->score >= $min_score)
            {
                $res = true;
            }

        }

        return $res;
    }

}