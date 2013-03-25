<?php
/**
 * @file lista_aula.php
 * @brief lista as aulas de uma turma
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');

?>

<?php
if (!$LSAULAF)
{
  ?>
    <!-- formulario de nova aula-->
    <div id="new_aula" style="display:none">
      <form id='fnew_aula' method="post" action="">
        <table>
	  <tr>
	    <th>Dia</th>
	    <th>Aulas</th>
	    <th>Last</th>
	    <td><a href="javascript:showHide('new_aula');">-</a></td>
	  </tr><tr>
	    <td>
	      <input size="8" name='dia' type="text" value="<?php echo Aula::new_data_aula(); ?>" />
	    </td><td>
              <input size="2" name='aulas' type="text" value="<?php echo Aula::get_campo(Turma::get_id_aula_pos(-1),'aulas'); ?>" />
	    </td><td>
              <input size="30" name='last' type="text" value="" />
	    </td><td>
	      <a href="javascript:document.forms.fnew_aula.submit();" href="#">:D</a>
	    </td>
	  </tr>
	</table>
	<input type="hidden" name="id_turma" value="<?php echo $CASession->id_turma ?>">
	<input type="hidden" name="target" value="aula">
	<input type="hidden" name="updater" value="new">
      </form>
    </div>
    <div id="new_aula_bt">
      <a href="javascript:showHide('new_aula');">+</a>
    </div>

    <!-- lista de aulas-->
    <table class="list">
      <th>Data</th><th>Faltas</th><th>L. Entregues</th><th>L. Pedidas</th>
      <?php Turma::ls_aulas(0,15)?>
    </table>
  <?php
}
else
{
  ?>
    <tr class="<?php echo $oddeven?>">
      <th>
        <div class='date'><?php Aula::link_aula($id_aula, $dia) ?></div>
	<div class='caps'>( <?php include($CAconf['basedir']."/templates/lista_caps.php"); ?>)</div>
      </th>
      <td><?php include($CAconf['basedir']."/templates/lista_faltas.php"); ?></td>
      <td><?php $entr="S"; include($CAconf['basedir']."/templates/lista_licoes_aula.php"); ?></td>
      <td><?php $entr="N"; include($CAconf['basedir']."/templates/lista_licoes_aula.php"); ?></td>
    <tr>
  <?php
}
