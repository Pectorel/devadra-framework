<?php
    $title = empty($this->title) ? "Pas de title!" : $this->title;
    $description = empty($this->description) ? "Site crée par Devadra" : $this->description;
    $canonical = empty($this->canonical) ? null : $this->canonical;


    $config = parse_ini_file("config.ini", true);
    $ggId = $config["analytics"]["id"];
?>

<!doctype html>
<html>
<head>

    <?php
        if(!empty($ggId))
        {
            ?>
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ggId; ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '<?php echo $ggId; ?>');
                gtag('anonymizeIp', true);
            </script>
            <?php
        }
    ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php

        if(!empty($this->noindex))
        {
            ?>
            <meta name="robots" content="noindex, nofollow">
            <?php
        }

        if(!empty($this->description))
        {
            ?>
            <meta name="description" content="<?php echo $this->description; ?>">
            <?php
        }

    ?>
    <base href="/">
    <link rel="stylesheet" href="/Public/Styles/dist/public.min.<?php echo Generale::getCssVersion("public"); ?>.css">
    <title><?php echo $title; ?></title>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php

        if(!empty($canonical))
        {
            ?>
            <link rel="canonical" href="<?php echo $canonical; ?>" />
            <?php
        }

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
