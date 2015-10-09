<?php

/*
* ©2014 Croce Rossa Italiana
*/

controllaParametri(['id', 'corso'], 'presidente.iscritti&err');
paginaPrivata();

$u = Utente::id($_GET['id']);
$corso = CorsoBase::id($_GET['corso']);

proteggiDatiSensibili($u, [APP_SOCI, APP_PRESIDENTE]);
paginaApp([APP_SOCI , APP_PRESIDENTE]);
if($u->partecipazioniBase(ISCR_CONFERMATA)) {
    redirect('presidente.iscritti&gia');
}

if(!CorsoBase::id($corso)) {
    redirect('presidente.iscritti&err');
}



$p = new PartecipazioneBase();
$p->volontario = $u;
$p->corsoBase = $corso;
$p->stato = ISCR_RICHIESTA;
$p->timestamp = time();
$p->concedi();

$u->stato = ASPIRANTE;

// registrazione quota

/* generazione della quota*/
$quota = (float) $_GET['quota'];
$quota = round($quota, 2);

if($quota > 0) {
    $a = $u->ultimaAppartenenza(MEMBRO_CORSO_BASE);
    $q = new Quota();
    $q->appartenenza    = $a;
    $q->timestamp       = time();
    $q->tConferma       = time();
    $q->pConferma       = $me;
    $q->anno            = date('Y');
    $q->assegnaProgressivo();
    $q->quota           = $quota;
    $q->causale         = "Iscrizione corso di formazione per Volontari della Croce Rossa Italiana. Codice corso: {$corso->progressivo()}.";

    // generazione pdf
    $l = new PDF('ricevutaquota', 'ricevuta.pdf');
    $l->_COMITATO   = $a->comitato()->locale()->nomeCompleto();
    $l->_ID         = $q->progressivo();
    $l->_NOME       = $u->nome;
    $l->_COGNOME    = $u->cognome;
    $l->_FISCALE    = $u->codiceFiscale;
    $l->_NASCITA    = date('d/m/Y', $u->dataNascita);
    $l->_LUOGO      = $u->luogoNascita;
    $l->_IMPORTO    = soldi($q->quota);
    $l->_QUOTA      = $q->causale;
    $l->_OFFERTA    = '';
    $l->_OFFERIMPORTO = '';
    $l->_TOTALE     = soldi($quota->quota);
    $l->_LUOGO      = $a->comitato()->locale()->comune;
    $l->_DATA       = $q->dataPagamento()->format('d/m/Y');
    $l->_CHINOME    = $me->nomeCompleto();
    $l->_CHICF      = $me->codiceFiscale;
    $f = $l->salvaFile($a->comitato());

    // invio email
    $m = new Email('corsoBaseQuota', "Pagata quota iscrizione al Corso Base" );
    $m->a               = $u;
    $m->da              = $me;
    $m->_NOME           = $u->nome;
    $m->allega($f);
    $m->invia();
}






redirect('presidente.iscritti&iscritto');

