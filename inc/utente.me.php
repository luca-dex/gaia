<?php

/*
 * ©2012 Croce Rossa Italiana
 */

paginaPrivata();

if ( !$me->email ) { redirect('nuovaAnagraficaContatti'); }
if ( !$me->password && $sessione->tipoRegistrazione = VOLONTARIO ) { redirect('nuovaAnagraficaAccesso'); }

foreach ( $me->comitatiPresidenzianti() as $comitato ) {
    $p = $comitato->unPresidente();
    if ( $p && $p == $me->id && !$comitato->haPosizione() && !$comitato->principale ) {
        redirect('presidente.wizard&forzato&oid=' . $comitato->oid());
    }
}
/* Noi siamo cattivi >:) */
// redirect('curriculum');

$attenzione = false;

$rf = $me->attivitaReferenziateDaCompletare();
if ($rf) {
    $attenzione = true;
    $attivita = $rf[0];
    ?>

<div class="modal fade automodal">
        <div class="modal-header">
          <h3 class="text-error"><i class="icon-warning-sign"></i> Attività da completare</h3>
        </div>
        <div class="modal-body">
          <p><?php echo $me->nome; ?>, sei stato selezionato come referente per l'attività:</p>
          <hr />
          <p class="allinea-centro">
              <strong><?php echo $attivita->nome; ?></strong>
              <br />
              <?php echo $attivita->area()->nomeCompleto(); ?><br />
              <span class="muted">
              <?php echo $attivita->comitato()->nomeCompleto(); ?>
              </span>
          </p>
          <hr />
          <h4>Completa i dettagli dell'attività</h4>
          <p>Devi inserire le seguenti informazioni:</strong>
                  <ul>
                      <li><i class="icon-time"></i> Giorni e turni;</li>
                      <li><i class="icon-globe"></i> Locazione dell'attività;</li>
                      <li><i class="icon-pencil"></i> Informazioni per i volontari;</li>
                      <li><i class="icon-group"></i> A chi è aperta l'attività;</li>
                  </ul><br />
           </p>
          <p class="text-error">
             <i class="icon-info-sign"></i> Non appena verranno inseriti tutti
                  i dettagli riguardanti l'attività, questa comparirà sul calendario dei volontari.
                  Potranno così richiedere di partecipare attraverso Gaia.
          </p>
              
          </ul>
          
        </div>
        <div class="modal-footer">
          <a href="?p=attivita.gestione" class="btn">Non ora</a>
          <a href="?p=attivita.modifica&id=<?php echo $attivita->id; ?>" class="btn btn-primary">
              <i class="icon-asterisk"></i> Vai all'attività
          </a>
        </div>
</div>


<?php 
} 
if ( !$me->consenso() ){ ?>
  <div class="modal fade automodal">
    <div class="modal-header">
      <h3 class="text-success"><i class="icon-cog"></i> Aggiornamento condizioni d'uso di Gaia!</h3>
    </div>
    <div class="modal-body">
      <p>Ciao <strong><?php echo $me->nome; ?></strong>,</p>
      <p>Per migliorare il nostro servizio, apportiamo periodicamente dei cambiamenti alle condizioni d'uso.</p>
      <p>Gli ultimi aggiornamenti sono già disponibili sul nostro sito. Puoi consultarli in due modi: </p>
      <ul>
        <li>Leggi la pagina delle <a href="?p=public.privacy" target="_new"> <i>condizioni d'uso</i>;</a></li>
        <li>Apri una nuova finestra del browser. Digita www.gaiacri.it, clicca <i>informazioni</i> in fondo alla pagina e poi <i>condizioni d'uso</i>.</li>
      </ul>
      <p><strong>Cosa fare</strong></p>
      Ti consigliamo di leggere gli aggiornamenti alle condizioni d'uso perché contengono importanti informazioni.<br/>
      Se sei d'accordo con quanto riportato premi il pulsante "Accetto le condizioni d'uso".<br/>
      Le condizioni d'uso resteranno valide fino all'entrata in vigore della versione aggiornata.
      </p>Grazie per la fiducia,</br>
      Lo staff di Gaia</p>
    </div>
    <div class="modal-footer">
      <a href="?p=utente.privacy&first" class="btn btn-success">
        <i class="icon-ok"></i>
        Ok, Accetto!
      </a>
    </div>
  </div>
<?php } 

if(false && !$sessione->barcode) {?>

<div class="modal fade automodal">
  <div class="modal-header">
          <h3 class="text-error"><i class="icon-warning-sign"></i> Gaia ha bisogno di te!</h3>
  </div>
  <div class="modal-body">
    <p>Ciao <?php echo $me->nome; ?>, abbiamo bisogno del tuo aiuto per migliorare la qualità del servizio
    fornito da Gaia.</p>
    <p>Stiamo effettuando uno studio sull'uso dei dispositivi mobili (smartphone e tablet) da parte 
    dei Volontari che usano Gaia, con particolare riferimento all'uso della fotocamera 
    per la scansione dei codici a barre.</p>
    <p>Se hai una stampante ed uno smartphone o tablet, aiutaci nel nostro esperimento, 
    completando il questionario!</p>

    <p><i>Grazie della collaborazione</i><br />
    <i>Lo staff di Gaia</i><p>

    </div>
  <div class="modal-footer">
    <a class="btn btn-danger" href="?p=utente.barcode&no">
      Non sono interessato
    </a>
    <a class="btn btn-success" href="?p=utente.barcode&ok">
      Ok, ci sto!
    </a>
  </div>
</div>

<?php } ?>

<div class="row-fluid">
    
    <div class="span3"><?php menuVolontario(); ?></div>

    <div class="span9">
        
        <h2><span class="muted">Ciao, </span><?php if($me->presiede()){?><span class="muted">Presidente</span> <?php echo $me->nome;}else{echo $me->nome;} ?>.</h2>
        
        <?php if (isset($_GET['suppok'])) { $attenzione = true; ?>
        <div class="alert alert-success">
            <h4><i class="icon-ok-sign"></i> Richiesta supporto inviata</h4>
            <p>La tua richiesta di supporto è stata inviata con successo, a breve verrai contattato da un membro dello staff.</p>        
        </div> 
        <?php } ?>
        <?php if (isset($_GET['ok'])) { $attenzione = true;  ?>
        <div class="alert alert-success">
            <i class="icon-ok"></i> <strong>Mail inviata</strong>.
            La tua mail è stata inviata con successo.
        </div> 
        <?php } ?>
        <?php if (isset($_GET['mass'])) { $attenzione = true;  ?>
        <div class="alert alert-success">
            <i class="icon-ok"></i> <strong>Mail inviate</strong>.
            Mail di massa inviata con successo.
        </div> 
        <?php } ?>
        <?php if (!$me->wizard) { $attenzione = true;  ?>
        <div class="alert alert-block alert-error">
            <h4><i class="icon-warning-sign"></i> Completa il tuo profilo</h4>
            <p>Inserisci titoli, patenti, certificazioni e competenze dalla sezione curriculum.</p>        
            <p><a href="?p=utente.titoli&t=0" class="btn btn-large"><i class="icon-ok"></i> Clicca qui per iniziare</a></p>
        </div> 
        <?php } else { ?>
        <div class="alert alert-block alert-success">
            <div class="row-fluid">
                <span class="span7">
                    <h4><i class="icon-ok"></i> Grande, hai finito!</h4>
                    <p>Quando vorrai modificare qualcosa, clicca sul pulsante per ricominciare la procedura di Modifica curriculum.</p> 
                </span>
                <span class="span5">
                    <a href="?p=utente.titoli&t=0" class="btn btn-large">
                        <i class="icon-refresh"></i>
                        Ricominciamo
                    </a>
                </span>
            </div>
        </div>
        <?php } ?>
       
        <?php foreach ( $me->appartenenzePendenti() as $app ) { $attenzione = true;  ?>
        <div class="alert alert-block">
            <h4><i class="icon-time"></i> In attesa di conferma</h4>
            <p>La tua appartenenza a <strong><?php echo $app->comitato()->nomeCompleto(); ?></strong> attende conferma.</p>
            <p>Successivamente riceverai una email di notifica e potrai partecipare ai servizi del comitato.</p>
            
        </div>
        <?php } ?>
        <?php 
            $h=0;
            foreach ( $patenti =  TitoloPersonale::scadenzame($me)  as $patente ) { 
                if($h!=1){  ?>
        <div class="alert alert-error">
            <h4><i class="icon-warning-sign"></i> Patente in scadenza</h4>
            <p>La tua <strong>PATENTE CRI</strong> scadrà il <strong><?php echo date('d-m-Y', $patente->fine); ?></strong></p>
        </div>
        <?php $h=1;
               }
           } ?>
        <?php 
        
        if($me->inRiserva()){
          $r = Riserva::filtra([
              ['volontario', $me->id],
              ['stato', RISERVA_OK]
            ]);
          $r = $r[0];
          ?>

        <div class="alert alert-block">
            <h4><i class="icon-pause"></i> In riserva</h4>
            <p>Sei nel ruolo di riserva fino al  <strong><?php echo date('d/m/Y', $r->fine); ?></strong>.</p>
        </div>
        <?php } ?> 
        <?php   if ( $me->storico() && !$me->appartenenzePendenti() && $me->unComitato()->gruppi() ) { 
                        if (!$me->mieiGruppi()){ ?>
                                <div class="alert alert-danger">
                                    <div class="row-fluid">
                                         <span class="span7">
                                              <h4><i class="icon-group"></i> Non sei iscritto a nessun gruppo!</h4>
                                                  <p>Il tuo Comitato ha attivato i gruppi di lavoro, sei pregato di regolarizzare l'iscrizione ad un gruppo.</p>
                                         </span>
                                         <span class="span5">
                                             <a href="?p=utente.gruppo" class="btn btn-large">
                                                 <i class="icon-group"></i>
                                                     Iscriviti ora!
                                             </a>
                                         </span>
                                     </div>
                                </div>
        <?php }
                        } ?>
            
        <!-- Per ora mostra sempre... -->
        <div class="alert alert-block alert-info">
            <h4><i class="icon-folder-open"></i> Hai già caricato i tuoi documenti?</h4>
            <p>Ricordati di caricare i tuoi documenti dalla sezione <strong>Documenti</strong>.</p>
        </div>
    </div>
</div>

<?php
if ( !$attenzione && $me->comitatiDiCompetenza() ) {
    redirect('presidente.dash');
}
?>