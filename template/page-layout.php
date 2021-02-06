<?php ob_start() ?>

<!--------------------------
Header
---------------------------->
<header id="header">

    <div class="container">

        <div class="logo-page float-left">
            <a href="/" class="scrollto img-fluid"><img src="img/flylang-logo.png" alt=""></a>
        </div>

        <?php include_once "nav.php" ?>

    </div>
</header><!-- #header -->
<div class="margin-top-100"></div>
<?php $header = ob_get_clean() ?>

<?php include_once "layout.php" ?>