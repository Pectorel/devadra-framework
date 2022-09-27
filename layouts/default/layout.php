<?php
    $this->render("layouts/default/head.php");
?>

<?php


    $flashMessages = FlashMessage::getFlashMessages();

    if(!empty($flashMessages))
    {

        foreach ($flashMessages as $flashMessage):
            ?>

            <div id="FlashMessagePopup" class="popup active">

                <header class="popup_header txt_center">
                    <h2>Erreur</h2>

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

    $this->render("layouts/default/header.php");

    if(!empty($this->actionScript)){

        $this->render($this->actionScript);

    }

    $this->render("layouts/default/footer.php");
?>

<script type="text/javascript" src="/Public/Scripts/dist/public.min.<?php echo Generale::getJsVersion("public"); ?>.js"></script>

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
