<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
if (ProfClass::belongs($_POST['campo'],$_POST['value']))
{
  $CASession->inject($_POST['campo'],$_POST['value']);
  if ($_POST['info'] == $_POST['campo'])
    $CASession->inject('info',$_POST['campo']);
}

?>
