<?php
/**
 * @file licao.php
 *
 * @brief Conten as operacoes de update/inser/del da tabela licoes
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
extract($_POST);

/* para que haja um update, a licao tem que pertencer ao professor */
if ($id_licao && ProfClass::belongs('licao', $id_licao))
{
  list($op, $up) = sscanf($updater,"%c%d");
  
  switch ($op)
  {
    case 'S': Licao::mod_status($up,$id_licao,$status);
	      break;
    case 'D': echo "Alterar Dia em $up";
	      break;
    case 'E': Licao::mod_entrega($up,$id_licao);
	      break;
    case 'C': Licao::mod_status($up,$id_licao);
  }
}
/* quando a licoa nao pertence ao professor, temos que analisar se
 o capitulo e aluno pertencem para criarmos uma nova licao */
else if (!$id_licao && ProfClass::belongs('capitulo', $id_capitulo) && ProfClass::belongs('aluno', $id_aluno))
{
  $status = 'PD';
  Licao::create_licao($id_aluno,$id_capitulo,$id_aula,$status);
}
?>
