
<?php
class PageController extends Controller
{
    
    
    public static $nom_a_afficher = "Page";

    /*
    protected $_colNotSelect = array(
		"_ssid",
		"id",
		"titre",
		"sous-titre",
		"titrehtml",
		"deschtml",
		"_action",
		"_controller",
		"_route",
		"Template_id"
	);
    */

    /*
    protected $_colFilter = array(
		"_ssid",
		"id",
		"titre",
		"sous-titre",
		"titrehtml",
		"deschtml",
		"_action",
		"_controller",
		"_route",
		"Template_id"
	);
    */

    public function indexAction()
    {

        $id = $this->getParam("id");

        if(empty($id))
        {
            $this->redirectTo(array("controller" => "Error", "action" => "error404"));
        }

        $pageM = new Page();

        $page = $pageM->find($id);
        if(empty($page))
        {
            $this->redirectTo(array("controller" => "Error", "action" => "error404"));
        }

        if(!empty($page["_controller"]) && !empty($page["_action"]))
        {

            $this->view->setNoLayout();
            $this->view->setNoRender();

            $controller_name = $page["_controller"] . "Controller";
            new $controller_name($page["_action"], $page["_controller"], $page);

        }


    }
    
}
