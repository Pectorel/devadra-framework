<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 21/08/2017
 * Time: 22:05
 */

class ErrorController extends Controller
{

    public function error404Action()
    {

    }

    public function error401Action()
    {

    }

    public function maintenanceAction()
    {

        $this->view->setNoLayout();

    }


}