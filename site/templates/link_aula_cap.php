<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>


<?php $form_id="caps$id_capitulo"."_"."$id_aula" ?>
<span class='capitulo'>
  <form action="" id="<?php echo "$form_id" ?>" method="post" class="hform">
    <input type="hidden" name="target" value="caps_aula">
    <input type="hidden" name="updater" value="cicle">
    <input type="hidden" name="id_caps" value="<?php echo $id_capitulo ?>">
    <input type="hidden" name="id_aula" value="<?php echo $id_aula ?>">
    <input type="hidden" name="status" value="<?php echo $status ?>">
  </form>
  <a onclick="javascript:document.forms.<?php echo "$form_id" ?>.submit()" href="#" class='<?php echo $status ?>'>
    <?php echo $capitulo ?>
  </a>
</span>
