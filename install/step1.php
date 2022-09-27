<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 27/05/2018
 * Time: 18:10
 */

    require_once "./library/DevadraF/Model.php";
    require_once "./models/Language.php";

?>





<h2>Étape 1 : Création des tables requises</h2>

<?php

    $prefix = "Devadra";

    $admin_table = "Admin";

    /* ================== ADMIN ============== */
    $sql = "
                CREATE TABLE IF NOT EXISTS {$admin_table} (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `login` VARCHAR(45) NOT NULL,
                  `pass` VARCHAR(255) NOT NULL,
                  `role` VARCHAR(50) NOT NULL,
                  `_ssid` VARCHAR(12) NULL,
                  PRIMARY KEY (`id`))
                ENGINE = InnoDB
                
            ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table Admin !</span> <br/>";
        die();
    }

    $sql = "SELECT id FROM {$admin_table}
              WHERE login = 'Superadmin'";

    $req = $db->query($sql);

    if($req !== false)
    {

        if(count($req->fetchAll()) == 0)
        {

            $hash = password_hash("4Tbpvgsh", PASSWORD_BCRYPT);

                    $sql = "
                        INSERT INTO {$admin_table}
                        (login, pass, role)
                         VALUES ('Superadmin', '$hash', 'superadmin')
                    ";

                    $req = $db->query($sql);

                    if($req === false)
                    {
                        echo "<span class='error'>Erreur lors de la création de l'admin par défaut !</span> <br/>";
                        die();
                    }

                }
    }
    else{
        echo "<span class='error'>Erreur lors de la recherche de l'admin par défaut !</span> <br/>";
        die();
    }

    echo "Table Admin créée <br/>";





    /* ================== CONFIG ============== */
    $config_table = $prefix . "Config";

    $sql = "
                CREATE TABLE IF NOT EXISTS {$config_table} (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `nom` VARCHAR(255) NOT NULL,
                  `value` VARCHAR(255) NOT NULL,
                  PRIMARY KEY (`id`))
                ENGINE = InnoDB
            ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table GFRConfig !</span> <br/>";
        die();
    }

    echo "Table GFRConfig créée <br/>";

    /* ==================== Language ================== */

    $sql = "
            CREATE TABLE IF NOT EXISTS `Language` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `nom` VARCHAR(255) NOT NULL,
              `code` VARCHAR(10) NOT NULL,
              `active` TINYINT NOT NULL DEFAULT 1,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
        ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table Language !</span> <br/>";
        die();
    }

    echo "Table Language créée <br/>";


    $languageM = new Language();

    $sql = "SELECT * FROM Language";

    $res = $languageM->select($sql, array());

    if(count($res) == 0)
    {
        $lang_insert = array(
            "nom" => "Français",
            "code" => "fr",
            "active" => 1
        );

        $languageM->insert($lang_insert);

        echo "Français ajouté aux langues <br/>";

    }


    /* ================== Admin Language ========================= */

$sql = "
        CREATE TABLE IF NOT EXISTS `AdminLanguage` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `Admin_id` INT NOT NULL,
          `Language_id` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_AdminLanguage_Admin1_idx` (`Admin_id` ASC),
          INDEX `fk_AdminLanguage_Language1_idx` (`Language_id` ASC),
          CONSTRAINT `fk_AdminLanguage_Admin1`
            FOREIGN KEY (`Admin_id`)
            REFERENCES `Admin` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_AdminLanguage_Language1`
            FOREIGN KEY (`Language_id`)
            REFERENCES `Language` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB
    ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table AdminLanguage !</span> <br/>";
        die();
    }

    echo "Table AdminLanguage créée <br/>";

    /* ================== Contenu ============== */

$sql = "
                CREATE TABLE IF NOT EXISTS `Contenu` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `titre` VARCHAR(255) NOT NULL,
                  `contenu` TEXT NOT NULL,
                  `Language_id` INT NOT NULL,
                  PRIMARY KEY (`id`),
                  INDEX `fk_Contenu_Language1_idx` (`Language_id` ASC),
                  CONSTRAINT `fk_Contenu_Language1`
                    FOREIGN KEY (`Language_id`)
                    REFERENCES `Language` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE)
                ENGINE = InnoDB
            ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table Contenu !</span> <br/>";
        die();
    }

    echo "Table Contenu créée <br/>";


    /* ================== Template ================ */

    $sql = "
            CREATE TABLE IF NOT EXISTS `Template` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `intitule` VARCHAR(255) NOT NULL,
              `_view` VARCHAR(255) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table Template !</span> <br/>";
        die();
    }

    echo "Table Template créée <br/>";


    /* ================== PAGE ================ */

        $sql = "
              CREATE TABLE IF NOT EXISTS `Page` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `titre` VARCHAR(255) NOT NULL,
              `sous-titre` VARCHAR(255) NULL,
              `titrehtml` VARCHAR(255) NULL,
              `deschtml` VARCHAR(255) NULL,
              `_action` VARCHAR(255) NULL,
              `_controller` VARCHAR(255) NULL,
              `_route` VARCHAR(255) NULL,
              `Template_id` INT NOT NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_Page_Template1_idx` (`Template_id` ASC),
              CONSTRAINT `fk_Page_Template1`
                FOREIGN KEY (`Template_id`)
                REFERENCES `Template` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
        ";

        $req = $db->query($sql);

        // Si erreur requête
        if($req === false)
        {
            echo "<span class='error'>Erreur lors de la création de la table Page !</span> <br/>";
            die();
        }

    echo "Table Page créée <br/>";

    /* ================== PositionPhoto ================ */

    $sql = "
             CREATE TABLE IF NOT EXISTS `PositionPhoto` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `position` VARCHAR(255) NOT NULL,
              `_class` VARCHAR(255) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
                ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table PositionPhoto !</span> <br/>";
        die();
    }

    echo "Table PositionPhoto créée <br/>";

    /* ================== PARAGRAPHE ================ */

    $sql = "
          CREATE TABLE IF NOT EXISTS `Paragraphe` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `titre` VARCHAR(255) NOT NULL,
          `contenu` TEXT NOT NULL,
          `PositionPhoto_id` INT NULL,
          `Page_id` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_Paragraphe_Page1_idx` (`Page_id` ASC),
          INDEX `fk_Paragraphe_PositionPhoto1_idx` (`PositionPhoto_id` ASC),
          CONSTRAINT `fk_Paragraphe_Page1`
            FOREIGN KEY (`Page_id`)
            REFERENCES `Page` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_Paragraphe_PositionPhoto1`
            FOREIGN KEY (`PositionPhoto_id`)
            REFERENCES `PositionPhoto` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB
            ";

    $req = $db->query($sql);

    // Si erreur requête
    if($req === false)
    {
        echo "<span class='error'>Erreur lors de la création de la table Paragraphe !</span> <br/>";
        die();
    }

    echo "Table Paragraphe créée <br/>";
?>

<div class="mb_20"></div>






