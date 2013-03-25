<?php
/**
 * @file data_aula.php
 * @brief template com a geracao do link-string
 * da data da aula
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');

?>


<?php
$form_id="aula$id_aula";
?>
<form action="" id="<?php echo "$form_id" ?>" method="post" class="hform">
  <input type="hidden" name="value" value="<?php echo $id_aula?>">
  <input type="hidden" name="campo" value="aula">
  <input type="hidden" name="info" value="aula">
  <input type="hidden" name="target" value="display">
</form>
<a onclick="javascript:document.forms.<?php echo "$form_id" ?>.submit()" href="#">
  <?php echo $dia ?>
</a>
