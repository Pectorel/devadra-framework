<section id="AdminIndex">

    <h1 class="txt_center">Connexion à l'espace admin</h1>

    <div class="wrapper bloc_center">

        <form id="AdminForm" class="custom_form txt_center" action="<?php echo $this->url(array("controller" => "Admin", "action" => "connectDo")); ?>" METHOD="POST">

            <div class="bloc35 bloc_center">
                <input type="text" name="login" placeholder="Identifiant" required>
            </div>
            <div class="bloc35 bloc_center">
                <input type="password" name="pass" placeholder="Mot de passe" required>
            </div>

            <input type="submit" value="Connexion">

        </form>

    </div>

</section>
