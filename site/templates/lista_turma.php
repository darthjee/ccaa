<?php
/**
 * @file lista_turma.php
 * @brief imprime a lista de turmas pertencentes ao professor
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');

?>


<?php
/* Quando o flag LSTURMAF nao e definido, o "header"
 * da lista eh criado */
if (!$LSTURMAF)
{
  ?>
    <table class="list">
      <?php ProfClass::ls_turmas(); ?>
    </table>
  <?php

}
/* Quando o flag eh definido (dentro de @ref PRofClass::ls_turmas),
 * as linhas individuais sao criadas */
else
{
  $form_id="turma$id_turma";
  ?>
  <tr class="<?php echo $oddeven?>">
   <td><?php echo $filial?></td>
   <td> --- </td>
   <td>
    <a onclick="javascript:document.forms.<?php echo "$form_id" ?>.submit()" href="#">
     <?php echo $turma ?>
    </a>
     <form action="" id="<?php echo "$form_id" ?>" method="post" class="hform">
     <input type="hidden" name="value" value="<?php echo $id_turma?>">
     <input type="hidden" name="campo" value="turma">
     <input type="hidden" name="target" value="display">
    </form>
   </td>
  <tr>
  <?php
}
?>
