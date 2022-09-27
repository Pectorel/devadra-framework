<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 28/05/2018
 * Time: 18:23
 */


    $pages = array(

        "Mentions légales",
        "Conditions de vente"

    );

?>

<h2>Étape 3 : Création des pages par défaut</h2>

<h3>Choisissez quelles page doivent être créées pas défaut sur le site</h3>


<form action="<?php echo $view->url() . "install.php?step=4";?>" class="custom_form" method="POST">



    <?php
        foreach ($pages as $page)
        {
            ?>
            <div class="bloc20 bloc_center">
                <label class="custom_checkbox">
                    <?php echo $page; ?>
                    <input type="checkbox" name="pages[]" value="<?php echo $page; ?>">
                    <span class="checkbox"></span>
                </label>

            </div>


            <?php
        }

    ?>

    <input class="btn_blue" type="submit" value="Étape suivante">


</form>
