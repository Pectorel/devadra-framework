
<?php
class Contenu extends LangTableModel
{

    protected $_instance;
    protected $_table = "Contenu";

    protected $_referenceMap = array(
        "Language" => array(
            "table" => "Language",
            "foreign" => "Language_id",
            "constraint" => "id",
            "showColumn" => "nom"
        )
    );

    protected $_careDepent = array();

    public static function getContent($name)
    {

        $lang = Generale::getLang();

        $contenuM = new Contenu();

        $sql = "SELECT contenu FROM Contenu WHERE titre = ? AND Language_id = ?";
        $params = array($name, $lang["lang"]);

        $res = $contenuM->select($sql, $params, PDO::FETCH_ASSOC, "fetch");


        if(empty($res))
        {

            $data_insert = array(
                "titre" => $name,
                "contenu" => "Contenu à ajouter"
            );


            if(get_parent_class() == "LangTableModel")
            {

                $langM = new Language();

                $sql = "SELECT id FROM Language ORDER BY id ASC";

                $lang = $langM->select($sql, array(), PDO::FETCH_ASSOC, "fetch");


                $data_insert["Language_id"] = $lang["id"];

            }


            $contenuM->insert($data_insert);

            $res = array("contenu" => $data_insert["contenu"]);

        }

        return $res["contenu"];

    }

    public static function getImg($name, $lang = null)
    {
        $contenuM = new Contenu();

        $sql = "SELECT * FROM Contenu WHERE titre = ?";
        $params = array($name);
        if($lang != null)
        {
            $sql.=" AND Language_id = ?";
            $cur_lang = Generale::getLang()["lang"];
            $params[] = $cur_lang;
        }


        $res = $contenuM->select($sql, $params, PDO::FETCH_ASSOC, "fetch");
        $img = null;

        if(!empty($res))
        {
            $img = $contenuM->getImage($res["id"], 0, "jpg");
        }


        return $img;
    }

    public function gestionForAction($obj)
    {

        $language = $this->findParent("Language", $obj);

        return $language["nom"];

    }

    /**
     * Fonction qui permet d'ajouter un enregistrement à la table correpondante
     *
     * @param array $data
     * @return bool
     */
    public function insert($data)
    {


        if(get_parent_class() != "LangTableModel" || isset($data["noCheckLang"]))
        {
            return parent::insert($data);
        }


        $res = false;

        $insert_data = $this->filter($data);

        //var_dump($insert_data);



        if(!empty($this->_table))
        {

            $sql = "INSERT INTO {$this->_table} ";

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


                $langM  = new Language();

                $sql = "SELECT * FROM Language WHERE id != ?";
                $params = array($insert_data["Language_id"]);

                $langs = $langM->select($sql, $params);


                foreach ($langs as $lang)
                {

                    $insert_data["Language_id"] = $lang["id"];
                    $insert_data["noCheckLang"] = true;

                    $this->insert($insert_data);



                }


            }


        }


        return $res;
    }

}
