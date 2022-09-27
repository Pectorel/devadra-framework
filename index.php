<?php


    ob_start();
    session_start();



//var_dump($_SESSION);
    //var_dump(session_id());


?>


<?php



    // Autoloader des classes PHP
    spl_autoload_register(function ($class)
    {
        $dirs = array(
            "library/DevadraF/",
            "library/DevadraF/Helpers",
            "controllers/",
            "models/"
        );

        foreach ($dirs as $dir)
        {
            $full_dir = $dir . "/" . $class . ".php";
            if(file_exists($full_dir))
            {
                require($full_dir);
            }

        }

    });

    if (!Generale::isLocal())
    {
        error_reporting(-1);
        ini_set('display_errors', 'On');

        ErrorHandler::setErrorHandler();
    }

    require_once "library/DevadraF/init.php";


?>
