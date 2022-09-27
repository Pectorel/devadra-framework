<?php

    $colFilters = $this->colFilter;
    $model = $this->model;
    $colNames = $this->colNames;
    //var_dump($colNames);

    if(!empty($colFilters))
    {

        ?>
        <div class="admin_filter">

            <header>
                <h2>Filtres : </h2>
                <?php /*<h2>Filters</h2>*/ ?>
                <span class="icon icon-arrow-down" data-slide-down="AdminFilterForm"></span>
            </header>


            <div id="AdminFilterForm" class="slide_down">
                <form class="custom_form" method="POST">
                    <?php

                    foreach($colFilters as $colFilter):

                        // Si traducteur, on passe le language
                        if($_SESSION["role"] == "traducteur" && $colFilter == "Language_id")
                        {
                            continue;
                        }

                        $foreign = Utilities::is_upper(substr($colFilter ,0, 1));
                        $modelName = null;
                        $modelM = null;
                        $refMap = null;

                        if($foreign)
                        {

                            $modelName = explode("_", $colFilter)[0];
                            $modelM = new $modelName();

                            $referenceMaps = $model->getReferenceMap();

                            $foreign = false;
                            foreach ($referenceMaps as $key => $referenceMap)
                            {

                                /*if($key === $modelName)
                                {
                                    $foreign = true;
                                    $refMap = $referenceMap;
                                    break;

                                }*/

                                if($referenceMap["foreign"] === $colFilter)
                                {
                                    $foreign = true;
                                    $refMap = $referenceMap;
                                    break;
                                }

                            }

                        }

                        $filter_name = !empty($colNames[$colFilter]) ? $colNames[$colFilter] : ucfirst($colFilter);

                        if($foreign)
                        {

                            $vals = $modelM->fetchAll();

                            /*var_dump($vals);
                            var_dump($refMap);*/




                            if(!empty($vals))
                            {
                                ?>
                                <div class="filter_line relative">
                                    <div class="bloc25 pull_left">


                                        <label class="vertical_center" for="<?php echo $colFilter ?>"> <?php echo $filter_name; ?> :
                                        </label>
                                    </div>

                                    <div class="bloc70 pull_right">
                                        <select id="<?php echo $colFilter; ?>" name="<?php echo $colFilter; ?>">
                                            <option value></option>
                                            <?php
                                            foreach ($vals as $val):
                                                ?>
                                                <option value="<?php echo $val[$refMap["constraint"]]; ?>"
                                                    <?php
                                                    if(!empty($_POST[$colFilter]) && $val[$refMap["constraint"]] == $_POST[$colFilter])
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>
                                                >
                                                    <?php echo $val[$refMap["showColumn"]]; ?>
                                                </option>
                                                <?php
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="clear"></div>

                                </div>

                                <?php
                            }
                        }
                        else
                        {


                            ?>

                            <div class="filter_line relative">
                                <div class="bloc25 pull_left">
                                    <label class="vertical_center" for="<?php echo $colFilter ?>"> <?php echo $filter_name; ?> :
                                    </label>
                                </div>
                                <div class="bloc70 pull_right">
                                    <input id="<?php echo $colFilter ?>" type="text" name="<?php echo $colFilter ?>"
                                        <?php if (!empty($_POST[$colFilter])) echo "value=\"" . htmlspecialchars($_POST[$colFilter]) . "\""; ?>
                                    >
                                </div>
                                <div class="clear"></div>
                            </div>


                            <?php
                        }
                    endforeach;
                    ?>
                    <div class="txt_center no_margin">
                        <input type="submit" class="btn_white" value="Filtrer">
                        <a href="<?php echo $this->url(array("controller" => $this->controller, "action" => "admin")); ?>" class="btn_white">Réinitialiser</a>
                        <?php /*
                        <input type="submit" class="btn_white" value="Filter">
                        <a href="<?php echo $this->url(array("controller" => $this->controller, "action" => "admin")); ?>" class="btn_white">Reset</a>
                        */?>
                    </div>
                </form>
            </div>
        </div>
        <?php

    }

?>
