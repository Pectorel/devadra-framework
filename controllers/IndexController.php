<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 19/08/2017
 * Time: 20:54
 */

class IndexController extends Controller
{

    public function setLangAction()
    {

        $lang = $this->getParam("lang");


        if(empty($lang))
        {
            $this->redirectTo(array("controller" => "Index", "action" => "index"));
        }


        $_SESSION["lang"] = $lang;
        // On met le langcode à jour grâce à cette fonction
        Generale::getLang(true);

        /*var_dump($_SERVER['HTTP_REFERER']);
        exit();*/

        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();

        //$this->redirectTo(array("controller" => "Index", "action" => "index"));



    }

    public function indexAction()
    {

        $this->view->title = "";

    }


}