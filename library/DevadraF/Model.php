<?php

class Model
{


    protected $_instance = null;
    protected $_sql = null;
    protected $_params = array();
    protected $_table = null;
    protected $_referenceMap = array();
    protected $_careDepent = array();

    public function __construct()
    {
        $this->_connect();
    }

    protected function _connect()
    {
        //PDO
        $this->_instance = PDO_DB::getInstance();
    }

    public function getReferenceMap()
    {
        return $this->_referenceMap;
    }

    public function getCareDepent()
    {
        return $this->_careDepent;
    }


    /*public function __call($name, $arguments)
    {


        if(strpos("Via", $name))
        {

            $to_find = str_replace("find", "", explode("Via", $name)[0]);
            $via = explode("Via", $name)[1];
            var_dump($to_find);
            var_dump($via);

            return $this->findVia($to_find, $via);


        }

    }*/


    public function find($id)
    {

        $res = null;

        if(!empty($this->_table))
        {

            $stmt = $this->_instance->prepare("SELECT * FROM `{$this->_table}` WHERE id = ?");
            $stmt->execute(array($id));
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            //var_dump($res);

        }

        return $res;

    }

    /**
     *
     * Fonction qui permet de récupérer tous les enregistrements d'une table
     *
     * @param int $fetch_type
     * @return array|null
     */
    public function fetchAll($fetch_type = PDO::FETCH_ASSOC)
    {

        $res = null;

        if (!empty($this->_table))
        {

            $stmt = $this->_instance->prepare("SELECT * FROM `{$this->_table}`");
            $stmt->execute();
            $res = $stmt->fetchAll($fetch_type);


        }

        //var_dump($res);

        return $res;

    }


    /**
     * Fonction qui permet d'ajouter un enregistrement à la table correpondante
     *
     * @param array $data
     * @return bool
     */
    public function insert($data)
    {

        $res = false;

        $insert_data = $this->filter($data);

        //var_dump($insert_data);



        if(!empty($this->_table))
        {

            $sql = "INSERT INTO `{$this->_table}` ";

            $cols = "(";

            $insert = "VALUES (";

            $inserted_data = array();

            foreach ($insert_data as $key => $value) {

                $cols.= "`" . $key."`, ";
                $insert.= "?, ";
                array_push($inserted_data, $value);

            }
            $cols = trim($cols, ", ");
            $insert = trim($insert, ", ");


            $insert.= ")";

            $cols.= ") ";


            $sql = $sql.$cols.$insert;

            //error_log($sql);

            $stmt = $this->_instance->prepare($sql);
            $inserted = $stmt->execute($inserted_data);

            if($inserted)
            {
                $res = true;
            }


        }


        return $res;
    }


    /**
     *
     * Fonction qui filtre les données d'un tableau pour qu'elles correspondent aux champs de la table
     *
     * @param $data
     * @param array $no_check
     * @param null $elem
     * @return array | null
     */
    public function filter($data, $no_check = array("id","_ssid"), $elem = null){

        $res = null;

        if(empty($no_check)) $no_check = array("id", "_ssid");

        //var_dump($data);

        $validator = array(

            "string" => array(
                "varchar",
                "text",
                "char",
                "datetime",
                "date",

            ),
            "integer" => array(
                "tinyint",
                "int",
                "bigint",
                "double",
                "varchar"
            ),
            "double" => array(
                "float",
                "double"
            ),
            "checkbox" => array(
                "tinyint"
            ),
        );

        /*$no_check = array(
            "id",
            "_ssid"
        );*/

        if(!empty($this->_table))
        {

            $columns = $this->getColumns();

            //var_dump($columns);



            if(!empty($columns))
            {

                foreach ($columns as $num => $column)
                {

                    //var_dump($data);
                    $key = $column["Field"];
                    $true_type = explode("(", $column["Type"])[0];
                    //var_dump($key);

                    if(!empty($data[$key]) || (is_numeric($data[$key]) && $data[$key] == 0))
                    {


                        if($data[$key] !== "on")
                        {

                            /* echo $true_type . "<br />";
                             echo gettype($data[$key]) . "<br /><br />";
                            */

                            if(is_numeric($data[$key]) && $key !== "pass"){


                                //echo 'Numeric '. $data[$key] . "<br />";
                                if(stristr($true_type, "int") !== false)
                                {
                                    $data[$key] = (intval($data[$key]));
                                }
                                else if(stristr($true_type, "float") !== false || stristr($true_type, "double") !== false){
                                    $data[$key] = (floatval($data[$key]));
                                }
                                //echo gettype($data[$key]);


                            }

                            if(in_array($true_type, $validator[gettype($data[$key])]))
                            {
                                $res[$key] = $data[$key];
                            }

                        }
                        else{

                            //echo "checkbox";
                            if($true_type === "tinyint")
                            {
                                $res[$key] = 1;
                            }

                        }


                    }
                    elseif(!in_array($key, $no_check))
                    {

                        if($true_type === "tinyint")
                        {
                            $res[$key] = 0;
                        }
                        // Si modif d'un élément, alors les éléments pas présent reprennent la valeur de l'élément de base
                        elseif(!empty($elem))
                        {
                            $elem_val = (empty($elem[$key]) && $elem[$key] != 0) ? $elem[$num] : $elem[$key];
                            $res[$key] = $elem_val;

                        }// Sinon ça prend la valeur par défaut de la colonne
                        elseif(!empty($column["Default"]) || $column["Default"] == 0)
                        {
                            $res[$key] = $column["Default"];
                        }// Et sinon on met null
                        else{
                            $res[$key] = null;
                        }


                    }
                }
            }
        }




        return $res;

    }


    public function getLastIdInserted()
    {

        $sql = "SELECT id FROM `{$this->_table}` ORDER BY id DESC";
        $res = $this->select($sql, array(), PDO::FETCH_ASSOC, "fetch");

        return $res["id"];
    }

    public function update($data, $cond, $condparams)
    {

        $res = false;
        $sql = "UPDATE `{$this->_table}` SET ";

        $elem = $this->find($data["id"]);

        $filtered_data = $this->filter($data, null, $elem);

        if(!empty($filtered_data))
        {

            $params = array();

            foreach ($filtered_data as $key => $filtered)
            {
                $sql.= "`" . $key . "` = ?, ";
                array_push($params, $filtered);
            }

            $sql = trim($sql, ", ");

            $sql.= " " . $cond;

            if(!empty($condparams))

            {
                $params = array_merge($params, $condparams);
            }

            //var_dump($params);

            //echo $sql;


            //error_log($sql);

            $stmt = $this->_instance->prepare($sql);
            $updated = $stmt->execute($params);

            if($updated)
            {
                $res = true;
            }

        }

        return $res;

    }

    /**
     *
     * Fonction qui permet de créer une requête sql
     *
     * @param $sql
     * @param $params
     * @param int $fetch_type
     * @param string $fetch_style
     * @return null
     */
    public function select($sql, $params, $fetch_type = PDO::FETCH_ASSOC, $fetch_style = "fetchAll"){


        if(empty($fetch_type)) $fetch_type = PDO::FETCH_ASSOC;
        $res = null;
        if(!empty($this->_table))
        {

            $stmt = $this->_instance->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->{$fetch_style}($fetch_type);

        }

        return $res;

    }

    public function delete($cond, $condparams)
    {

        $res = false;

        if(!empty($cond) && !empty($condparams) && !empty($this->_table))
        {

            $stmt = $this->_instance->prepare("DELETE FROM `{$this->_table}` " . $cond);
            $executed = $stmt->execute($condparams);

            if($executed)
            {
                $res = true;
            }
        }

        return $res;

    }

    public function getColumns()
    {

        $res = null;

        if(!empty($this->_table))
        {

            $stmt = $this->_instance->prepare("SHOW FULL COLUMNS FROM `{$this->_table}`");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        return $res;

    }


    public function findParent($table, $obj)
    {

        $res = null;

        if(!empty($this->_table))
        {

            $modelName = ucfirst($table);
            $modelM = new $modelName();



            $param = (!empty($obj[$modelName . "_id"])) ? $obj[$modelName . "_id"] : null;

            if(empty($param))
            {

                $obj = $this->find($obj[0]);
                $param = $obj[$modelName . "_id"];
            }


            $res = $modelM->find($param);


        }


        return $res;


    }

    /**
     * Fonction pour afficher des infos supplémentaires dans les lignes de l'espace admin
     *
     * @param $obj
     */
    public function gestionForAction($obj){}


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

            $sql = "SELECT * FROM `{$this->_table}` WHERE ";

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







        if(empty($sql)) {
            $sql = "SELECT * FROM `{$this->_table}` ";
        }


        if(!empty($options))
        {

            if(!empty($options["Sort"]))
            {

                $sql.= "  ORDER BY " . $options["Sort"];

            }

            if(!empty($options["Limit"]))
            {

                $sql.= "LIMIT " . $options["Limit"];

            }


        }

        $res = $this->select($sql, $whereparams, $fetch_type);


        return $res;

    }

    /**
     * Renvoie le nom à afficher dans l'espace Admin
     *
     * @param $obj
     * @return null
     */
    public function getDisplayName($obj)
    {

        $res = null;

        if(is_array($obj))
        {
            $res = array_slice($obj, 1, 1)[0];
        }


        return $res;

    }

    public function getAllImages($id)
    {

        $res = array();

        $path = "./Public/Images/" . $this->_table . "/" . $id;

        if(file_exists($path))
        {

            $files = array_diff(scandir($path), array('..', '.'));

            if(!empty($files))
            {

                foreach ($files as $file)
                {
                    $res[] = $path . "/" . $file;
                }

            }

        }

        return $res;

    }

    public function getImage($id, $indeximg, $type="png")
    {

        $res = null;

        $array = Generale::getManifest(true);
        /*$logger = new Logger("./log/img_versioning.log");
        $logger->write($this->_table . " : " . $array["Images"][$this->_table][$id][$indeximg]);
        $logger->close();*/
        $version = !empty($array["Images"][$this->_table][$id][$indeximg]) ? $array["Images"][$this->_table][$id][$indeximg] : 1;

        $path = "./Public/Images/" . $this->_table . "/" . $id . "/" . $indeximg . "." . $type;

        if(file_exists($path))
        {
            $res = "./Public/Images/" . $this->_table . "/" . $id . "/" . $indeximg . "." . $version . "." . $type;
        }

        return $res;

    }

    public function getImageVersion($id, $indeximg, $type="png")
    {

        $res = 1;

        $manifest = Generale::getManifest(true);

        if(!empty($manifest["Images"][$this->_table][$id][$indeximg]))
        {
            $res = $manifest["Images"][$this->_table][$id][$indeximg];
        }

        return $res;

    }



}