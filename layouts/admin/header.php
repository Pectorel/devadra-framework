<header class="navbar sticky">

    <nav class="navbar_content bloc_center wrapper">


        <div class="navbar_left">
            <ul class="reset">

                <li>
                    Espace Admin <a href="<?php echo $this->url(); ?>" target="_blank"><?php echo $this->url(); ?></a>
                </li>
            </ul>

        </div>

        <div>

            <h1>Espace Admin</h1>

        </div>

        <div class="navbar_right">

            <ul class="reset">
                <li><a class="icon icon-disconnect" href="<?php echo $this->url(array("controller" => "Admin", "action" => "disconnectDo")); ?>">Déconnexion</a></li>
            </ul>
            
        </div>


    </nav>

</header>


<?php

if(!$this->hideAsideMenu)
{
    ?>
    <nav id="AdminAside" class="aside_nav <?php if($this->asideactive) echo "active"; ?>">

        <div class="burger">
            <ul class="reset">
                <li><a id="AdminMenuLink" href="#" data-togclass-active="AdminAside"  data-togclass-open="nav-icon3">
                        Menu
                        <div class="icon icon-right icon-burger <?php if($this->asideactive) echo "open"; ?>" id="nav-icon3">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                </li>
            </ul>

        </div>

        <ul class="reset">
            <li><a href="<?php echo $this->url(array("controller" => "Admin", "action" => "accueil")); ?>">Accueil</a></li>
        <?php
            $menus = Acl::getAllowedMenus(array("Admin", "Error", "Index"));

            if(!empty($menus))
            {


                foreach ($menus as $menu):
                    $menu_controller = $menu."Controller";
                    $nom_a_afficher = $menu_controller::$nom_a_afficher;
                    if(empty($nom_a_afficher))
                    {
                        $nom_a_afficher = $menu;
                    }
                    ?>

                    <li><a href="<?php echo $this->url(array("controller" => $menu, "action" => "admin")); ?>"><?php echo $nom_a_afficher; ?></a></li>
                    <?php
                endforeach;

            }
        ?>
        </ul>

    </nav>
    <?php
}