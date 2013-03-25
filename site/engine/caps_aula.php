<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

extract($_POST);

if (!$id_caps || ProfClass::belongs('capitulo', $id_caps))
{
  if ($id_aula)
    $id_turma = Aula::get_campo($id_aula, 'id_turma');
  switch ($updater)
  {
    case 'cicle' : if (ProfClass::belongs('capitulo', $id_caps) && Turma::belongs('capitulo', $id_caps))
		   {
		     $status = Capitulo::get_relation($id_caps, $id_aula);
		     switch ($status)
		     {
		       case 'I' : Capitulo::create_aula($id_caps, $id_aula);
				  break;
		       case 'N' : Capitulo::complete($id_caps, $id_aula);
				  break;
		       case 'S' : Capitulo::del_aula_caps($id_caps, $id_aula, $status);
				  break;
		     }
		   }
		   break;;
  }
}
?>
