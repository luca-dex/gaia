<?php

/*
 * ©2012 Croce Rossa Italiana
 */

require('./core.inc.php');

header('Content-type: application/json');

$api = new APIServer( @$_REQUEST['sid'] );
$api->par = array_merge($_POST);
echo $api->esegui($_GET['a']);
