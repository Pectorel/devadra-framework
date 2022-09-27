<head>
    <link rel="stylesheet" href="./Public/Styles/install.css">

</head>
<?php
    /**
     * Created by PhpStorm.
     * User: Aurelien
     * Date: 25/05/2018
     * Time: 13:10
     */

    require_once "./library/DevadraF/PDO_DB.php";
    require_once "./library/DevadraF/View.php";
    require_once "./models/Generale.php";
    $view = new View();

    /*
     *
     * ================= Script installation Tables de base ==================
     *
     */

    $db = PDO_DB::getInstance();


    $step = isset($_GET["step"]) ? $_GET["step"] : null;
?>

<div id="InstallWrapper" class="wrapper bloc_center">

    <div id="StepContainer">

        <h1>Installation du Devadra Framework</h1>

        <?php

            if ($step !== null)
            {

                if(file_exists("./install/step" . $step . ".php"))
                {
                    require_once "./install/step" . $step . ".php";
                }
                else{
                    echo "Étape non trouvée";
                }


                //var_dump("./install/step" . ($step + 1) . ".php");
                if(file_exists("./install/step" . ($step + 1) . ".php"))
                {

                    if($step != 3 && $step != 5) {
                        $url = $view->url() . "install.php?step=" . ($step + 1);
                        //var_dump($url);
                        ?>

                        <a href="<?php echo $url; ?>" class="d_inline_block btn_blue">Étape Suivante</a>

                        <?php
                    }
                }
                else{
                    ?>

                    <br/>
                    <br/>
                    Base du site bien créée ! Pour des raisons de sécurité, veuillez supprimer le fichier install.php de la racine
                    <?php
                }

            }
            else{

                $url = $view->url() . "install.php?step=1";

                ?>
                <p class="mb_20">Vous êtes sur le point d'installer le Devadra Framework, afin de procéder, cliquez sur le bouton ci-dessous</p>

                <a href="<?php echo $url; ?>" class="d_inline_block btn_blue">Étape Suivante</a>
                <?php
            }


        ?>

    </div>


</div>
