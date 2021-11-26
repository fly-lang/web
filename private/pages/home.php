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
                <span class="title">Fly</span>
                <span class="subtitle">Programming Language for writing easily and quickly all types of software.</span>
            </div>

            <script type="text/javascript">
                var packages = <?php echo json_encode($latest) ?>;
            </script>

            <div class="container-fluid">
                <a type="button" href="#" onclick="downloadAutoPackage(packages)" class="btn download-btn btn-red">
                    <span class="download">Download</span>
                    <span class="version"><?php echo $latest['version'] ?> <small>(Prerelease)</small></span>
                </a>
                <span class="download-packages">Download packages for
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['windows']['url'] ?>', '<?php echo $latest['windows']['content_type'] ?>')">Windows 64-bit</a>,
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['macos']['url'] ?>', '<?php echo $latest['macos']['content_type'] ?>')">macOS</a>,
                    <a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['linux']['url'] ?>', '<?php echo $latest['linux']['content_type'] ?>')">Linux</a>.
                </span>
            </div>
        </div>
    </div>
</section>
<!-- #intro -->

<!--------------------------
Home Section
---------------------------->
<section class="bg-blue">

    <div class="container" id="get-started">
        <h2>- Get Started -</h2>

        <div class="content">
            <h3>First Example</h3>
            <p>To get started with Fly we will use the classic code example of "Hello world" to demonstrate the basic
                syntax of the Fly programming language.</p>
            <small class="fly-filename">main.fly</small>
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
            <h3>Learn Fly</h3>
            <p>You can start to learn the Fly language from the official
                <a target="_blank" href="<? echo $WIKI_URL?>" target="_blank">Github Wiki</a>.
                Here you can find:
            </p>
            <div class="row">
                <div class="col-md-4">
                    <h4>
                        <a href="<? echo $WIKI_LANGUAGE_REFERENCE_URL?>" target="_blank">
                            Language Reference
                        </a>
                    </h4>
                    <p class="card-text">The main reference to Fly language syntax</p>
                </div>
                <div class="col-md-4">
                    <h4>
                        <a href="<? echo $WIKI_COMMAND_GUIDE_URL?>" target="_blank">
                            Command Guide
                        </a>
                    </h4>
                    <p class="card-text">The guide to launch Fly command and how to use all different options.</p>
                </div>
                <div class="col-md-4">
                    <h4>
                        <a href="<? echo $WIKI_PROGRAMMERS_MANUAL_URL?>" target="_blank">
                            Programmer's Manual
                        </a>
                    </h4>
                    <p class="card-text">For better understand compiler functions or starting to contribute to project development.</p>
                </div>
            </div>
        </div>
    </div>

</section><!-- #home -->
