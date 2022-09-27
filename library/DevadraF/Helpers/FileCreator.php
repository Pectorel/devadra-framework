<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 27/05/2018
 * Time: 17:26
 */

require_once "./library/DevadraF/Helpers/Utilities.php";


class FileCreator
{


    private $_controller_folder = "./controllers/";
    private $_model_folder = "./models/";
    private $_default_ignored_row = array("\"_ssid\"");

    /**
     * @var PDO null
     */
    private $_db = null;


    /**
     * @param PDO $db
     */
    public function setDb($db)
    {
        $this->_db = $db;
    }

    /**
     *
     * Create Controller files
     *
     * @param $name
     * @param $options
     *
     *
     */
    public function createController($name, $options = null)
    {

        $name = ucfirst($name);

        if(!file_exists($this->_controller_folder . $name . "Controller.php"))
        {
            require "./library/DevadraF/FileCreatorTemplates/tempController.php";
            $tmp_path = "./tmp/" . $name . "Controller.php";

            $string = $this->_parseControllerString($controllerString, $name);

            file_put_contents($tmp_path, $string);


            copy($tmp_path, $this->_controller_folder . $name . "Controller.php");
            unlink($tmp_path);
        }


        if(!empty($options["model"]))
        {

            $this->createModel($name, $options);

        }

    }

    /**
     *
     * Create Model files
     *
     * @param $name
     * @param $options
     */
    public function createModel($name, $options = null)
    {

        $name = ucfirst($name);

        if(!file_exists($this->_model_folder . $name . ".php"))
        {
            require "./library/DevadraF/FileCreatorTemplates/tempModel.php";
            $tmp_path = "./tmp/" . $name . ".php";

            $string = $this->_parseModelString($modelString, $name);

            file_put_contents($tmp_path, $string);

            copy($tmp_path, $this->_model_folder . $name . ".php");

            unlink($tmp_path);
        }

    }


    /**
     * @param $string
     * @param $name
     * @return mixed
     */
    private function _parseControllerString($string, $name)
    {
        $string = str_replace("tempController", $name."Controller", $string);



        $colNotSelect = $this->_default_ignored_row;

        try{
            $sql = "SHOW COLUMNS FROM `" . $name . "`";

            $cols = $this->_db->query($sql);

            $cols = $cols->fetchAll(PDO::FETCH_NUM);

            foreach ($cols as $col)
            {

                $colNotSelect[] = "\"" . $col[0] . "\"";

            }

            $colNotSelect = implode(",\n\t\t", $colNotSelect);

            $colNotSelect = "colNotSelect = array(\n\t\t" . $colNotSelect . "\n\t)";

            $string = str_replace("colNotSelect = array()", $colNotSelect, $string);

            $colFilter = str_replace("colNotSelect", "colFilter",$colNotSelect);

            $string = str_replace("colFilter = array()", $colFilter, $string);

            $colName = array();
            foreach ($cols as $col)
            {
                $colName[] = "\"" . $col[0] . "\" => \"\"";
            }

            $colName = "colName = array(\n\t\t" . implode(",\n\t\t", $colName) . "\n\t)";

            $string = str_replace("colName = array()", $colName, $string);
        }
        catch (Exception $e)
        {
            var_dump($e);
        }


        return $string;
    }

    /**
     * @param $string
     * @param $name
     * @return mixed
     */
    private function _parseModelString($string, $name)
    {

        $string = str_replace("tempModel", $name, $string);

        $string = str_replace("table = null", "table = \"" . $name . "\"", $string);

        $sql = "SHOW COLUMNS FROM `" . $name . "`";

        $cols = $this->_db->query($sql);

        /*$exemple = array(
            "Equipe" => array(
            "table" => "equipe",
            "foreign" => "Equipe_id",
            "constraint" => "id",
            "showColumn" => "nom"
             )
        );*/




        $referenceMap = "referenceMap = array(\n\t\t";

        foreach ($cols as $col) {

            // Si clé étrangère
            if (strstr($col[0], "_id") !== false && Utilities::is_upper(substr($col[0], 0, 1)))
            {

                $table = str_replace("_id", "", $col[0]);

                $referenceMap .= "\"$table\" => array(
                \"table\" => \"$table\",
                \"foreign\" => \"$col[0]\",
                \"constraint\" => \"id\",
                ";

                $sql = "SHOW COLUMNS FROM `" . $table . "`";

                $cols_foreign = $this->_db->query($sql);

                if ($cols_foreign !== false)
                {
                    $cols_foreign = $cols_foreign->fetchAll(PDO::FETCH_NUM);

                    if (!empty($cols_foreign))
                    {

                        $referenceMap .= "\"showColumn\" => \"" . $cols_foreign[1][0] . "\"";

                    }

                }

                $referenceMap .= "\n\t\t),";
            }




        }



        $referenceMap .= "\n\t)";

        $string = str_replace("referenceMap = array()", $referenceMap, $string);


        return $string;

    }


}
?>