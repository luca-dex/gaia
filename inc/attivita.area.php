<?php

/*
 * ©2013 Croce Rossa Italiana
 */

paginaPrivata();
$a = $_GET['id'];
$a = Attivita::id($a);

?>
<form action="?p=attivita.obiettivo.ok" method="POST">

    <div class="modal fade automodal">
        <div class="modal-header">
          <h3>Area di intervento</h3>
      </div>
      <div class="modal-body">
          <p>È importante conoscere l'area di intervento di un'attività.<br />Questo permetterà a Gaia di categorizzarla.</p>
          <hr />
          <p class="text-info"><i class="icon-info-sign"></i> Il presidente può creare le Aree dal pannello presidente.</p>
          <select name="inputArea" class="input-xxlarge">
              <?php foreach ( $a->comitato()->aree() as $area ) { ?>
              <option value="<?php echo $area->id; ?>"><?php echo $area->nomeCompleto(); ?></option>
              <?php } ?>
          </select>
          
      </div>
      <div class="modal-footer">
          <!-- <a href="?p=utente.me" class="btn">Annulla</a> -->
          <button type="submit" class="btn btn-primary">Assegna area</button>
      </div>
  </div>
</form>
