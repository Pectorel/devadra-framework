<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 29/05/2018
 * Time: 19:24
 */


$acl = simplexml_load_file("./acl.xml") or die("Error: Cannot create object");


foreach ($_POST as $role => $controllers)
{

    // Si role inexistant, on l'ajoute
    if(!isset($acl->roles->{$role}))
    {
        $acl->roles->addChild($role);
    }

    $target = $acl->roles->{$role};
    $init_target = $target;


    foreach ($controllers as $controller => $actions)
    {

        if (!isset($init_target->{$controller}))
        {

            $init_target->addChild($controller);


        }

        $target = $init_target->{$controller};

        if(!isset($target->functions))
        {
            $target->addChild("functions");
        }

        $target = $target->functions;


        foreach ($actions as $action => $right)
        {


            if (!isset($target->{$action}))
            {

                $target->addChild($action)->addChild("right", "allow");



            }



        }

    }


}

$dom = new DOMDocument();
$dom->loadXML($acl->asXML());
$dom->formatOutput = true;
$formattedXML = $dom->saveXML();

$fp = fopen("./acl.xml",'w+');
fwrite($fp, $formattedXML);
fclose($fp);
