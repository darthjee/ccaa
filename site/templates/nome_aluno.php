<?php
/**
 * @file nome_aluno.php
 * @brief template com a geracao do link-nome do aluno
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');

?>


<?php
$form_id="aluno$id_aluno";
?>
<form action="" id="<?php echo "$form_id" ?>" method="post" class="hform">
  <input type="hidden" name="value" value="<?php echo $id_aluno?>">
  <input type="hidden" name="campo" value="aluno">
  <input type="hidden" name="info" value="aluno">
  <input type="hidden" name="target" value="display">
</form>
<a onclick="javascript:document.forms.<?php echo "$form_id" ?>.submit()" href="#">
  <?php echo $nome ?>
</a>
