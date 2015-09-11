<?php
if (!isset($_GET['key'])) {
    exit;
}

header('Location: http://192.168.33.11/files/'.$_GET['key'].'/index.html');
exit;