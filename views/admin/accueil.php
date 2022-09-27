<?php
    $dashsMenus = $this->dashs;
?>


<section id="AdminAccueil">

    <div class="wrapper bloc_center">

        <h1 class="txt_center mb_20">Bienvenue dans l'interface de gestion de <?php echo $this->url(); ?></h1>
        <?php /*<h1 class="txt_center mb_20">Welcome to the admin dashboard of <?php echo $this->url(); ?></h1>*/ ?>


        <div id="DashBoard">

            <h2 class="txt_center">Dashboard</h2>


            <div id="DashboardContainer">


                <?php

                    foreach ($dashsMenus as $section => $dashs)
                    {
                        ?>
                        <div class="dashboard_section">

                            <h3 class="txt_center mb_30"><?php echo $section ?></h3>

                            <div class="dash_container">
                                <?php
                                foreach ($dashs as $dash)
                                {
                                    ?>
                                    <a href="<?php echo $dash["lien"]; ?>" class="dashboard_card <?php if(!empty($dash["class"])) echo $dash["class"]; ?>">

                                        <span><?php echo $dash["titre"]; ?></span>

                                    </a>
                                    <?php
                                }

                                ?>

                            </div>



                        </div>
                        <?php
                    }

                ?>





            </div>

        </div>

    </div>

</section>



