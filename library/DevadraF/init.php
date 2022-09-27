<?php

if(!isset($_SESSION["lang"]))
{
    $lang = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : "en";

    $languageM = new Language();

    $sql = "SELECT * FROM Language WHERE code = ?";
    $params = array($lang);

    $res = $languageM->select($sql, $params, PDO::FETCH_ASSOC, "fetch");


    if(!empty($res))
    {
        $_SESSION["lang"] = $res["id"];
        $_SESSION["langcode"] = $res["code"];
    }
    else
    {
        $_SESSION["lang"] = 2;
        $_SESSION["langcode"] = "en";
    }
}



function call($controller, $action)
{


    $path = "controllers/". $controller . "Controller.php";



    if(!file_exists($path))
    {


        call('Error', 'error404');
        return;
    }

    $class_name = $controller."Controller";
    new $class_name($action, $controller);



}


//Check Maintenance
if (DevadraConfig::isMaintenance() && !Generale::isDev())
{

    $_GET["controller"] = "Error";
    $_GET["action"] = "maintenance";

}



// Si pas de controller indiqué, on met Index/index par défaut
if(isset($_GET["controller"]))
{

    $controller = $_GET['controller'];

}
else
{
    $controller = 'Index';

}

if (isset($_GET["action"]))
{

    $action = $_GET['action'];
}
else
{
    $action = 'index';
}





/* Système ACL */
Acl::checkAcl();

Credentials::checkAuth();

$allowed = Acl::isAllowed(array("controller" => $controller, "action" => $action));

if($allowed["access"]){

    call($controller, $action);

}
else{
    if($allowed["errorCode"] === 5){
        call('Error', 'error401');
    }
    else{

        call('Error', 'Error404');
    }
}

