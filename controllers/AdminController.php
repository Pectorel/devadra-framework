<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 27/08/2017
 * Time: 00:45
 */

class AdminController extends Credentials
{

    protected $_layout = "layouts/admin/layout.php";

    public function indexAction()
    {


        if(Acl::hasRole(array("admin", "superadmin", "traducteur")))
        {

            $this->redirectTo(array("controller" => "Admin", "action" => "accueil"));

        }

        $this->view->hideAsideMenu = true;
        parent::indexAction();
    }


    public function accueilAction()
    {


        $dashboardMenus = array(
            "section" => array(
                "card1" => array(
                    "titre" => "Card 1",
                    "lien" => $this->view->url(array("controller" => "Admin", "action" => "accueil")),
                    "class" => "dash_card"
                ),
                "card2" => array(
                    "titre" => "Card 2",
                    "lien" => $this->view->url(array("controller" => "Admin", "action" => "accueil")),
                    "class" => "dash_card"
                ),
                "card3" => array(
                    "titre" => "Card 3",
                    "lien" => $this->view->url(array("controller" => "Admin", "action" => "accueil")),
                    "class" => "dash_card"
                ),
                "card4" => array(
                    "titre" => "Card 4",
                    "lien" => $this->view->url(array("controller" => "Admin", "action" => "accueil")),
                    "class" => "dash_card"
                )
            )
        );

        $this->view->dashs = $dashboardMenus;

        $this->view->title = "Accueil";

    }


    public function disconnectDoAction()
    {

        $this->view->setNoLayout();
        $this->view->setNoRender();
        Auth::destroy();
        FlashMessage::addFlassMessage("Vous avez bien été deconnecté");
        //FlashMessage::addFlassMessage("You have successfully been disconnected");
        $this->redirectTo(array("controller" => "Admin", "action" => "index"));


    }

}