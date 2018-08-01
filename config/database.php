<?php

/*
 * Standard single-node URI format: 
 * mongodb://[username:password@]host:port/[database]
 */
 
$db_user = "username";
$db_pass = "password1";
$db_host = "ds159661.mlab.com";
$db_port = "59661";
$db_name = "grouphealth";

$uri = "mongodb://" . $db_user . ":" . $db_pass . "@" . $db_host . ":" . $db_port . "/" . $db_name;

$client = new MongoDB\Client($uri);

?>