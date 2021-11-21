<?php
    $title = 'Home';
    $releases = new FlyReleases();
    $latest = $releases->getLatest();
?>
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
                <p>
                    The Fly programming language was created in 2020 as an alternative to other programming languages.
                    We aim to be fast, efficient and easy to understand. We have made our syntax quick to write and to
                    be powerful and versatile so that you can continue to develop great software!
                </p>
                <script type="text/javascript">
                    var packages = <?php echo json_encode($latest) ?>;
                </script>

                <div class="container-fluid" id="download">
                    <a type="button" href="#get-started" class="btn btn-azure btn-lg">Get Started</a>
                    <a type="button" href="#" onclick="downloadAutoPackage(packages)" class="btn btn-red btn-lg">
                        Download <small>(<?php echo $latest['version'] ?> Prerelease)</small></a>
                </div>
                <p>Download packages for
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['windows']['url'] ?>', '<?php echo $latest['windows']['content_type'] ?>')">Windows 64-bit</a>,
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['macos']['url'] ?>', '<?php echo $latest['macos']['content_type'] ?>')">macOS</a>,
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['linux']['url'] ?>', '<?php echo $latest['linux']['content_type'] ?>')">Linux</a>.</p>
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

        <div class="content">
            <h3>Install latest version</h3>
            <p>
                <a target="_blank" href="javascript:void(0);" onclick="window.location.href=config.release.url.releases">Download</a>
                and install the latest version of Fly by following the
                <a target="_blank" href="javascript:void(0);" onclick="window.location.href=config.wiki.install">installation guide</a>
            </p>
        </div>

        <div class="content">
            <h3>Hello World</h3>
            <p>To get started with Fly we will use the classic code example of "Hello world" to demonstrate the basic
                syntax of the Fly programming language.</p>
            <small class="text-muted">main.fly</small>
            <pre class="fly-code">
                <code>
        import std

        main() {
            std.print("Hello World!");
        }
                </code>
            </pre>
        </div>

        <div class="content">
            <h3>Learning Fly</h3>
            <p>You can start to learn the Fly language from the official
                <a target="_blank" href="javascript:void(0);" onclick="window.location.href=config.wiki.url">Github Wiki</a>.
                Here you can find:
            </p>
            <div class="row">
                <div class="col-md-4">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="javascript:void(0);" onclick="window.location.href=config.wiki.languageReference">
                                    Language Reference
                                </a>
                            </h5>
                            <p class="card-text">The main reference to Fly language syntax</p>
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
                            <p class="card-text">The guide to launch Fly command and how to use all different options.</p>
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
                            <p class="card-text">For better understand compiler functions or starting to contribute to project development.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section><!-- #home -->
