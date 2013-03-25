<?php

/**
 * @file bd_prof_class.php
 * @brief Classe @ref ProfClass
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

require_once('bd_class.php');

/**
 * @brief Classe que controla e representa uma
 * linha da tabela professores
 */
class ProfClass
{
  var $id_professor = 0;
  var $nome = "";
  var $id_joomla = 0;
  /** Flag marcando se o professor eh
    * ou nao registrado */
  var $Guest = false;
  var $BD;

  var $Turmas = array();

  /**
   * @brief Cria uma instancia de @ref ProfClass
   */
  function ProfClass($id_joomla=null, $BDObjs=null)
  {
    global $User;
    global $CCAA;

    if($id_joomla)
      $this->id_joomla = $id_joomla;
    else
      $this->id_joomla = $User->id;

    if($BD)
      $this->BD =& $BDObj;
    else
      $this->BD =& $CCAA;

    $this->set_param_prof($this->id_joomla);
  }

  /**
   * @brief busca no banco pelos dados do professor
   */
  function set_param_prof($id_joomla)
  {
    $querystr = "select nome, id_professor from professores where id_joomla=$id_joomla limit 1";
    $prof = $this->BD->query_to_table($querystr);

    if ($this->BD->Rows)
    {
      foreach ($prof[0] as $campo => $row)
      {
        $this->$campo = $row;
      }
    }
    else
    {
      $this->Guest = true;
      return false;
    }
  }

  /**
   * @brief busca o id (primary key) do professor
   * @param $id_joomla : id_joomla do professor
   */
  function get_id($id_joomla=null)
  {
    global $User;
    global $CCAA;

    /* busca principal do id_joomla */
    if (!$id_joomla)
    {
      if (property_exists($this,'id_joomla'))
	$id_joomla = $this->id_joomla;
      /* busca do objeto joomla */
      else
	$id_joomla = $User->id;
    }

    $query = "select id_professor from professores where id_joomla = $id_joomla limit 1";

    /* definiçao do banco a ser usado */
    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $profs = $BD->query_to_table($query);

    $id_prof = $profs[0]['id_professor'];
    return $id_prof;
  }

  /**
   * @brief checa se um determinado id_joomla é professor
   */
  function is_prof($id_joomla=null)
  {
    global $User;
    global $CCAA;

    if (property_exists($this,'id_joomla'))
    {
      /* quando Guest = 0 && id_professor=0 tem
	 que ser buscado novamente */
      if (!$this->Guest && !$this->id_professor)
      {
        if (!$id_joomla)
	  $id_joomlai = $this->id_joomla;
        $this->set_param_prof($id_joomla);
      }
      else
	$return = !$this->Guest;
    }
    /* quando o objeto nao foi instanciado,
     o os ids deverao ser buscados do banco*/
    else
    {
      if (!$id_joomla)
	$id_joomla = $User->id;
      $querystr = "select id_professor from professores where id_joomla=$id_joomla limit 1";
      $return = $CCAA->check_query($querystr);
    }

    return $return;
  }

  /**
   * @brief pega a lista de turmas de um determinado professor
   *
   * @see Aula::get_faltas
   * @see Aluno::get_licoes
   * @see Turma::get_aulas
   */
  function get_turmas($id_prof=null)
  {
    global $CCAA;

    if (!$id_prof && property_exists($this, 'id_professor'))
      $id_prof = $this->id_professor;

    $querystr = "select ";
    $querystr.= " pt.id_turma as id_turma,";
    $querystr.= " pt.id_professor as id_professor,";
    $querystr.= " t.id_filial as id_filial,";
    $querystr.= " t.turma as turma,";
    $querystr.= " f.nome as filial,";
    $querystr.= " t.ano as ano,";
    $querystr.= " t.semestre as semestre";

    $querystr.= " from turmas as t,";
    $querystr.= " professores_turmas as pt,";
    $querystr.= " filiais as f";

    $querystr.= " where pt.id_turma=t.id_turma";
    $querystr.= " and t.id_filial=f.id_filial";
    $querystr.= " and pt.id_professor=$id_prof";
    $querystr.= " and t.ativa='S'";

    /* o banco deve ser utilizado do objeto ou do
     bancio padrao*/
    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $Turmas = $BD->query_to_table($querystr,true);
    return $Turmas;
  }

  /**
   * @brief Lista as turmas de um professor
   *
   * Para a listagem utiliza-se o template @ref lista_turma.php
   * @see Aula::ls_faltas
   * @see Aluno::ls_licoes
   * @see Turma::ls_aulas
   */
  function ls_turmas($id_prof=null)
  {
    global $PGconf;
    global $User;

    /* @brief Flag utilizado para chamar corretamente
       lista_turma.php*/
    $LSTURMAF=true;

    /* caso o resultado tenha sido obtido anteriormente */
    if (property_exists($this,'Turmas') && $this->Turmas && $this->id_professor ==  $id_prof)
      $Turmas = $this->Turmas;

    /* caso seja nescessario sera buscada do banco */
    if (!$Turmas)
    {
      /* quando nao foi passado o id_professor, este sera
       buscado do banco */
      if (!$id_prof)
      {
	if (property_exists($this,'id_professor'))
	  $id_prof = $this->id_professor;
	else
	  $id_prof = ProfClass::get_id();
      }
      $Turmas = ProfClass::get_turmas($id_prof);
    }

    /* chamada do template para as turmas */
    foreach($Turmas as $turma)
    {
      /* auxiliar de criaçao do css estriado */
      if ($oddeven == 'par')
	$oddeven = 'impar';
      else
	$oddeven = 'par';

      extract($turma);
      include($PGconf['basedir']."/templates/lista_turma.php");
    }
  }

  /**
   * @brief Checa se um dado aluno/turma...etc pertence
   * a um dado professor;
   * @param $tipo : tipo de objeto (aluno, turma, etc..)
   * @param $valor : id do objeto a ser analizado
   * @param $id_prof : id do professor
   */
  function belongs($tipo, $valor, $id_prof=null)
  {
    global $User;
    global $CCAA;

    if (!$id_prof && property_exists($this, 'id_professor'))
      $id_prof = $this->id_professor;
    else if (!$id_prof)
      $id_prof = ProfClass::get_id();

    /* formacao da query caso a caso */
    switch ($tipo)
    {
      case 'turma' : $query = "select id_professor from professores_turmas where ";
		     $query.= "id_turma = $valor and id_professor=$id_prof";
		     break;
      case 'aluno' : $query = "select id_professor from professores_turmas where id_professor=$id_prof";
		     $query.= " and id_turma in ";
		     $query.= "(select id_turma from alunos_turmas where id_aluno=$valor)";
		     break;
      case 'aula' :  $query = "select id_professor from professores_turmas where id_professor=$id_prof";
		     $query.= " and id_turma in ";
		     $query.= "(select id_turma from aulas where id_aula=$valor)";
		     break;
      case 'licao' : $query = "select id_professor from professores_turmas where id_professor=$id_prof";
		     $query.= " and id_turma in (select id_turma from capitulos where id_capitulo in";
		     $query.= "(select id_capitulo from licoes where id_licao=$valor))";
		     break;
      case 'capitulo':$query = "select id_professor from professores_turmas where id_professor=$id_prof";
		      $query.= " and id_turma in ";
		      $query.= "(select id_turma from capitulos where id_capitulo=$valor)";
		      break;
      default : $query = false;
    }

    if ($query)
    {
      if (property_exists($this,'BD'))
	$BD =& $this->BD;
      else
	$BD =& $CCAA;

      if ($BD->query_to_val($query) == $id_prof)
	return true;
    }

    return false;
  }

}

?>
