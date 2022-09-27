<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 04/11/2018
 * Time: 18:03
 */

class LangModel extends Model
{



    public function find($id)
    {

        $res = null;

        if(!empty($this->_table))
        {

            $stmt = $this->_instance->prepare("SELECT T.*, TL.*, T.id FROM `{$this->_table}` T INNER JOIN {$this->_table}Language TL ON T.id=TL.{$this->_table}_id AND TL.Language_id = ? WHERE T.id = ?");
            $stmt->execute(array(Generale::getLang()["lang"], $id));
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

            $stmt = $this->_instance->prepare("SELECT T.*, TL.*, T.id FROM `{$this->_table}` T INNER JOIN {$this->_table}Language TL ON T.id=TL.{$this->_table}_id AND TL.Language_id = ?");
            $params = array(Generale::getLang()["lang"]);
            $stmt->execute($params);
            $res = $stmt->fetchAll($fetch_type);

        }

        //var_dump($res);

        return $res;
    }

    /**
     * PErmet de récupérer la traduction d'un élément
     *
     * @param $obj
     * @param null $lang
     * @return array | null
     */
    public function getTrad($obj, $lang = null)
    {

        $res = null;


        if(!empty($this->_table))
        {

            $id = empty($obj["id"]) ? $obj[0] : $obj["id"];

            $stmt = $this->_instance->prepare("SELECT * FROM {$this->_table}Language WHERE {$this->_table}_id = ? AND Language_id = ?");

            $lang = empty($lang) ? Generale::getLang()["lang"] : $lang;

            $params = array($id, $lang);
            $stmt->execute($params);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);


        }

        return $res;

    }

    public function orderElementBy($order = array("col" => "id", "order" => "ASC"))
    {

        $res = null;

        if (!empty($this->_table))
        {

            $stmt = $this->_instance->prepare("SELECT T.*, TL.*, T.id FROM `{$this->_table}` T INNER JOIN {$this->_table}Language 
            TL ON T.id=TL.{$this->_table}_id AND TL.Language_id = ? ORDER BY T." . $order["col"] . " " . $order["order"] );
            $params = array(Generale::getLang()["lang"]);
            $stmt->execute($params);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //var_dump($res);

        return $res;

    }

    public function getDisplayName($obj)
    {

        $trad = $this->getTrad($obj, 1);



        $value = array_slice($trad, 1, 1);


        return end($value);

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
        //var_dump($data);

        $insert_data = $this->filter($data);

        //var_dump($insert_data);



        if(!empty($this->_table))
        {

            $sql = "INSERT INTO `{$this->_table}` ";

            $cols = "(";

            $insert = "VALUES (";

            $inserted_data = array();

            foreach ($insert_data as $key => $value) {

                $cols.= $key.", ";
                $insert.= "?, ";
                array_push($inserted_data, $value);

            }
            $cols = trim($cols, ", ");
            $insert = trim($insert, ", ");


            $insert.= ")";

            $cols.= ") ";


            $sql = $sql.$cols.$insert;

            $stmt = $this->_instance->prepare($sql);
            $inserted = $stmt->execute($inserted_data);

            if($inserted)
            {
                $res = true;


                // Duplication des éléments pour toutes les langues
                $id = $this->getLastIdInserted();

                $langM = new Language();
                $sql = "SELECT * FROM Language ORDER BY id ASC";
                $langs = $langM->select($sql, array());


                $model = $this->_table."Language";

                /**
                 * @var Model $modelM
                 */
                $modelM = new $model();

                $array_insert = array(
                    $this->_table."_id" => $id
                );

                foreach ($insert_data as $key => $data)
                {
                    $array_insert[$key] = $data;
                }

                foreach ($langs as $lang)
                {

                    $array_insert["Language_id"] = $lang["id"];

                    $modelM->insert($array_insert);

                }



            }

        }


        return $res;
    }


}