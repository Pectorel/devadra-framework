<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 31/05/2018
 * Time: 13:57
 *
 *
 * @var View $this
 */


    $loginField = $this->loginField;
    $passField = $this->passField;
    $controller = $this->controller;

    $url = $this->url(array("controller" => $controller, "action" => "connectDo"));



?>

<form action="<?php echo $url; ?>" method="POST" class="custom_form">

    <label>
        Identifiant
        <input type="text" name="<?php echo $loginField; ?>">
    </label>

    <label>
        Mot de passe
        <input type="password" name="<?php echo $passField; ?>">
    </label>

    <input type="submit" value="Connexion">

</form>


