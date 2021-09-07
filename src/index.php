<?php ob_start() ?>

<!--------------------------
Intro Section
---------------------------->

<section id="intro" class="clearfix">
    <div class="container">
        <div class="row justify-content-center align-self-center">
            <div class="intro-top col-md-12">

            </div>

            <div class="intro-info col-md-8">
                <h1>Fly Programming Language</h1>
                <ul>
                    <li><h4>Continue to develop <strong>as you've always done</strong></h4></li>
                    <li><h4>Maintain your <strong>code simple</strong></h4></li>
                    <li><h4>Compiled, Multi-Paradigm, Easy, Fast, Versatile</h4></li>
                </ul>
                <div class="container-fluid" id="download">
                    <button type="button" onclick="download()" class="btn btn-primary btn-lg">Download (0.0.4-alpha)</button>
                    <a type="button" href="#get-started" class="btn btn-light btn-outline-info btn-lg">Get Started</a>
                </div>
                <p>Download packages for <a href="javascript:void(0);" onclick="window.location.href=getDownloadWinUrl()">Windows 64-bit</a>, <a href="javascript:void(0);" onclick="window.location.href=getDownloadMacUrl()">macOS</a>, <a href="javascript:void(0);" onclick="window.location.href=getDownloadLinuxUrl()">Linux</a>.</p>
            </div>
            <div class="intro-img col-md-4">
                <img src="img/intro-img.png" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</section>
<!-- #intro -->

<!--------------------------
Home Section
---------------------------->
<section>

    <div class="container" id="get-started">
        <h2>Get Started</h2>
        <h3>Install latest version</h3>
        <p><a target="_blank" href="javascript:void(0);" onclick="window.location.href=config.release.url.releases">Download</a> and Install the latest version,
            by following the <a target="_blank" href="javascript:void(0);" onclick="window.location.href=config.wiki.install">installation guide</a></p>
        <br>
        <h3>Hello World</h3>
        <p>A classic to show basic syntax of Fly programming language.</p>
        <small class="text-muted">main.fly</small>
        <pre class="fly-code">
            <code>
                import std

                main() {
                    std.print("Hello World!");
                }
            </code>
        </pre>
        <br>
        <h3>Learning Fly</h3>
        <p>You can start to Fly code from official <a target="_blank" href="javascript:void(0);" onclick="window.location.href=config.wiki.url">Github Wiki</a> here you can find:</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="javascript:void(0);" onclick="window.location.href=config.wiki.languageReference">
                                Language Reference
                            </a>
                        </h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="javascript:void(0);" onclick="window.location.href=config.wiki.commandGuide">
                                Command Guide
                            </a>
                        </h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="javascript:void(0);" onclick="window.location.href=config.wiki.programmersManual">
                                Programmer's Manual
                            </a>
                        </h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section><!-- #project -->

<?php $main = ob_get_clean() ?>
<?php $title = "Home" ?>
<?php include_once "template/layout.php" ?>