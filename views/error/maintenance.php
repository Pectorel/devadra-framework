<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 29/08/2018
 * Time: 18:22
 */

?>

<!doctype html>
<html lang="<?php echo $_SESSION["langcode"]; ?>" class="error_page">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="robots" content="noindex, nofollow">
        <base href="/">
        <link rel="stylesheet" href="/Public/Styles/dist/public.min.<?php echo Generale::getCssVersion("public"); ?>.css">
        <title><?php echo $title; ?></title>
        <?php

        if(!empty($this->script))
        {

            foreach ($this->script as $script)
            {
                ?>
                <script src="<?php echo $script; ?>"></script>
                <?php
            }

        }

        ?>

    </head>


    <body>
        <div class="maintenance">
            <h1>Notice</h1>
            <p>
                Nous sommes actuellement en maintenance, nous nous excusons de la gène occasionnée
            </p>
        </div>
    </body>

</html>

