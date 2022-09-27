<?php

    $columns = $this->columns;
    $user = $this->user;

    $temp_inputs = (isset($_SESSION["temp_inputs"])) ? $_SESSION["temp_inputs"] : null;


    $exceptionsFields = array(
        "id",
        "_ssid"
    );

    $exceptionsFields = $this->exceptions;

    $colNames = $this->colNames;

    $imagesArray = $this->imagesArray;

/**
 * @var Model $model
 */
    $model = $this->model;
    $model_name = get_class($model);

    $urlform = !empty($this->urlform) ? $this->urlform : $this->url(array("controller" => $_GET["controller"], "action" => "addDo"));

    $controllername = $_GET["controller"]."Controller";
    $nom_a_afficher = $controllername::$nom_a_afficher;

    if(empty($nom_a_afficher))
    {
        $nom_a_afficher = $_GET["controller"];
    }


?>


<section class="admin_form">

    <h1 class="admin_title txt_center">
        <?php

            if(empty($user))
            {
                ?>
                Création
                <?php
            }
            else
            {
                ?>
                Modification
                <?php
            }
/*
        ?>
        d'un(e)
        */
        ?>
        de <?php echo $nom_a_afficher; ?>
    </h1>

    <div class="wrapper bloc_center">

        <form class="custom_form" method="POST" onsubmit="new FormValidator($(this), {}, event)" data-controller="<?php echo $_GET["controller"]; ?>"
              action="<?php if(empty($user))
                            {
                                echo $urlform;
                            }
                            else{
                                echo $this->url(array("controller" => $_GET["controller"], "action" => "modifyDo")) . "?id=" . $user["id"];
                            }
              ?>"

            <?php
                if(!empty($user))
                {
                    echo "data-id=\"" . $user["id"] . "\"";
                }

            ?>
        enctype="multipart/form-data"
        >

            <?php
                //var_dump($columns);

            ?>

            <?php


                $validator = array(
                        "text" => array(
                                "varchar",
                                "char"
                        ),
                        "textarea" => array(
                                "text"
                        ),
                        "checkbox" => array(
                                "tinyint"
                        ),
                        "number" => array(
                                "int",
                                "float"
                        ),
                        "date" => array(
                            "date"
                        ),
                        "datetime" => array(
                            "datetime"
                        )

                );

                if(!empty($columns))
                {

                    foreach ($columns as $column)
                    {


                        if(!empty($user["id"]) && $column["Field"] == "id")
                        {
                            ?>
                            <input type="hidden" name="id" value="<?php echo $user["id"]; ?>">
                            <?php
                        }

                        if(!in_array($column["Field"], $exceptionsFields))
                        {
                            ?>

                            <div class="bloc60 bloc_center relative">
                                <div class="bloc25 pull_left">
                                    <?php

                                        $name = ucfirst($column["Field"]);

                                        //var_dump($colNames);

                                        if(!empty($colNames[$column["Field"]]))
                                        {
                                            $name = $colNames[$column["Field"]];
                                        }

                                    ?>
                                    <label class="vertical_center" for="<?php echo $column["Field"]; ?>"><?php echo $name; ?> :</label>
                                </div>

                                <div class="bloc75 pull_right">

                                    <?php
                                        // Si majuscule premier charactère alors c'est une clé étrangère
                                        $foreign = substr($column["Field"] ,0, 1) != "_" && Utilities::is_upper(substr($column["Field"] ,0, 1));
                                        $modelName = null;
                                        $modelM = null;
                                        $refMap = null;

                                        if($foreign)
                                        {

                                            $modelName = explode("_", $column["Field"])[0];
                                            $modelM = new $modelName();

                                            $referenceMaps = $model->getReferenceMap();

                                            $foreign = false;
                                            foreach ($referenceMaps as $key => $referenceMap)
                                            {

                                                if($referenceMap["foreign"] === $column["Field"])
                                                {
                                                    $foreign = true;
                                                    $refMap = $referenceMap;
                                                    //var_dump($refMap);
                                                    break;
                                                }

                                            }

                                        }

                                        if($foreign)
                                        {

                                            $vals = $modelM->fetchAll();

                                            /*var_dump($vals);
                                            var_dump($refMap);*/


                                            ?>
                                            <select id="<?php echo $column["Field"]; ?>" name="<?php echo $column["Field"]; ?>">

                                            <?php

                                                if($column["Null"] === "YES")
                                                {
                                                    ?>
                                                    <option value="" selected></option>
                                                    <?php
                                                }
                                                if(!empty($vals))
                                                {
                                                    foreach ($vals as $val)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $val[$refMap["constraint"]]; ?>"
                                                            <?php
                                                            if (!empty($user[$column["Field"]]) && $val[$refMap["constraint"]] == $user[$refMap["foreign"]]) {
                                                                echo "selected";
                                                            } elseif (!empty($temp_inputs[$column["Field"]]) && $val[$refMap["constraint"]] == $temp_inputs[$refMap["foreign"]]) {
                                                                echo "selected";
                                                            }
                                                            ?>
                                                        >
                                                            <?php echo $val[$refMap["showColumn"]]; ?>
                                                        </option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                            </select>
                                            <?php
                                        }
                                        else
                                            {



                                            $tag = "input";
                                            $type = "text";
                                            $true_type = explode("(", $column["Type"])[0];
                                            //var_dump($true_type);

                                            foreach ($validator as $key => $validatum)
                                            {

                                                if(in_array($true_type, $validatum))
                                                {

                                                    $type = $key;
                                                    if($type === "textarea") $tag = $type;
                                                    break;

                                                }

                                            }

                                            // var_dump($tag);
                                            //var_dump($type);

                                            ?>

                                            <?php
                                            if($tag !== "textarea")
                                            {

                                                if($type !== "checkbox")

                                                {
                                                    ?>

                                                    <input id="<?php echo $column["Field"]; ?>" type="<?php echo $type; ?>" name="<?php echo $column["Field"]; ?>"
                                                        <?php
                                                        if(!empty($user[$column["Field"]]) || $user[$column["Field"]] == 0)
                                                        {
                                                            echo "value=\"" . htmlspecialchars($user[$column["Field"]]) . "\"";
                                                        }
                                                        elseif(!empty($temp_inputs[$column["Field"]]) || $temp_inputs[$column["Field"]] == 0)
                                                        {
                                                            echo "value=\"" . htmlspecialchars($temp_inputs[$column["Field"]]) . "\"";
                                                        }
                                                        ?>
                                                    />
                                                    <?php
                                                }
                                                else{

                                                    ?>
                                                    <label class="admin custom_checkbox">
                                                        <input type="checkbox" name="<?php echo $column["Field"]; ?>"
                                                            <?php if(!empty($user[$column["Field"]])) echo "checked"; ?>
                                                        >
                                                        <span class="checkbox"></span>
                                                    </label>
                                                    <?php

                                                }

                                            }
                                            else{
                                                ?>
                                                <textarea name="<?php echo $column["Field"]; ?>" id="<?php echo $column["Field"]; ?>"><?php
                                                    if(!empty($user[$column["Field"]])){
                                                        echo htmlspecialchars($user[$column["Field"]]);
                                                    }
                                                    elseif(!empty($temp_inputs[$column["Field"]]))
                                                    {
                                                        echo "value=\"" . htmlspecialchars($temp_inputs[$column["Field"]]) . "\"";
                                                    }
                                                    ?></textarea>

                                                <?php
                                            }

                                        }

                                    ?>

                                </div>

                                <div class="clear"></div>



                            </div>
                            <?php
                        }

                    };


                    if(!empty($this->imagesArray))
                    {

                        $showBtn = false;
                        $images = array();

                        if(!empty($user["id"]))
                        {
                            $images = $model->getAllImages($user["id"]);
                        }
                        ?>



                        <div id="ImagesContainer" class="bloc60 bloc_center">

                            <?php
                                $i = 0;
                                foreach ($imagesArray as $imageSlot)
                                {

                                    if(!empty($images[$i]))
                                    {
                                        ?>
                                        <div class="image_container d_inline_block mb_40 txt_center" data-img="<?php echo $_GET["controller"] . $i; ?>">
                                            <img src="<?php echo $images[$i]; ?>" alt="Erreur" class="mb_10">

                                            <?php
                                            if(Acl::isAllowed(array("controller" => $_GET["controller"], "action" => "removeImageAjaxDo")))
                                            {


                                                ?>
                                                <a href="#" class="btn btn_white btn_small" data-ajax-url="<?php echo $this->url(array("controller" => $_GET["controller"], "action" => "removeImageAjaxDo")); ?>"
                                                data-ajax-vals='{ "id":<?php echo $user["id"]; ?>, "index":<?php echo $i; ?> }' data-ajax-callback="remImageCallback"
                                                   data-ajax-method="POST">Supprimer</a>
                                                <?php
                                                /* ?>
                                                <a href="#" class="btn btn_white btn_small" data-ajax-url="<?php echo $this->url(array("controller" => $_GET["controller"], "action" => "removeImageAjaxDo")); ?>"
                                                data-ajax-vals='{ "id":<?php echo $user["id"]; ?>, "index":<?php echo $i; ?> }' data-ajax-callback="remImageCallback"
                                                   data-ajax-method="POST">Delete</a>
                                                */
                                            }

                                            ?>

                                        </div>


                                        <?php
                                    }
                                    else
                                    {

                                        ?>
                                        <label class="image_input_container txt_center">
                                            <span class="filename"></span>
                                            <span class="add_phrase">Ajouter une image</span>
                                            <?php /*<span class="add_phrase">Add a picture</span>*/ ?>
                                            <input id="Image<?php echo $model_name . $i; ?>" class="custom_file_button" type="file" name="images[]">
                                        </label>

                                        <?php
                                    }

                                    $i++;
                                }

                            ?>

                        </div>
                        <?php
                    }

                    if(Acl::isAllowed(array("controller" => $_GET["controller"], "action" => "removeDo")) && isset($_GET["delButton"]))
                    {
                        ?>
                        <div class="txt_center">

                            <button class="btn_white" data-popup-link="RemoveConfirm"
                                    data-change-href="<?php echo $this->url(array("controller" => $_GET["controller"], "action" => "removeAjaxDo")) . "?id=" . $user["id"]; ?>"
                                    data-ch-target="RemoveConfirmLink">Supprimer</button>

                            <?php /*
                            <button class="btn_white" data-popup-link="RemoveConfirm"
                                    data-change-href="<?php echo $this->url(array("controller" => $_GET["controller"], "action" => "removeAjaxDo")) . "?id=" . $user["id"]; ?>"
                                    data-ch-target="RemoveConfirmLink">Delete</button>
                            */ ?>
                        </div>

                         <?php
                    }
                }

                // CareDepents
                $careDepents = $model->getCareDepent();
                if(!empty($careDepents))
                {

                    foreach ($careDepents as $careDepent => $careName)
                    {
                        /**
                         * @var Model $careModelM
                         */
                        $careModelM = new $careDepent();




                        $controllername = $careDepent . "Controller";

                        if(empty($careName)) $careName = $controllername::$nom_a_afficher;
                        ?>
                        <div class="care_depent_block txt_center bloc60 bloc_center">
                            <h3><?php echo $careName; ?>s</h3>
                            
                            <div id="<?php echo $careDepent; ?>FormsContainer"></div>
                            
                            <?php


                            $sql = "SELECT id FROM " . $careDepent . " WHERE " . $_GET["controller"] . "_id = ? ORDER by id";
                            $params = array($user["id"]);
                            $res = $careModelM->select($sql, $params);




                            foreach ($res as $result)
                            {
                                ?>
                                <div data-care-depent data-cd-controller="<?php echo $careDepent; ?>" data-cd-id="<?php echo $result["id"]; ?>"></div>
                                <?php
                            }



                            if(Acl::isAllowed(array("controller" => $careDepent, "action" => "addAjax")))
                            {

                                ?>
                                <button id="AddAjaxButton<?php echo $careDepent; ?>" class="btn_white" data-controller="<?php echo $careDepent; ?>">Ajouter</button>
                                <?php /*
                                <button id="AddAjaxButton<?php echo $careDepent; ?>" class="btn_white" data-controller="<?php echo $careDepent; ?>">Add</button>
                                */
                            }
                            ?>
                        </div>
                        <?php



                    }

                }

            ?>




            <?php
                if(!isset($_GET["noSave"]))
                {
                    ?>
                     <div class="txt_center">
                        <input class="btn_white" type="submit" value="Enregistrer">
                         <?php /*<input class="btn_white" type="submit" value="Save">*/ ?>
                    </div>
                    <?php
                }

                if(isset($_GET["localRemove"]))
                {
                    ?>
                    <div class="txt_center">
                        <button class="btn_white local_remove">Supprimer</button>
                        <?php /*<button class="btn_white local_remove">Delete</button>*/ ?>

                    </div>

                    <?php
                }

            ?>

        </form>

    </div>

</section>

<?php
    if(!isset($_GET["noRemovePopup"]))
    {
        ?>
        <div id="RemoveConfirm" class="popup" data-popup>

            <div class="popup_header">
               <h2 class="txt_center">Attention!</h2>
                <?php /* <h2 class="txt_center">Warning!</h2>*/ ?>
                <a href="#" class="popup_close" data-popup-close></a>
            </div>

            <div class="popup_content">

               <p class="txt_center">Voulez-vous vraiment supprimer ce(tte) <span id="RemControllerNAme"><?php echo $nom_a_afficher; ?></span>?</p>
                <?php /* <p class="txt_center">Are you sure you want to delete this <span id="RemControllerNAme"><?php echo $nom_a_afficher; ?></span>?</p>*/ ?>


                <div class="txt_center">
                    <a id="RemoveConfirmLink" class="btn_white" href="<?php echo $this->url(array("controller" => $_GET["controller"], "action" => "removeAjaxDo")); ?>" data-ajax>Supprimer</a>
                    <a href="#" class="btn_white" data-popup-close>Annuler</a>

                    <?php /*
                    <a id="RemoveConfirmLink" class="btn_white" href="<?php echo $this->url(array("controller" => $_GET["controller"], "action" => "removeAjaxDo")); ?>" data-ajax>Delete</a>
                    <a href="#" class="btn_white" data-popup-close>Cancel</a>
                    */ ?>
                </div>

            </div>

        </div>

        <?php
    }

?>
