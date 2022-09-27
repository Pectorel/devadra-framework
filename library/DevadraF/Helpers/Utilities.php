<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 21/08/2018
 * Time: 15:21
 */

class Utilities
{

    public static function is_upper ($in_string)
    {
        return($in_string === strtoupper($in_string) ? true : false);
    }

}