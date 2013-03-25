<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');

?>


<?php
$form_id="elicao$id_licao"."_".$class;
?>

<form action="" id="<?php echo "$form_id" ?>" method="post" class="hform">
  <input type="hidden" name="target" value="licao">
  <input type="hidden" name="updater" value="<?php echo $updater ?>">
  <input type="hidden" name="id_licao" value="<?php echo $id_licao ?>">
</form>
<span class="licao">
  <a onclick="javascript:document.forms.<?php echo "$form_id" ?>.submit()" href="#" class="<?php echo $class ?>">
    <?php echo "$arrow" ?>
  </a>
</span>
