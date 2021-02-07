<?php ob_start() ?>
    <section>
        <div class="container">
            <h2>Licenses</h2>
            <h3>Code</h3>
            <p>The Fly Programming Language, including this website, use:
            <ul>
                <li>
                    <a href="https://www.apache.org/licenses/LICENSE-2.0" target="_blank">Apache License, Version
                        2.0</a>
                </li>
            </ul>
            </p>
            <h3>Arts</h3>
            <p>The Fly logos (bitmap and vector) are owned by Fly Community and distributed under the terms of the
                <a href="https://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution license (CC-BY).</a>
            </p>
            <br><br><br><br><br><br><br><br>
        </div>
    </section>

<?php $main = ob_get_clean() ?>
<?php $title = "Licenses" ?>
<?php include_once "template/page-layout.php" ?>