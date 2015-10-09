<?php

/*
 * ©2012 Croce Rossa Italiana
 */

class PartecipazioneBase extends Entita {

    protected static
        $_t  = 'partecipazioniBase',
        $_dt = 'datiPartecipazioniBase';

    use EntitaCache;

    public function utente() {
        return Utente::id($this->volontario);
    }
    
    public function corsoBase() {
        return CorsoBase::id($this->corsoBase);
    }
    
    public function organizzatore() {
        return $this->corsoBase->organizzatore();
    }
    
    public function confermata() {
        return (bool) $this->stato == ISCR_CONFERMATA;
    }

    public function cancella() {
        AssenzaLezione::cancellaTutti([['utente', $this->utente()]]);
        parent::cancella();
    }

    public function attiva() {
        if ((int) $this->stato >= ISCR_RICHIESTA)
            return true;
        return false;
    }

    public function concedi($com = null, $operatore=null, $quota=0.0) {
        if(!$operatore) {
            global $sessione;
            $operatore = $sessione->utente();
        }
        $u = $this->utente();
        if($this->aggiorna(ISCR_CONFERMATA, $operatore)) {

            if($com && !$u->appartenenzaAttuale()){
                $a = new Appartenenza();
                $a->volontario  = $this->volontario;
                $a->comitato    = $com;
                $a->inizio      = time();
                $a->fine        = PROSSIMA_SCADENZA;
                $a->timestamp   = time();
                $a->stato       = MEMBRO_CORSO_BASE;
                $a->conferma    = $operatore;
            }

            /* generazione della quota*/
            if($quota > 0) {
                $a = $u->ultimaAppartenenza(MEMBRO_CORSO_BASE);
                $q = new Quota();
                $q->appartenenza    = $a;
                $q->timestamp       = time();
                $q->tConferma       = time();
                $q->pConferma       = $operatore;
                $q->anno            = date('Y');
                $q->assegnaProgressivo();
                $q->quota           = $quota;
                $q->causale         = "Iscrizione corso di formazione per Volontari della Croce Rossa Italiana";

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
                $l->_CHINOME    = $operatore->nomeCompleto();
                $l->_CHICF      = $operatore->codiceFiscale;
                $f = $l->salvaFile($a->comitato());

                // invio email
                $m = new Email('corsoBaseQuota', "Pagata quota iscrizione al Corso Base" );
                $m->a               = $u;
                $m->da              = $operatore;
                $m->_NOME           = $u->nome;
                $m->allega($f);
                $m->invia();
            }


            return true;
        }
        return false;
    }
    
    public function nega($operatore=null) {
        return $this->aggiorna(ISCR_NEGATA, $operatore);
    }

    public function aggiorna( $s = ISCR_CONFERMATA, $operatore=null ) {
        if(!$operatore) {
            global $sessione;
            $operatore = $sessione->utente();
        }
        if($this->stato == ISCR_RICHIESTA){
            $this->stato = (int) $s;
            $this->pConferma = $operatore;
            $this->tConferma = time();
            return true;
        }
        return false;
    }

    public function haConclusoCorso() {
        return $this->promosso() || $this->bocciato();
    }

    public function promosso() {
        return $this->stato == ISCR_SUPERATO;
    }

    public function bocciato() {
        return $this->stato == ISCR_BOCCIATO;
    }
}
