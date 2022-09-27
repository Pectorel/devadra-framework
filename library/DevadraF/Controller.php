<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 21/08/2017
 * Time: 21:28
 */

class Controller
{


    public $view = null;
    private $_path = null;
    private $_defaultpath = null;
    protected $_layout = null;
    protected $_controller = null;

    protected $_images = array();


    public static $nom_a_afficher;

    protected $_colName = array();

    protected $_colNotSelect = array(
        "id",
        "_ssid"
    );

    protected $_colFilter = array();

    private $_flushs = array(
        "path",
        "view",
        "flushs"
    );


    function __call($name, $arguments)
    {

        $this->redirectTo(array("controller" => "Error", "action" => "error404"));
    }

    function __construct($action, $controller, $args = null)
    {

        $this->view = new View();

        $this->_controller = $controller;

        if(!empty($args))
        {
            $this->{ $action."Action" }($args);
        }
        else
        {
            $this->{ $action."Action" }();
        }

        $this->_path = "views/". strtolower($controller) . "/" . $action . ".php";

        //echo $this->_path;

        $this->_defaultpath = "library/DevadraF/Views/" . $action . ".php";


        if(!file_exists($this->_path) && !$this->view->setted && !empty($this->view->getActionScript()))
        {
            if(file_exists($this->_defaultpath))
            {
                $this->_path = $this->_defaultpath;
            }
            else{

                $this->redirectTo(array("controller" => "Error", "action" => "error404"));
                return;
            }

        }

        if(!empty($this->view->getActionScript()) && !$this->view->setted && !$this->view->norender)
        {
            $this->view->setActionScript($this->_path);
        }



        if(!empty($this->_layout) && !$this->view->layoutsetted && !$this->view->nolayout)
        {
            $this->view->setLayoutScript($this->_layout);
        }

        $this->view->showRender();

    }

    /**
     *
     * Methode par défaut pour les pages admin des Models
     *
     */
    public function adminAction()
    {

        $adminlayout = "layouts/admin/layout.php";
        $this->view->setLayoutScript($adminlayout);

        $modelM = new $this->_controller();


        $paginator = new Paginator("AdminPaginator", $modelM, array("itemPerPage" => 15));
        $this->view->paginator = $paginator;
        $this->view->results = $paginator->getRes();

        $this->view->controller = $this->_controller;
        $this->view->asideactive = true;
        $this->view->colFilter = $this->_colFilter;
        $this->view->colNames = $this->_colName;
        $this->view->model = $modelM;


    }

    /**
     *
     * Action qui ajoute un élément à la table
     *
     */
    public function addAction()
    {

        $adminlayout = "layouts/admin/layout.php";

        $this->view->setLayoutScript($adminlayout);

        $path = "views/" . $this->_controller . "/form.php";


        if(!file_exists($path))
        {
            $path = "library/DevadraF/Views/form.php";
        }

        $this->view->setActionScript($path);

        /**
         * @var Model $modelM
         */
        $modelM = new $this->_controller();

        $columns = $modelM->getColumns();



        $this->view->imagesArray = $this->_images;
        $this->view->columns = $columns;
        $this->view->asideactive = true;
        $this->view->colNames = $this->_colName;
        $this->view->exceptions = $this->_colNotSelect;
        $this->view->model = $modelM;

    }

    function addDoAction()
    {

        $this->view->setNoLayout();
        $this->view->setNoRender();

        $params = $this->getAllParams();

        $this->_careDepentInsert_Modify($params);

    }

    public function modifyAction()
    {

        $adminlayout = "layouts/admin/layout.php";

        $this->view->setLayoutScript($adminlayout);
        $path = "views/" . $this->_controller . "/form.php";

        if(!file_exists($path))
        {
            $path = "library/DevadraF/Views/form.php";
        }

        $this->view->setActionScript($path);

        $modelM = new $this->_controller();

        $columns = $modelM->getColumns();

        $this->view->columns = $columns;

        $id = $this->getParam("id");

        if(!$id)
        {
            FlashMessage::addFlassMessage("Aucun id renseigné, aucun(e) " . $this->_controller . " ne peut être modifié");
            //FlashMessage::addFlassMessage("No id provided, " . $this->_controller . " won't be modified");
            $this->redirectTo(array("controller" => $this->_controller, "action" => "admin"));
        }

        $user = $modelM->find((int) $id);

        $this->view->user = $user;

        //var_dump($user);
        $this->view->imagesArray = $this->_images;
        $this->view->asideactive = true;
        $this->view->exceptions = $this->_colNotSelect;
        $this->view->colNames = $this->_colName;
        $this->view->model = $modelM;

    }

    public function modifyDoAction()
    {

        $this->view->setNoLayout();
        $this->view->setNoRender();

        $params = $this->getAllParams();



        unset($params["id"]);

        //var_dump($all_params);
        //exit();



        $this->_careDepentInsert_Modify($params);



    }


    /**
     *
     * Action qui remove un élément de la table
     *
     */
    public function removeDoAction()
    {

        $this->view->setNoLayout();
        $this->view->setNoRender();

        $params = $this->getAllParams();
        $id = $this->getParam("id");

        if(empty($id))
        {

            FlashMessage::addFlassMessage("Aucun id renseigné, aucun(e) " . $this->_controller . " n'a été supprimé");
            //FlashMessage::addFlassMessage("No id provided, " . $this->_controller . " not deleted");
            $this->redirectTo(array("controller" => $this->_controller, "action" => "admin"));

        }

        $modelM = new $this->_controller();

        $sql = "WHERE id = ?";
        $executed = $modelM->delete($sql, array($id));

        if($executed)
        {
            FlashMessage::addFlassMessage("L'objet a bien été supprimé");
        }
        else{
            FlashMessage::addFlassMessage("Une erreur est survenue lors de la suppresion de l'objet. Veuillez réessayer");
        }

        $this->redirectTo(array("controller" => $this->_controller, "action" => "admin"));

    }

    public function addAjaxAction()
    {

        $this->view->setNoLayout();

        $path = "views/" . $this->_controller . "/form_ajax.php";

        if(!file_exists($path))
        {
            $path = "library/DevadraF/Views/form.php";
        }

        $this->view->setActionScript($path);


        $modelM = new $this->_controller();

        $columns = $modelM->getColumns();

        $this->view->imagesArray = $this->_images;
        $this->view->columns = $columns;
        $this->view->asideactive = true;
        $this->view->colNames = $this->_colName;
        $this->view->exceptions = $this->_colNotSelect;
        $this->view->model = $modelM;

        $this->view->urlform = $this->view->url(array("controller" => $this->_controller, "action" => "addAjaxDo"));

    }

    public function addAjaxDoAction()
    {

        $res = array(
            "ErrCode" => "1",
            "ErrMess" => "une erreur inconnue est survenue",
            "success" => false
        );


        $this->view->setNoLayout();
        $this->view->setNoRender();

        $params = $this->getAllParams();

        $_SESSION["temp_inputs"] = $params;

        /**
         * @var $modelM Model
         */
        $modelM = new $this->_controller();

        $executed = $modelM->insert($params);

        if($executed)
        {
            unset($_SESSION["temp_inputs"]);

            $last_id = $modelM->getLastIdInserted();

            $res = array(
                "ErrCode" => "0",
                "ErrMess" => "L'élément a bien été crée",
                "success" => true,
                "id" => $last_id
            );

        }

        Generale::setJsonRes($res);

    }


    public function modifyAjaxAction()
    {

        $this->view->setNoLayout();

        $path = "views/" . $this->_controller . "/form.php";

        if(!file_exists($path))
        {
            $path = "library/DevadraF/Views/form.php";
        }

        $this->view->setActionScript($path);

        $modelM = new $this->_controller();

        $columns = $modelM->getColumns();

        $this->view->columns = $columns;

        $id = $this->getParam("id");

        $this->view->imagesArray = $this->_images;
        $controllername = $this->_controller."Controller";
        $controllername = $controllername::$nom_a_afficher;

        if(!$id)
        {
            FlashMessage::addFlassMessage("Aucun id renseigné, aucun(e) " . $controllername . " ne peut être modifié");
            $this->redirectTo(array("controller" => $this->_controller, "action" => "admin"));
        }

        $user = $modelM->find((int) $id);

        $this->view->user = $user;

        //var_dump($user);

        $this->view->asideactive = true;
        $this->view->colNames = $this->_colName;
        $this->view->exceptions = $this->_colNotSelect;
        $this->view->model = $modelM;

    }

    public function modifyAjaxDoAction()
    {
        $this->view->setNoLayout();
        $this->view->setNoRender();

        $id = $this->getParam("id");

        $params = $this->getAllParams();
        unset($params["id"]);

        //var_dump($params);

        $modelM = new $this->_controller();

        $sql = "WHERE id = ?";
        $condparams = array($id);
        $executed = $modelM->update($params, $sql, $condparams);

        if($executed)
        {
            FlashMessage::addFlassMessage("L'objet a bien été modifié");


        }
        else{
            FlashMessage::addFlassMessage("Une erreur est survenue lors de la modification. Veuillez réessayer");

        }


    }

    public function removeAjaxDoAction()
    {

        $res = array(
            "ErrCode" => "1",
            "ErrMess" => "une erreur inconnue est survenue",
            "success" => false
        );

        $this->view->setNoLayout();
        $this->view->setNoRender();

        $params = $this->getAllParams();
        $id = $this->getParam("id");


        $controllername = $this->_controller."Controller";
        $controllername = $controllername::$nom_a_afficher;

        if(empty($id))
        {

            $res = array(
                "ErrCode" => "2",
                "ErrMess" => "Aucun id renseigné, aucun(e) " . $controllername . " n'a été supprimé",
                "success" => false
            );

        }
        else
        {

            $modelM = new $this->_controller();

            $sql = "WHERE id = ?";
            $executed = $modelM->delete($sql, array($id));

            if($executed)
            {

                $res = array(
                    "ErrCode" => "0",
                    "ErrMess" => "Un(e) " . $controllername  ." a bien été supprimé",
                    "success" => true,
                    "id" => $id
                );
            }

        }

        Generale::setJsonRes($res);

    }

    public function getJsonAction()
    {

        $this->view->setNoLayout();
        $this->view->setNoRender();

        $modelM = new $this->_controller();
        $results = $modelM->fetchAll();

        if(!empty($results))
        {

            $res = array(
                "ErrCode" => "0",
                "ErrMess" => "Requête ok",
                "success" => true,
                "data" => $results
            );

        }
        else
        {

            $res = array(
                "ErrCode" => "2",
                "ErrMess" => "Aucun résultat trouvé",
                "success" => false
            );

        }


        Generale::setJsonRes($res);

    }


    /**
     *
     * Fonction qui permet de rediriger un utilisateur sur une page selon l'action et le controller passé
     *
     *
     * @param {array} $params
     */
    protected function redirectTo($params)
    {

        if(!empty($params["controller"]) && !empty($params["action"]))
        {

            $redirect_url = $this->view->url($params);
            header("Location: " . $redirect_url);
            die();

        }
        else{
            $this->redirectTo(array("controller" => "Index", "action" => "index"));
        }


    }


    protected function getParam($index)
    {

        $res = null;

        if(!empty($index))
        {

            if(!empty($_GET[$index])){
                $res = $_GET[$index];
            }
            elseif(!empty($_POST[$index])){
                $res = $_POST[$index];
            }
            elseif(!empty($_FILES[$index])){
                $res = $_FILES[$index];
            }

        }

        return $res;
    }

    protected function getAllParams()
    {
        $res = array();

        foreach($_GET as $key => $value)
        {
            $res[$key] = $value;
        }

        foreach ($_POST as $key => $value)
        {
            $res[$key] = $value;
        }

        return $res;

    }

    protected function getFiles()
    {

        $res = array();

        foreach ($_FILES as $key => $value)
        {

            $res[$key] = $value;

        }

        return $res;

    }


    protected function _careDepentInsert_Modify($all_params)
    {

        $filess = $this->getFiles();
        //var_dump($filess);


        foreach ($filess as $classename => $classe)
        {

            //var_dump($classename);

            foreach ($classe["tmp_name"] as $index => $files)
            {

                //Si image controller Principal
                if($index === "images")
                {
                    //var_dump($index);
                    $filearray = array();

                    foreach ($classe as $key => $array)
                    {

                        for($i=0; $i < count($classe["name"]["images"]); $i++)
                        {
                            $filearray[$i][$key] = $classe[$key]["images"][$i];
                        }

                    }

                    //var_dump($filearray);


                    $all_params[$this->_controller]["images"] = $filearray;
                }
                //Sinon si careDepent
                //TODO : Check if multiple image added on care depent Works
                else
                {
                    foreach ($classe["tmp_name"] as $key2 => $rows)
                    {
                        $filearray = array();
                        foreach ($classe as $key => $array)
                        {
                            foreach ($classe[$key][$key2]["images"] as $key3 => $image)
                            {
                                $filearray[$key] = $classe[$key][$key2]["images"][$key3];
                            }

                        }

                        $all_params[$classename][$key2]["images"][] = $filearray;

                    }
                }

            }

        }


        $params = $all_params[$this->_controller];

        $id = null;

        //var_dump($params);

        /**
         * @var $modelM Model
         *
         */
        $modelM = new $this->_controller();

        $modify = false;
        if(!empty($params["id"]))
        {

            $res = $modelM->find($params["id"]);

            if(!empty($res))
            {
                $modify = true;
            }

        }


        if($modify)
        {
            $sql = "WHERE id = ?";
            $condparams = array($params["id"]);
            $id = $params["id"];

            $executed = $modelM->update($params, $sql, $condparams);

            if($executed)
            {
                FlashMessage::addFlassMessage("L'objet a bien été modifié");
                //FlashMessage::addFlassMessage("Element has been modified");
            }
            else{
                FlashMessage::addFlassMessage("Une erreur est survenue lors de la modification. Veuillez réessayer");
                //FlashMessage::addFlassMessage("An error occured during modification, try again");
            }

        }
        else
        {
            $executed = $modelM->insert($params);
            $id = $modelM->getLastIdInserted();


            if($executed)
            {
                FlashMessage::addFlassMessage("L'objet a bien été crée");
                //FlashMessage::addFlassMessage("Element has been created");
            }
            else{
                FlashMessage::addFlassMessage("Une erreur est survenue lors de la création. Veuillez réessayer");
                //FlashMessage::addFlassMessage("An error occured during modification, try again");
            }
        }



        if(!empty($all_params[$this->_controller]["images"]))
        {
            $this->_insertImage($all_params[$this->_controller]["images"], $id, $this->_controller);
        }

        unset($all_params[$this->_controller]);
        unset($all_params["action"]);
        unset($all_params["controller"]);

        /*var_dump($all_params);
        exit();
*/

        foreach ($all_params as $model => $rows)
        {



            $modelM = new $model();



            $foreignId = null;
            $i = 0;
            foreach ($rows as $row) {

                $modify = false;

                $row[$this->_controller."_id"] = $id;



                if(!empty($row["id"]))
                {

                    $res = $modelM->find($row["id"]);

                    if(!empty($res))
                    {
                        $modify = true;
                    }

                }

                if($modify)
                {
                    $sql = "WHERE id = ?";
                    $id_child = $row["id"];
                    $foreignId = $row["id"];
                    unset($row["id"]);
                    $modelM->update($row, $sql, array($id_child));
                }
                else
                {


                    $modelM->insert($row);
                    $foreignId = $modelM->getLastIdInserted();

                }

                if(!empty($all_params[$model][$i]["images"]))
                {
                    $this->_insertImage($all_params[$model][$i]["images"], $foreignId, $model);
                }


                $i++;
            }

        }

    }

    private function _insertImage($images, $id, $controller)
    {

        $controller = ucfirst($controller);

        $path = "./Public/Images/" . $controller . "/" . $id;



        if(!file_exists($path))
        {

            mkdir($path, 0755, true);
            // Si on change pas les propriétaires sur Linux alors pas les droits nécessaires pour mettre les fichiers
            chown("./Public/Images/" . $controller . "/", "www-data");
            chgrp("./Public/Images/" . $controller . "/", "www-data");
            chown($path, "www-data");
            chgrp($path, "www-data");
        }





        $i = 0;

        foreach ($images as $image)
        {
            //var_dump($image);
            //exit();
            $options = $this->_images[$i];
            $moved_path = $path . "/" . $i . "." . $options["type"];
            $moved = false;

            if($options["adjust"] == "none")
            {
                $moved = move_uploaded_file($image["tmp_name"], $moved_path);
            }
            else
            {
                $imgRetouch = new ImageRetouch($image["tmp_name"]);

                $resized_img = $imgRetouch->resize($options["adjust"], $options);

                $function = "image" . $this->_images[$i]["type"];


                //$test = null;
                if($options["type"] == "png")
                {
                    imagealphablending($resized_img, false);
                    imagesavealpha($resized_img, true);
                }
                else if ($options["type"] == "jpg")
                {
                    $function = "imagejpeg";
                }

                $function($resized_img, $moved_path);

                $imgRetouch->setSrc($moved_path);
                $img = null;
                if(Generale::isLocal())
                {
                    $img = $resized_img;
                }
                else
                {
                    $img = $imgRetouch->compress();
                }

                $moved = $function($img, $moved_path);
            }




            // TODO : Centralize manifest modifications
            $json = Generale::getManifest(true);
            if(empty($json["Images"]))
            {
                $json["Images"] = array();
            }
            if (empty($json["Images"][$controller]))
            {
                $json["Images"][$controller] = array();
            }
            if(empty($json["Images"][$controller][$id]))
            {
                $json["Images"][$controller][$id] = array();
            }
            if(empty($json["Images"][$controller][$id][$i]))
            {
                $json["Images"][$controller][$id][$i] = 0;
            }
            $json["Images"][$controller][$id][$i]+=1;
            $content_encode = json_encode($json, JSON_PRETTY_PRINT);
            file_put_contents("./manifest.json", $content_encode);
            // Fin du to do

            if(!$moved)
            {
                FlashMessage::addFlassMessage("Erreur lors de l'upload de l'image");
                //FlashMessage::addFlassMessage("Error during img upload");
                return;
            }

            $i++;

        }

    }


    public function removeImageAjaxDoAction()
    {

        $this->view->setNoLayout();
        $this->view->setNoRender();

        $res = array(
            "ErrCode" => "4",
            "ErrMess" => "Pas assez d'arguments",
            "success" => false
        );

        $id = $this->getParam("id");
        $index = $_POST["index"];




        if(!empty($id) && (!empty($index) || $index == '0'))
        {




            $rem = $this->_removeImage($this->_controller, $id, $index);

            if($rem)
            {

                $res = array(
                    "ErrCode" => "0",
                    "ErrMess" => "Modification bien effectuée",
                    "success" => true,
                    "id" => $id,
                    "index" => $index,
                    "controller" => $this->_controller,
                    "type" => $this->_images[$index]["type"]
                );

            }
            else
            {
                $res = array(
                    "ErrCode" => "1",
                    "ErrMess" => "Modification echouée",
                    "success" => false
                );
            }

        }

        Generale::setJsonRes($res);

    }

    private function _removeImage($controller, $id, $index)
    {

        //var_dump($index);

        $path = "./Public/Images/" . $controller . "/" . $id . "/" . $index . "." . $this->_images[$index]["type"];



        $rem = unlink($path);

        return $rem;

    }


    /**
     * Getter du ColNotSelect
     *
     * @return array
     */
    public function getColNotSelect()
    {
        return $this->_colNotSelect;
    }

    public function getImagesArray()
    {

        return $this->_images;

    }


    protected function _isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }


    /**
     *
     * Fonction qui permet de supprimer les variables 'sensibles' de l'objet
     *
     * @deprecated
     *
     */
    private function flushProperties()
    {
        if (!empty($this->_flushs)) {
            foreach ($this->_flushs as $flush) {
                unset($this->{$flush});
            }
        }
    }

}

