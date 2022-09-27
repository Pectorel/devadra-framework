<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 23/01/2019
 * Time: 17:43
 */

class LangTableModel extends Model
{


    /**
     *
     * Fonction qui permet de filtrer les résultats dans admin
     *
     * @param int $fetch_type
     * @param array $options
     * @return array|null
     */
    public function filterAdmin($fetch_type = PDO::FETCH_ASSOC, $options = array())
    {


        if(!isset($_GET["page"]))
        {
            unset($_SESSION["admin_filter"]);
        }

        $res = null;
        $params = $_POST;

        //var_dump($params);

        $sql = null;
        $whereparams = array();

        unset($params["action"]);
        unset($params["controller"]);

        if(empty($params) && !empty($_SESSION["admin_filter"]))
        {
            $params = $_SESSION["admin_filter"];
        }


        $filter = false;

        foreach ($params as $param)
        {
            if(!empty($param))
            {

                $filter = true;
                break;

            }
        }

        if($filter)
        {

            $_SESSION["admin_filter"] = $params;

            $sql = "SELECT * FROM {$this->_table} WHERE ";

            foreach ($params as $key => $param) {

                if(!empty($param))
                {
                    $sql.= $key . " LIKE ? AND ";
                    array_push($whereparams, $param);
                }

            }
            $sql = trim($sql, "AND ");
        }

        /*echo $sql;
        var_dump($whereparams);*/


        $where = false;
        if(empty($sql)) {
            $where = true;
            $sql = "SELECT * FROM {$this->_table} ";
        }


        if($_SESSION["role"] == "traducteur")
        {

            if($where)
            {
                $sql.= "WHERE ";
            }
            else{
                $sql.= "AND ";
            }

            $adminsql = "SELECT id FROM Admin WHERE _ssid = ?";
            $adminparam = array($_SESSION["session_id"]);

            $adminM = new Admin();
            $user = $adminM->select($adminsql, $adminparam, PDO::FETCH_ASSOC, "fetch");

            $adminLangM = new AdminLanguage();

            $sqladmLanguage = "SELECT Language_id FROM AdminLanguage WHERE Admin_id = ?";
            $admLanguageParam = array($user["id"]);

            $langs = $adminLangM->select($sqladmLanguage, $admLanguageParam, PDO::FETCH_ASSOC);

            $questionsmarks = str_repeat("?,", count($langs)-1) . "?";

            $sql.= "Language_id IN(" . $questionsmarks . ")";

            foreach ($langs as $lang)
            {
                $whereparams[] = $lang["Language_id"];
            }


        }


        if(!empty($options))
        {

            if(!empty($options["Limit"]))
            {

                $sql.= " LIMIT " . $options["Limit"];

            }

        }

        //var_dump($sql);

        $res = $this->select($sql, $whereparams, $fetch_type);


        return $res;

    }


    public function gestionForAction($obj)
    {

        $lang = $this->findParent("Language", $obj);

        return $lang["nom"];

    }

}