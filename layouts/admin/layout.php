<?php
    $this->render("layouts/admin/head.php");
?>

<?php


$flashMessages = FlashMessage::getFlashMessages();

if(!empty($flashMessages))
{

    foreach ($flashMessages as $flashMessage):
        ?>

        <div id="FlashMessagePopup" class="popup active">

            <header class="popup_header txt_center">
                <h2 class="icon icon-info icon-bigger no-hover popup-icon">Information</h2>

                <a href="#" class="popup_close" data-popup-close></a>
            </header>

            <div class="popup_content txt_center">

                <p><?php echo $flashMessage; ?></p>

            </div>

        </div>

        <?php

    endforeach;
    FlashMessage::flushMessages();
}



$this->render("layouts/admin/header.php");

if(!empty($this->actionScript)){

    $this->render($this->actionScript);

}

$this->render("layouts/admin/footer.php");

?>

<div id="AddAjaxPopup" class="popup">

    <header class="popup_header">
        <h2 class="txt_center">Ajouter un élément</h2>
        <a href="#" class="popup_close" data-popup-close></a>
    </header>

    <div class="popup_content">



    </div>

</div>

<div id="ConfirmAjaxPopup" class="popup" data-popup>

    <header class="popup_header">
        <h2 id="ConfirmAjaxTitle" class="txt_center">Informations</h2>
        <a href="#" class="popup_close" data-popup-close></a>
    </header>

    <div class="popup_content">

        <p id="ConfirmAjaxText" class="txt_center"></p>

    </div>

</div>

<script src="/Public/Scripts/dist/admin.min.<?php echo Generale::getJsVersion("admin"); ?>.js"></script>


<script>

    $(window).ready(function ()
    {
        var $flashmessage = $("#FlashMessagePopup");

        if($flashmessage.length > 0)
        {

            var $popup = new Popup($flashmessage);


            setTimeout(function ()
            {

                $popup.open();

            }, 100);

        }

    });



</script>

<?php
    $this->render("layouts/default/end.php");
?>


