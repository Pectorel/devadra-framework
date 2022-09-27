<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 26/08/2017
 * Time: 21:40
 */

class FlashMessage
{


    public static function addFlassMessage($message)
    {

        if(empty($_SESSION["FlashMessages"]))
        {
            $_SESSION["FlashMessages"] = array();

        }

        array_push($_SESSION["FlashMessages"], $message);
    }

    public static function getFlashMessages()
    {

        return !empty($_SESSION["FlashMessages"]) ? $_SESSION["FlashMessages"] : null;

    }

    public static function flushMessages()
    {
        unset($_SESSION["FlashMessages"]);
    }

}