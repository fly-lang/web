<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $title = "Fly Programming Language - " . $title?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?php echo $description = "Fly is an open source compiled programming language simple, fast and powerful." ?>" name="description">
    <?php $baseurl = "https://flylang.org" ?>
    <!-- Facebook OpenGraph -->
    <meta property="og:title" content="<?php echo $title ?>" />
    <meta property="og:url" content="<?php echo $url = $baseurl . $_SERVER['REQUEST_URI'] ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="<?php echo $description ?>" />
    <meta property="og:image" content="<?php echo $baseurl . "/img/flylang-logo.png" ?>>" />
    <!-- Twitter OpenGraph -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?php echo $title ?>" />
    <meta name="twitter:description" content="<?php echo $description ?>" />
    <meta name="twitter:url" content="<?php echo $url ?>" />
    <meta name="twitter:image" content="<?php echo $baseurl . "/img/flylang-logo.png" ?>" />

    <!-- Favicons -->
    <link href="img/favicon.png" rel="icon">
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Bootstrap CSS File -->
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Libraries CSS Files -->
    <link href="lib/fork-awesome/css/fork-awesome.min.css" rel="stylesheet">

    <!-- Main Stylesheet File -->
    <link href="css/style.css" rel="stylesheet">

</head>

<body>

<!--------------------------
Header
---------------------------->
<header id="header">

    <?php include_once "header.php" ?>

</header>
<!-- #header -->

<!--------------------------
Main
---------------------------->
<main id="main">
<?php echo $main?>
</main>
<!-- #main -->

<?php include_once "footer.php" ?>

<!-- JavaScript Libraries -->
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/jquery/jquery-migrate.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="lib/mobile-nav/mobile-nav.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/platform/platform.js"></script>

<!-- Template Main Javascript File -->
<script src="js/main.js"></script>

<!-- Google Analytics -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-81EPH0DZ94"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-81EPH0DZ94');
</script>

</body>
</html>