<?php

/*
 * ©2013 Croce Rossa Italiana
 */

paginaAdmin();
$v = $_GET['id'];
$v = Volontario::id($v);

if ( strlen($_POST['inputPassword']) < 8 || strlen($_POST['inputPassword']) > 15 ) {
	redirect('presidente.utenti&passe');
}

if ($_POST['inputPassword']!=$_POST['inputPassword2']){
	redirect('presidente.utenti&passen');
}

$password     = $_POST['inputPassword'];
$v->cambiaPassword($password);

redirect('presidente.utenti&passok');
