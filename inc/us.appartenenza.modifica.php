<?php

/*
 * ©2013 Croce Rossa Italiana
 */

paginaApp([APP_SOCI , APP_PRESIDENTE]);
$a = $_GET['a'];
$app = Appartenenza::by('id', $a);
$v = $app->volontario;
?>
<form action="?p=us.appartenenza.modifica.ok&a=<?php echo $a; ?>" method="POST">
<div class="modal fade automodal">
        <div class="modal-header">
          <h3>Modifica Appartenenza</h3>
        </div>
        <div class="modal-body">
          <div class="row-fluid">
                    <div class="span4 centrato">
                        <label for="dataInizio"><i class="icon-calendar"></i> Ingresso in CRI</label>
                    </div>
                    <div class="span8">
                        <input id="dataInizio" class="span12" name="dataInizio" type="text"  value="<?php echo date('d/m/Y', $app->inizio); ?>" required />
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span4 centrato">
                        <label for="dataFine"><i class="icon-time"></i> Scadenza</label>
                    </div>
                    <div class="span8">
                        <input id="dataFine" class="span12" name="dataFine" type="text"  value="<?php echo date('d/m/Y', $app->fine); ?>" required <?php if(!$me->admin()){ ?> readonly <?php } ?>/>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span4 centrato">
                        <label for="inputComitato"><i class="icon-home"></i> Comitato</label>
                    </div>
                    <?php if($me->admin()){ ?>
                        <div class="span8">
                            <select required name="inputComitato" id="inputComitato" class="span12">
                                <?php foreach ( Comitato::elenco('locale ASC') as $c ) { ?>
                                    <option value="<?php echo $c; ?>" <?php if ( $c == $app->comitato() ) { ?>selected<?php } ?>><?php echo $c->nomeCompleto(); ?></option>
                                <?php } ?>
                            </select>
                        </div>
               </div>
            <?php }else{ ?>
                    <div class="span8">
                        <input id="comitato" class="span12" name="comitato" type="text"  value="<?php echo $app->comitato()->nomeCompleto(); ?>" readonly />
                    </div>
               </div>
            <?php } ?>
        </div>
        <div class="modal-footer">
          <a href="?p=presidente.utente.visualizza&id=<?php echo $v; ?>" class="btn">Annulla</a>
          <button type="submit" class="btn btn-primary">
              <i class="icon-save"></i> Modifica
          </button>
        </div>
</div>
</form>
