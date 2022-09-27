<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 29/08/2018
 * Time: 18:05
 */

class ErrorHandler
{

    public static function setErrorHandler()
    {

        set_error_handler("ErrorHandlerFunction");

    }


}


function ErrorHandlerFunction($errno, $errmsg, $filename, $linenum, $vars)
{

    //echo "test";

    // timestamp for the error entry
    $dt = date("Y-m-d H:i:s (T)");

    $errortype = array (
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
    );

    $err = "<errorentry>\n";
    $err .= "\t<datetime>" . $dt . "</datetime>\n";
    $err .= "\t<errornum>" . $errno . "</errornum>\n";
    $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
    $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
    $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
    $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";


    $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";

    $err .= "</errorentry>\n\n";

    if ($errno == E_ERROR) {
        mail("contact@devadra.com", "Critical User Error", $err);
    }


    header("Location: /Error/maintenance");

}