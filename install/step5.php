<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 29/05/2018
 * Time: 17:32
 */

    // Droits par défaut qui s'affiche juste pour indication
    //  NE PAS ENLEVER CES DROITS SAUF INDICATION CONTRAIRE!
    $default_rights = array(

        "Admin" =>  array(
            "accueil" => array(
                "admin"
            ),
            "disconnectDo" => array(
                "admin"
            ),
            "index" => array(
                "guest"
            ),
            "connectDo" => array(
                "guest"
            ),
        ),
        "Index" => array(
            "index" => array(
                "guest",
            ),
        ),
        "Error" => array(
            "error404" => array(
                "guest"
            ),
            "error401" => array(
                "guest"
            ),
            "maintenance" => array(
                "guest"
            )
        ),
        "Update" => array(
            "update" => array(
                "admin"
            )
        )
    );

    // Droits admin
    // Récupération à partir de la classe "Controller",
    // TOUTES LES FONCTIONS ADMIN DEVRAIENT ÊTRE DANS CE FICHIER!!!
    $admin_rights = array();

    $controllerFile = "./library/DevadraF/Controller.php";

    //On récupère le contenu du controller pour afficher les fonctions déjà disponibles
    $controllerContent = file_get_contents($controllerFile);

    $matches = array();
    preg_match_all("/\w+Action\(*\)/", $controllerContent, $matches);

    //var_dump($matches);

    foreach ($matches as $match)
    {
        foreach ($match as $adAction)
        {
            $admin_rights[] = preg_replace("/Action\(*\)/", "", $adAction);
        }

    }

    //var_dump($admin_rights);

    // Recup des rôles
    $acl = simplexml_load_file("./acl.xml") or die("Error: Cannot create object");

    $rolesxml = $acl->roles;

    $roles = array();
    foreach ($rolesxml as $key => $roleobj)
    {
        foreach ($roleobj as $role => $details)
        {
            $roles[] = $role;
        }

    }



?>

<h2 class="mb_20">Étape 4 : Création des droits acl pour les controllers</h2>

<?php

    $controllers = scandir("./controllers");
    $controllers = array_diff($controllers, array('.', '..'));


?>


<form class="custom_form" action="<?php echo $view->url() . "install.php?step=6"; ?>" method="POST" onsubmit="removeDisabled()">


    <?php
        foreach ($controllers as $controller):

            $name = str_replace("Controller.php", "", $controller);

            ?>

            <fieldset class="mb_40">
                <legend><?php echo $name; ?></legend>

                <?php
                    if(array_key_exists($name, $default_rights))
                    {

                        foreach ($default_rights[$name] as $action => $defroles):
                            ?>

                            <div class="acl_line">

                                <div class="acl_libelle pull_left">
                                    <span><?php echo ucfirst($action); ?></span>
                                </div>

                                <?php

                                    foreach ($roles as $role):

                                        ?>

                                            <div class="role_check_wrapper pull_right bloc10">
                                                <span class="label_top"><?php echo ucfirst($role); ?></span>
                                                <label class="custom_checkbox fix_label_top">

                                                    <input type="checkbox" name="<?php echo $role . "[" . $name . "]" . "[" . $action . "]";?>" value="allow" <?php if(in_array($role, $defroles)) echo "checked disabled"; ?>>
                                                    <span class="checkbox"></span>
                                                </label>

                                            </div>

                                        <?php
                                    endforeach;

                                ?>


                                <div class="clear"></div>
                            </div>

                            <?php
                        endforeach;

                    }

                    foreach ($admin_rights as $admin_right):
                        ?>
                            <div class="acl_line">

                                <div class="acl_libelle pull_left">
                                    <span><?php echo ucfirst($admin_right); ?> (Administration)</span>
                                </div>

                                <?php
                                    foreach ($roles as $role):

                                        ?>

                                        <div class="role_check_wrapper pull_right bloc10">
                                            <span class="label_top"><?php echo ucfirst($role); ?></span>
                                            <label class="custom_checkbox fix_label_top">

                                                <input type="checkbox" name="<?php echo $role . "[" . $name . "]" . "[" . $admin_right . "]"; ?>" value="allow" <?php if($role == "admin") echo "checked"; ?>>
                                                <span class="checkbox"></span>
                                            </label>

                                        </div>

                                        <?php
                                    endforeach;
                                ?>
                            </div>
                        <?php
                    endforeach;

                ?>




            </fieldset>

            <?php
        endforeach;

    ?>


    <input class="btn_blue" type="submit" value="Étape suivante">


</form>

<script>
    function removeDisabled()
    {

        var $disabled = document.querySelectorAll("[disabled]");

        for (var i=0; i<$disabled.length; i++) {

            $disabled[i].removeAttribute("disabled");

        }

    }
    
</script>



