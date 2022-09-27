<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 27/05/2018
 * Time: 18:21
 */


?>

<h2>Étape 2 : Création des Controller et des Models</h2>

<?php

    require_once "./library/DevadraF/Helpers/FileCreator.php";

    class InstallCtrlModls extends FileCreator
    {

        public function init($db)
        {

            $sql = "
                SHOW TABLES;
            ";

            $tables = $db->query($sql);

            //var_dump($tables->fetchAll(PDO::FETCH_NUM));

            $tables = $tables->fetchAll(PDO::FETCH_NUM);

            foreach($tables as $table)
            {

                //var_dump($table[0]);

                $this->setDb($db);

                $options["model"] = true;

                $this->createController($table[0], $options);

            }

            $this->createController("SuperAdmin", array("model" => false));

        }

    }

    $installController = new InstallCtrlModls();
    $installController->init($db);

?>

<p>
   Les Controllers et Models ont bien été crées
</p>


