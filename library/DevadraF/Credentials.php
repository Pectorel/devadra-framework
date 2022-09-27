<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 31/05/2018
 * Time: 13:39
 */

class Credentials extends Controller
{

    protected $_model = "Admin";
    protected $_loginField = "login";
    protected $_passField = "pass";
    protected $_role;
    private $_login;
    private $_pass;



    public function indexAction()
    {

        $this->view->loginField = $this->_loginField;
        $this->view->passField = $this->_passField;

        $this->view->title = "Connexion";

        $this->view->controller = str_replace("Controller", "", get_class($this));

    }

    public function connectDoAction()
    {
        $this->view->setNoLayout();
        $this->view->setNoRender();

        /**
         * @var Model $modelM
         */
        $modelM = new $this->_model();

        $params = $this->getAllParams();


        if (empty($params[$this->_passField]) || empty($params[$this->_loginField])) {

            //FlashMessage::addFlassMessage("Veuillez indiquer votre identifiant et votre mot de passe");
            FlashMessage::addFlassMessage("Please submit your login and password");
            $this->redirectTo(array("controller" => str_replace("Controller", "", get_class($this)), "action" => "index"));

        }

        $this->_login = $params[$this->_loginField];
        $this->_pass = $params[$this->_passField];


        $sql = "SELECT * FROM {$this->_model} WHERE {$this->_loginField} = ?";




        $user = $modelM->select($sql, array($this->_login), PDO::FETCH_ASSOC, "fetch");



        if ($user)
        {

            $hash = $user[$this->_passField];



            if (password_verify($this->_pass, $hash))
            {

                
                $ssid = null;
                do {
                    $ssid = self::generateSSID();

                    $sql = "SELECT _ssid FROM {$this->_model} WHERE _ssid = ?";
                    $used = $modelM->select($sql, array($ssid));


                } while (!empty($used));

                $cond = "WHERE id = ?";
                $user["_ssid"] = $ssid;

                $executed = $modelM->update($user, $cond, array((int) $user["id"]));

                if($executed)
                {
                    $_SESSION["session_id"] = $ssid;

                    if(!empty($user["role"]))
                    {
                        $_SESSION["role"] = $user["role"];
                    }
                    elseif (!empty($this->role))
                    {
                        $_SESSION["role"] = $this->role;
                    }


                    $this->redirectTo(array("controller" => $this->_model, "action" => "accueil"));

                }
            }

        }

        //FlashMessage::addFlassMessage("Les identifiants indiqués ne correspondent à aucun compte");
        FlashMessage::addFlassMessage("Wrong credentials");
        $this->redirectTo(array("controller" => $this->_model, "action" => "index"));

    }

    public function accueilAction()
    {

        $this->view->title = "Accueil";

    }


    public static function generateSSID($length = 12)
    {

        return bin2hex(openssl_random_pseudo_bytes($length/2));

        /*$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;*/

    }


    /**
     * Check si l'utilisateur est bien autentifié, sinon reset les droits aux droits utilisateurs
     *
     *
     */
    public static function checkAuth()
    {


        $authdata = array(
            "role" => "guest",
            "session_id" => null,
        );

        //var_dump($_SESSION["role"]);


        $role = !empty($_SESSION["role"]) ? $_SESSION["role"] : "guest";

        if($role == "guest")
        {
            $role = !empty($_COOKIE["role"]) ? $_COOKIE["role"] : "guest";
        }


        $acl = "./acl.xml";

        $xml = simplexml_load_file($acl) or die("Error: Cannot create object");



        try{
            if(!empty($xml->roles->{$role}->userTable))
            {

                $table = $xml->roles->{$role}->userTable->__toString();

                $sql = "SELECT id FROM {$table} WHERE _ssid = ?";


                $ssid = !empty($_SESSION["session_id"]) ? $_SESSION["session_id"] : null;

                if(empty($ssid))
                {
                    $ssid = !empty($_COOKIE["session_id"]) ? $_COOKIE["session_id"] : null;
                }

                $params = array($ssid);

                /**
                 * @var Model $modelM
                 */
                $modelM = new $table();

                $res = $modelM->select($sql, $params, PDO::FETCH_ASSOC, "fetch");

                if($res)
                {

                    $authdata = array(
                        "role" => $role,
                        "session_id" => $ssid
                    );
                }

            }
        }
        catch (Exception $e)
        {
            //session_destroy();
        }

        //var_dump($authdata);


        $reload = false;
        //Si pas rôle utilisateur Steam!!!
        /*if($role !== "utilisateur")
        {*/
            foreach ($authdata as $key => $data)
            {

                if(!empty($data))
                {

                    if($_SESSION[$key] != $data)
                    {
                        $reload = true;
                    }

                    $_SESSION[$key] = $data;
                }
                else{
                    unset($_SESSION[$key]);
                }

            }
      /*  }*/


        /*var_dump($_SESSION);
        exit();*/

        // Si session a changé, alors besoin de reload
        if ($reload)
        {

            /*var_dump($_SESSION);
            exit();*/

            //session_destroy();

            $view = new View();

            $url = $view->url(array("controller" => $_GET["controller"], "action" => $_GET["action"]));

            header("Location: " . $url);
            die();


        }

    }

}