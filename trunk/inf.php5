<?php
require_once('appinclude.php5');

// force a login page
$facebook->require_frame();
$user = $facebook->require_login();

// Echo the "infinite session key" that everyone keeps talking about.
echo $facebook->api_client->session_key;
?>
