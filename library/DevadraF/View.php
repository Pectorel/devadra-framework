<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 21/08/2017
 * Time: 22:53
 */

class View
{


    protected $layout = "layouts/default/layout.php";
    protected $actionScript = "views/index/index.php";
    public $setted = false;
    public $layoutsetted = false;
    public $nolayout = false;
    public $norender = false;


    public function setNoLayout(){
        $this->nolayout = true;
        $this->layout = null;
    }

    public function setNoRender()
    {
        $this->norender = true;
        $this->actionScript = null;
    }

    public function setLayoutScript($layoutScript){
        $this->layoutsetted = true;
        $this->layout = $layoutScript;
    }

    public function setActionScript($actionScript){
        $this->setted = true;
        $this->actionScript = $actionScript;
    }

    public function render($view, $return = false)
    {
        if(!empty($view))
        {
            if($return)
            {
                ob_start();
                require($view);
                return ob_get_clean();
            }
            else{

                if(file_exists($view))
                {
                    include($view);
                }
                else
                {

                    echo "Une erreur est survenue ! " . $view . " est introuvable";

                }

            }
        }


    }

    public function partial($view, $params)
    {
        $renderer = new View();

        foreach ($params as $key => $param)
        {
            $renderer->{$key} = $param;
        }

        $renderer->render($view);
    }

    public function showRender()
    {

        if ($this->layout !== null) {
            $this->render($this->layout);
        }
        elseif ($this->actionScript !== null) {
            $this->render($this->actionScript);
        }

    }

    /**
     *
     * Retourne une url selon les paramètres indiqués
     *
     * @param $urlParams
     * @return string
     */
    public function url($urlParams = array()){

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

        if(!empty($urlParams))
        {

            if(!empty($urlParams["controller"]))
            {
                $url.= $urlParams["controller"] . "/";
            }

            if(!empty($urlParams["action"]))
            {
                $url.= $urlParams["action"];
            }

            if(!empty($urlParams["params"]))
            {

                $i = 0;

                foreach ($urlParams["params"] as $key => $param)
                {

                    if($i == 0) $url.="?";
                    else $url.="&";

                    $url.=$key . "=" . $param;

                    $i++;
                }

            }


        }

        return $url;


    }



    public function getActionScript(){
        return $this->actionScript;
    }


}