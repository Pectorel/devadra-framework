<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 26/08/2017
 * Time: 01:02
 */

class Acl
{

    private static $acl = "acl.xml";

    /**
     * Fonction qui check si un utilisateur a bien un role attribué
     *
     */
    public static function checkAcl(){

        if(empty(self::getRole()))
        {

            $_SESSION["role"] = "guest";
        }

    }

    /**
     * @param $role
     * @return bool
     */
    public static function hasRole($role)
    {

        if(is_array($role))
        {

            foreach ($role as $rol)
            {

                if(self::getRole() === strtolower($rol))
                {
                    return true;
                }

            }

        }
        elseif(is_string($role)){
            if(self::getRole() === strtolower($role))
            {
                return true;
            }
        }


        return false;
    }

    /**
     * Check si un utilisateur a les droits sur une methode en fonction de son role
     *
     * @param $params
     * @return array
     */
    public static function isAllowed($params)
    {



        $res = array(
            "errorCode" => "0",
            "errorMess" => "No parameters bound",
            "access" => false
        );

        if(!empty($params["controller"]) && !empty($params["action"]))
        {

            $xml = self::getXML();

            //var_dump($xml);

            if(!empty($xml))
            {
                $role = self::getRole();

                //var_dump($role);

                $roleaccess = self::getInheritance($role);




                /*$roleaccess = array( $xml->roles->guest);

                if($role !== "guest")
                {
                    array_push($roleaccess, $xml->roles->{ $role });
                }*/


                if(!empty($roleaccess))
                {

                    foreach ($roleaccess as $roleacces)
                    {

                        $controller = $roleacces->{ $params["controller"] };

                        if(!empty($controller))
                        {

                            $action = $controller->functions->{ $params["action"] };



                            $res["errorCode"] = 5;
                            $res["errorMess"] = "Action " . $params["action"] . " is not allowed for this role " . $role;

                            if(!empty($action))
                            {

                                $right = $action->right;
                                //var_dump($roleaccess);
                                if($right == "allow")
                                {
                                    $res = array(
                                        "errorCode" => "4",
                                        "errorMess" => "no Error",
                                        "access" => true
                                    );
                                    break;
                                }
                            }


                        }
                        else{

                            $res["errorCode"] = 3;
                            $res["errorMess"] = "Controller " . $params["controller"] . " doesn't exist in acl for this role " . $role;

                        }

                    }

                }
                else{

                    $res["errorCode"] = 2;
                    $res["errorMess"] = "Role " . $role . " does not exist in Acl";

                }
            }
            else{

                $res["errorCode"] = 1;
                $res["errorMess"] = "Missing acl.xml";

            }
        }
        return $res;
    }

    /**
     *
     * Fonction privée qui permet de récupérer le rôle de l'utilisateur
     *
     * @return mixed
     */
    private static function getRole()
    {
        return !empty($_SESSION["role"]) ? strtolower($_SESSION["role"]) : null;
    }


    /**
     * Prmet de récupérer les menus autorisés par le rôle dans l'espace admin
     *
     * @param array $exceptions
     * @return array
     */
    public static function getAllowedMenus($exceptions = array())
    {

        $res = array();
        $role = self::getRole();

        if(!empty($role))
        {

            $xml = self::getXML();


            if(!empty($xml))
            {

                $roleaccess = self::getInheritance($role);


                foreach ($roleaccess as $roleacces)
                {


                    foreach ($roleacces as $key => $menus)
                    {


                        ;

                        foreach ($menus as $key2 => $menu)
                        {


                            if(empty($menu->functions)){
                                continue;
                            }




                            if(!in_array($key2, $exceptions) && !in_array($key2, $res))
                            {


                                if(!empty($menu->functions->admin))
                                {
                                    array_push($res, $key2);

                                }

                            }

                        }

                    }

                }



            }

        }



        return $res;

    }


    private static function getInheritance($role)
    {


        $xml = self::getXML();



        $inheritance = array(
            $xml->roles->{$role}
        );

        $role_given = array(
            $role
        );



        while (!empty(end($inheritance)->inherit))
        {

            $inherit = end($inheritance)->inherit->__toString();

            // Si le role est déjà attribué, on break pour éviter une boucle infinie
            if(in_array($inherit, $role_given))
            {
                break;
            }

            $role_given[] = $inherit;

            $inheritance[] = $xml->roles->{$inherit};

        }


        //var_dump($inheritance);

        return $inheritance;
    }

    private static function getXML()
    {
        return simplexml_load_file(self::$acl);
    }

}