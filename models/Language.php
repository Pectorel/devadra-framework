
<?php
class Language extends Model
{

    protected $_instance;
    protected $_table = "Language";
    private $_no_replication = array(
        "Language.php",
        "AdminLanguage.php"
    );

    protected $_referenceMap = array(

    );

    protected $_careDepent = array();

    // TODO : Add Image Cloning if necessary
    public function insert($data)
    {
        $res = parent::insert($data);
        $lang_id = $this->getLastIdInserted();
        $lang = $this->find($lang_id);

        $logger = new Logger("./log/LanguageInsert.log");
        $logger->write("========= New Language Added ========= \n");
        $logger->write("Language added : " . $lang["nom"] . "\n");

        $sql = "SELECT * FROM Language WHERE nom = ?";
        $params = array("English");
        $replicationLang = $this->select($sql, $params, PDO::FETCH_ASSOC, "fetch");

        $logger->write($replicationLang["nom"] . " Language found for replication. Id : " . $replicationLang["id"] . "\n\n");

        foreach (new DirectoryIterator('./models') as $file) {
            if ($file->isFile() && $file->getExtension() == "php") {

                $name = $file->getFilename();
                //$logger->write($name);

                if((!in_array($name, $this->_no_replication) && stristr($name, "Language") !== false) || $name == "Contenu.php")
                {
                    $model_name = str_replace(".php", "", $name);
                    $logger->write("LanguageTable to clone found ! Name : " . $model_name . "");

                    $model = new $model_name();

                    $parent_class = get_parent_class($model);
                    $logger->write("Object is of type : " . $parent_class);

                    if($parent_class == "LangTableModel")
                    {
                        $logger->write(" == Starting to clone elements from " . $replicationLang["nom"] . " to " . $lang["nom"] . " == ");

                        $sql = "SELECT * FROM " . $model_name . " WHERE Language_id = ?";
                        $params = array($replicationLang["id"]);

                        $to_clone = $model->select($sql, $params);

                        if(count($to_clone) > 0)
                        {

                            foreach ($to_clone as $clone)
                            {

                                $clone["id"] = null;
                                $clone["Language_id"] = $lang_id;

                                if($model_name == "Contenu")
                                {
                                    $clone["noCheckLang"] = true;
                                }

                                $model->insert($clone);

                            }

                        }
                        $logger->write(" == Cloning Ended : " . count($to_clone) . " objects cloned == \n");



                    }
                    else{
                        $logger->write("Incorrect Type detected, cloning will not occur! \n", "Warning");
                    }



                }
            }
        }

        $logger->close();

        return $res;


    }

    /**
     * @param $origin
     * @param $target
     * @param $table
     * @param $model Model
     * @deprecated
     */
    private function _cloneImage($origin, $target, $table, $model)
    {

        $path = "./Public/Images/" . $table . "/" . $target;

        if(!file_exists($path))
        {

            mkdir($path, 0755, true);

            // Si on change pas les propriétaires sur Linux alors pas les droits nécessaires pour mettre les fichiers
            chown("./Public/Images/" . $table . "/", "www-data");
            chgrp("./Public/Images/" . $table . "/", "www-data");
            chown($path, "www-data");
            chgrp($path, "www-data");
        }

        $images = array();

        $images = $model->getAllImages($origin);

        $i = 0;
        foreach ($images as $image)
        {
            copy($image, $path. "/" . $i . ".jpg");
            $i++;
        }

    }

}
