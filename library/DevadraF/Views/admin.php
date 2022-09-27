<?php

    $res = $this->results;
    $controller = $this->controller;


    $params = array(
        "controller" => $controller
    );


    //var_dump($controller);
    /**
     * @var $modelM Model
     */
    $modelM = new $controller();



    $controllername = $controller."Controller";
    $nom_a_afficher = $controllername::$nom_a_afficher;

    if(empty($nom_a_afficher))
    {
        $nom_a_afficher = $controller;
    }

    /**
    * @var $paginator Paginator
    */
    $paginator = $this->paginator;



?>


<section id="DefaultAdmin">

    <div class="wrapper bloc_center">

        <h2 class="txt_center">Liste des <?php echo $nom_a_afficher; ?>s</h2>
        <?php /*<h2 class="txt_center">List of <?php echo $nom_a_afficher; ?>s</h2>*/?>

        <?php
            $this->render("library/DevadraF/Views/filter_form.php");
        ?>

        <div id="ItemsContainer">
            <?php
            if(!empty($res)) {
                foreach ($res as $item):
                    ?>

                    <div class="item_line" data-id="<?php echo $item[0]; ?>">

                        <div class="bloc85 pull_left">
                            <div class="bloc80 pull_left">
                                <p><?php echo $modelM->getDisplayName($item); ?></p>

                            </div>
                            <div class="bloc20 pull_right txt_right">
                                <?php

                                $infos = $modelM->gestionForAction($item);


                                if (!empty($infos)) {
                                    ?>
                                    <span><?php echo $infos; ?></span>
                                    <?php
                                }
                                //var_dump($infos);

                                ?>
                            </div>

                            <div class="clear"></div>


                        </div>

                        <div class="bloc15 pull_right txt_right">

                            <ul class="reset">

                                <?php
                                $params["action"] = "modify";
                                if (Acl::isAllowed($params)["access"]) {
                                    ?>
                                    <li><a class="icon icon-center icon-pencil no-hover"
                                           href="<?php echo $this->url(array("controller" => $controller, "action" => "modify")) . "?id=" . $item[0]; ?>">

                                        </a>
                                    </li>
                                    <?php
                                }

                                $params["action"] = "removeDo";
                                if (Acl::isAllowed($params)["access"]) {
                                    ?>
                                    <li><a class="icon icon-center icon-trash no-hover" href="#"
                                           data-popup-link="RemoveConfirm"
                                           data-change-data-ajax-url="<?php echo $this->url(array("controller" => $controller, "action" => "removeAjaxDo")) . "?id=" . $item[0]; ?>"
                                           data-ch-target="RemoveConfirmLink">
                                        </a>
                                    </li>
                                    <?php
                                }

                                ?>

                            </ul>


                        </div>

                        <div class="clear"></div>

                    </div>

                    <?php
                endforeach;
            }
            ?>

        </div>


        <?php

            $params["action"] = "add";
            if(Acl::isAllowed($params)["access"])
            {
                ?>
                <div class="txt_center">
                    <a href="<?php echo $this->url(array("controller" => $controller, "action" => "add")); ?>" class="btn_white d_inline_block">Ajouter un(e) <?php echo $nom_a_afficher; ?></a>

                    <?php /* <a href="<?php echo $this->url(array("controller" => $controller, "action" => "add")); ?>" class="btn_white d_inline_block">Add a <?php echo $nom_a_afficher; ?></a>
                    */ ?>
                </div>

                <?php
            }


            $nbpages = $paginator->getNbPages();



            if($nbpages > 1)
            {
                ?>
                <div id="Pagination" class="txt_center">

                    <?php
                    for($i = 1; $i <= $nbpages; $i++)
                    {
                        ?>
                        <a href="<?php echo $controller; ?>/admin?page=<?php echo $i; ?>" class="pagination_btn d_inline_block"><?php echo $i; ?></a>
                        <?php
                    }

                    ?>

                </div>
                <?php
            }

        ?>
        
    </div>


</section>

<?php

?>
<div id="RemoveConfirm" class="popup" data-popup>

    <div class="popup_header">
        <h2 class="txt_center">Attention!</h2>
        <?php /*<h2 class="txt_center">Warning!</h2>*/?>
        <a href="#" class="popup_close" data-popup-close></a>
    </div>

    <div class="popup_content">

        <p class="txt_center">Voulez-vous vraiment supprimer ce(tte) <?php echo $nom_a_afficher; ?>?</p>
        <?php /*<p class="txt_center">Are you sure you want to delete <?php echo $nom_a_afficher; ?>?</p>*/?>


        <div class="txt_center">

            <button id="RemoveConfirmLink" class="btn_white" data-ajax-url="<?php echo $this->url(array("controller" => $controller, "action" => "removeAjaxDo")) ?>" data-ajax-callback="listingAdminRemove">Supprimer</button>
            <a href="#" class="btn_white" data-popup-close>Annuler</a>

            <?php /*
            <button id="RemoveConfirmLink" class="btn_white" data-ajax-url="<?php echo $this->url(array("controller" => $controller, "action" => "removeAjaxDo")) ?>" data-ajax-callback="listingAdminRemove">Delete</button>
            <a href="#" class="btn_white" data-popup-close>Cancel</a>
            */?>
        </div>

    </div>

</div>

