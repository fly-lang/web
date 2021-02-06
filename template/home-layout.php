<?php ob_start() ?>

<!--------------------------
Header
---------------------------->
<header id="header">

    <div id="topbar">
        <div class="container">
            <div class="logo-home float-left">
                <a href="/" class="scrollto img-fluid"><img src="img/flylang-logo.png" alt=""></a>
            </div>
            <div class="social-links">
                <a href="https://twitter.com/fly_lang" class="twitter"><i class="fa fa-twitter"></i>
                    <span class="slogan">FOLLOW</span></a>
                <a href="https://discord.gg/kyFHvTWUUg" class="discord"><i class="fa fa-discord"></i>
                    <span class="slogan">CHAT</span></a>
                <a href="https://github.com/fly-lang" class="github"><i class="fa fa-github"></i>
                    <span class="slogan">DEVELOP</span></a>
            </div>
        </div>
    </div>

    <?php include_once "nav.php" ?>

</header><!-- #header -->

<!--------------------------
Intro Section
---------------------------->
<a name="intro"></a>
<section id="intro" class="clearfix">
    <div class="container">
        <div class="row justify-content-center align-self-center">
            <div class="intro-top col-md-12">

            </div>

            <div class="intro-info col-md-6">
                <h1>Fly Programming Language</h1>
                <h3>Contribute to build your favourite programming language:</h3>
                <ul>
                    <li>
                        <h4>Simple</h4>
                    </li>
                    <li>
                        <h4>Performing</h4>
                    </li>
                    <li>
                        <h4>Versatile</h4>
                    </li>
                </ul>
            </div>
            <div class="intro-img col-md-6">
                <img src="img/intro-img.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-12">
                <h4>There is a lot of programming language, but Software Industry still prefers the same languages
                    in percentage from 30 or 40 years ago (<a href="https://www.tiobe.com/tiobe-index/" target="_blank">Tiobe.com</a>).
                    Some languages have been evolved but conserve the same core features from born but now we have the chance to start
                    from zero and create something different.<br>

                </h4>
                <h3 style="text-align: center">We aren't a company, we are the developers, we want to choose our next programming language!</h3>
            </div>
        </div>
    </div>
</section>
<!-- #intro -->
<?php $header = ob_get_clean() ?>

<?php include_once "layout.php" ?>