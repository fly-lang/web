<?php
include_once '..' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'config.php';

$page = $_SERVER['REQUEST_URI'];
$pos = strpos($_SERVER['REQUEST_URI'], '?');
if ($pos) {
    $page = substr($_SERVER['REQUEST_URI'], 0, $pos );
}
$page_array = explode('/', $page);
$page = trim(end($page_array));

if (!$page) {
    $page = 'home';
}
$script = $PAGES_PATH . $page . '.php';
if (file_exists($script)) {
    // Include Scripts
    include_once $SCRIPTS_PATH . 'github-api.php';
    include_once $SCRIPTS_PATH . 'fly-releases.php';

    // Cache page
    ob_start();
    include_once $script;
    $html = ob_get_clean();

    //  Include Template
    include_once $TEMPLATE_PATH . 'layout.php';
}