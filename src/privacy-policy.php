<?php ob_start()?>
<section id="intro">
    <div class="container">
        <h2>Privacy Policy</h2>
        <h3>Log Data</h3>
        <p>When you visit this website we receive your IP address as part of our standard server logs.
            We store these logs for 3 months without use your data. </p>
        <h3>Cookies</h3>
        <p>We use your cookies for Google Analytics service:
        <ul>
            <li>to "remember" what a user has done on previous pages / interactions with the website.</li>
            <li>Distinguish unique users</li>
            <li>Throttle the request rate</li>
        </ul></p>
        <br><br><br><br><br><br><br>
    </div>
</section>

<?php $main = ob_get_clean()?>
<?php $title = "Privacy Policy"?>
<?php include_once "template/layout.php" ?>