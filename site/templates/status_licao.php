<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');

?>


<?php
$form_id="slicao$id_licao"."_".$updater[0];
?>

<form action="" id="<?php echo "$form_id" ?>" method="post" class="hform">
  <input type="hidden" name="target" value="licao">
  <input type="hidden" name="updater" value="<?php echo $updater ?>">
  <input type="hidden" name="id_licao" value="<?php echo $id_licao ?>">
  <input type="hidden" name="id_aluno" value="<?php echo $id_aluno ?>">
  <input type="hidden" name="id_capitulo" value="<?php echo $id_capitulo ?>">
  <input type="hidden" name="status" value="<?php echo $status ?>">
  <input type="hidden" name="id_aula" value="<?php echo $id_aula ?>">
</form>
<span class="licao">
  <a onclick="javascript:document.forms.<?php echo "$form_id" ?>.submit()" href="#" class="<?php echo $status?>">
    <?php echo $str ?>
  </a>
</span>
