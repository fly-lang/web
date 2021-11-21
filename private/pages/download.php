<?php
    $title = 'Download';
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
                <h1>Download latest</h1>
                <script type="text/javascript">
                    var packages = <?php echo json_encode($latest) ?>;
                </script>

                <div class="container-fluid" id="download">
                    <a type="button" href="#" onclick="downloadAutoPackage(packages)" class="btn btn-red btn-lg">
                        Fly <small><?php $latest_version = $latest['version'].str_replace("v", ""); echo $latest_version ?> Prerelease</small></a>
                </div>
                <br>
                <h3>Chose your package for latest version <?php echo $latest_version ?></h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">OS</th>
                        <th scope="col">Arch</th>
                        <th scope="col">Version</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Windows</td>
                        <td>x64</td>
                        <td><?php echo $latest_version ?></td>
                        <td><a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['windows']['url'] ?>', '<?php echo $latest['windows']['content_type'] ?>')">Download</a></td>
                    </tr>
                    <tr>
                        <td>macOS</td>
                        <td>x64</td>
                        <td><?php echo $latest_version ?></td>
                        <td><a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['macos']['url'] ?>', '<?php echo $latest['macos']['content_type'] ?>')">Download</a></td>
                    </tr>
                    <tr>
                        <td>Linux</td>
                        <td>x64</td>
                        <td><?php echo $latest_version ?></td>
                        <td><a href="javascript:void(0);" onclick="downloadPackage('<?php echo $latest['linux']['url'] ?>', '<?php echo $latest['linux']['content_type'] ?>')">Download</a></td>
                    </tr>
                    </tbody>
                </table>


            </div>
        </div>
    </div>
</section>
<!-- #intro -->
