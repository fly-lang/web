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
        <div class="row">
            <div class="col-md-8">
                <span id="title">Fly</span>
                <span id="subtitle">Programming Language for writing easily and quickly all types of software.</span>
            </div>

                <script type="text/javascript">
                    var packages = <?php echo json_encode($latest) ?>;
                </script>

                <div class="container-fluid">
                    <a id="download-btn" type="button" href="#" onclick="downloadAutoPackage(packages)" class="btn btn-red btn-lg">
                        <span class="download">Download</span>
                        <span class="version"><?php echo $latest['version'] ?> <small>(Prerelease)</small></span>
                    </a>
                <span id="download-packages">Download packages for
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['windows']['url'] ?>', '<?php echo $latest['windows']['content_type'] ?>')">Windows 64-bit</a>,
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['macos']['url'] ?>', '<?php echo $latest['macos']['content_type'] ?>')">macOS</a>,
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['linux']['url'] ?>', '<?php echo $latest['linux']['content_type'] ?>')">Linux</a>.</span>
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
