<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 28/05/2018
 * Time: 18:38
 */

    // ============= Création Template par défaut ===================

    $idTemplate = null;

    $sqlget = "SELECT id 
        FROM Template 
        WHERE `_view` = 'default.php'
    ";

    $res = $db->query($sqlget);

    $res = $res->fetchAll(PDO::FETCH_ASSOC);


    if(count($res) == 0)
    {

        $sql = "INSERT INTO Template (intitule, `_view`) 
        VALUES ('Défaut', 'default.php')
    ";

        $res = $db->query($sql);

        if($res === false)
        {
            echo "Ereur lors de la création de l'enregistrement";
            die();
        }

        $res = $db->query($sqlget);

        $res = $res->fetchAll(PDO::FETCH_ASSOC);

    }


    $idTemplate = $res[0]["id"];

    $pages = $_POST["pages"];


    if(!empty($pages))
    {
        foreach ($pages as $page)
        {

            $sql = "SELECT id FROM Page
                    WHERE titre = '$page'
            ";

            $res = $db->query($sql);

            $res = $res->fetchAll(PDO::FETCH_NUM);

            if(count($res) > 0)
            {
                continue;
            }

            $sql = "
                INSERT INTO Page (titre, Template_id)
                VALUES ('" . $page . "', $idTemplate)
            ";


            $res = $db->query($sql);

            if($res === false)
            {

                echo "La page " . $page . "n'a pas pu être crée!";
                die();

            }


        }

    }
?>


<h2>Étape 3 : Création des pages par défaut</h2>

<p>Pages crées : </p>

<div class="bloc33 bloc_center">
    <ul class="vertical_list">


        <?php
            if(!empty($pages))
            {
                foreach ($pages as $page)
                {
                    ?>
                    <li><?php echo $page; ?></li>
                    <?php
                }
            }
            else{
                ?>
                <li>Aucune</li>
                <?php
            }

        ?>
    </ul>

</div>



