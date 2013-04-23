<?php

/*
 * ©2012 Croce Rossa Italiana
 */

/* Carico la configurazione */
$_conf = [
    'database', 'costanti', 'sessioni',
    'generale', 'errori', 'tabelle'
];
foreach ( $_conf as $_load ) {
    require('./core/conf/' . $_load . '.conf.php');
}

/* Creo hash database */
$conf['db_hash'] = substr( md5($conf['database']['dns']), 2, 8) . ':';

/* Carico le librerie */
$_lib = ['sicurezza', 'stringhe', 'pagine'];
foreach ( $_lib as $_load ) {
    require('./core/inc/'. $_load .'.lib.php');
}


/* Imposto l'autoloader della classe */
spl_autoload_register(function($_class) { 
    if ( is_readable ( './core/class/' . $_class . '.class.php' ) ) {
        require('./core/class/' . $_class . '.class.php');
        return true;
    } else {
        return false;
    }
});

/* Mostra errori */
ini_set('display_errors', (int) $conf['debug']);
error_reporting(E_ALL ^ E_NOTICE);

/* Imposta il timezone */
date_default_timezone_set($conf['timezone']);

/* Connetto alla cache */
if (class_exists('Memcached') ) {
    $cache = new Memcached();
    $cache->addServer('127.0.0.1', 11211);
}

/* Connetto al database */
$db = new PDO(
        $conf['database']['dns'],
        $conf['database']['user'],
        $conf['database']['pass'],
        [
            PDO::ATTR_PERSISTENT => 
                $conf['database']['persistent']
        ]
);

