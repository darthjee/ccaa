<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
extract($_POST);

/* para se modificar a tabela aulas, eh nescessario ser proprietario
 das aulas ou da turma */
if (!$id_aula || ProfClass::belongs('aula', $id_aula))
{
  switch($updater)
  {
    case 'new': if (ProfClass::belongs('turma', $id_turma))
		  Aula::create_aula($dia, $id_turma, $aulas, $last);
		break;
  }
}
?>
