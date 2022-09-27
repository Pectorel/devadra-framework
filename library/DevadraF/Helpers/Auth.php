<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 26/08/2017
 * Time: 22:47
 */

class Auth
{

    private $login;
    private $pass;
    private $table;
    private $loginField;
    private $passField;
    private $model;
    private $instance;


    public function __construct($options)
    {

        $this->instance = PDO_DB::getInstance();
        if(!empty($options))
        {

            $this->login = $options["login"];
            $this->pass = $options["pass"];
            $this->table = $options["table"];
            $this->loginField = $options["loginField"];
            $this->passField = $options["passField"];
            $this->model = $options["model"];

        }

    }

    public static function destroy()
    {

        unset($_SESSION["session_id"]);
        unset($_SESSION["role"]);

    }


    public function checkCredentials()
    {

        $res = false;

        if(!empty($this->instance))
        {

            $sql = "SELECT * FROM {$this->table} WHERE {$this->loginField} = ?";


            $stmt = $this->instance->prepare($sql);
            $stmt->execute(array($this->login));

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!empty($user)){

                if(!empty($user[$this->passField]))
                {

                    //var_dump($user);

                    $hash = $user[$this->passField];

                    echo $hash;

                    if(password_verify($this->pass, $hash))
                    {

                        $ssid = Auth::generateSSID();

                        do{

                            $sql = "SELECT _ssid FROM {$this->table} WHERE _ssid = ?";
                            $stmt = $this->instance->prepare($sql);
                            $stmt->execute(array($ssid));
                            $used = $stmt->fetch();


                            $usdeadmin = null;
                            // On check si le _ssid n'est pas utilisé dans admin pour éviter les confusions
                            if($this->table !== "Admin")
                            {
                                $sql = "SELECT _ssid FROM Admin WHERE _ssid = ?";
                                $stmt = $this->instance->prepare($sql);
                                $stmt->execute(array($ssid));
                                $usedadmin = $stmt->fetch();

                            }


                        }while(!empty($used) && !empty($usedadmin));


                        $modelM = new $this->model();

                        $cond = "WHERE id = ?";

                        $user["_ssid"] = $ssid;

                        $executed = $modelM->update($user, $cond, array((int) $user["id"]));

                        //var_dump($executed);

                        if($executed)
                        {
                            $_SESSION["session_id"] = $ssid;

                            $res = true;
                        }

                    }
                }
            }
        }

        return $res;
    }



    /**
     * Fonction qui check si un utilisateur est bien authentifié avec un rôle en particulier
     *
     * @param $options
     * @return bool
     * @deprecated
     */
    public static function checkAuth($options)
    {
        $res = false;

        if(!empty($options))
        {

            if(!empty($options["role"]))
            {

                $role = $options["role"];

                if(!Acl::hasRole($role))
                {
                    return false;
                }
            }

            $table = $options["table"];

            $cond = $options["cond"];

            $condparams = array(Auth::getSSID());

            $condparams = array_merge($condparams, $options["condparams"]);

            $ssid = Auth::getSSID();

            if(!empty($ssid))
            {

                $sql = "SELECT * FROM {$table} " . $cond;
                $instance = PDO_DB::getInstance();

                if(!empty($instance))
                {
                    $stmt = $instance->prepare($sql);
                    $stmt->execute($condparams);

                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if(!empty($user))
                    {
                        $res = true;
                    }
                }

            }

        }

        return $res;
    }

    /**
     * Retourne le ssid d'un utilisateur
     *
     * @return mixed
     */
    private static function getSSID()
    {
        return !empty($_SESSION["session_id"]) ? $_SESSION["session_id"] : null;
    }

}