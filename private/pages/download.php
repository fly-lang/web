<?php
    $title = 'Download';
    $releases = new FlyReleases();
    $latest = $releases->getLatest();
?>
<!--------------------------
Intro Section
---------------------------->

<section id="intro">
    <div class="container">
        <h2>Download</h2>
        <script type="text/javascript">
            var packages = <?php echo json_encode($latest) ?>;
        </script>
        <h3>Latest version <?php echo $latest_version ?></h3>
        <p>Download latest version for your operative system.</p>
        <div class="container-fluid" id="download">
            <a type="button" href="#" onclick="downloadAutoPackage(packages)" class="btn btn-red btn-lg">
                Fly <small><?php $latest_version = $latest['version'].str_replace("v", ""); echo $latest_version ?> Prerelease</small></a>
        </div>
    </div>
</section>

<section id="intro">
    <div class="container">
        <h3><?php echo $latest_version ?></h3>
        <p>Chose by architecture.</p>
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
</section>
<!-- #intro -->
