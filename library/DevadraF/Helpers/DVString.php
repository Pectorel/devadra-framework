<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 05/02/2019
 * Time: 17:39
 */

class DVString
{

    public static function truncate($string, $max_char)
    {
        $cut_text = $string;

        if(strlen($string) > $max_char)
        {

            $cut_text = substr($string, 0, $max_char);
            $cut_text = explode(' ', $cut_text);
            array_pop($cut_text); // remove last word from array
            $cut_text = implode(' ', $cut_text);
            $cut_text.=" (...)";

        }


        return $cut_text;

    }

}