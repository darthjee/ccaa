<?php
/**
 * @file turma_class.php
 * @brief Classe @ref Turma
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

require_once('aula_class.php');

/**
 * @brief Classe que controla e representa uma linha
 * da tablea alunos
 */
class Turma
{
  var $id_turma;
  var $turma;
  var $id_filial;
  var $ano;
  var $semestre;
  var $ativa;

  var $BD;

  var $Aulas;

  /**
   * Cria uma instancia de $ref Turma
   */
  function Turma($id_turma=null, $BDObj=null)
  {
    global $CASession;
    global $CCAA;

    if ($id_turma)
      $this->id_turma = $id_turma;
    else
    {
      $this->id_turma = $CASession->id_turma;
      $id_turma = $this->id_turma;
    }

    if ($BDObj)
      $this->BD =& $BDObj;
    else
    {
      $this->BD =& $CCAA;
      $BDObj = $this->BD;
    }

    $query = "select * from turmas where id_turma = $id_turma and id_turma in ";
    $query.= "(select id_turma from professores_turmas where id_professor=".$CASession->id_professor.")";
    $turma = $BDObj->query_to_table($query);

    if ($turma)
      foreach ($turma[0] as $campo => $valor)
      {
	$this->$campo = $valor;
      }
  }

  /**
   * @brief gera uma tabela contendo a lista de aulas
   * de uma turma;
   *
   * @see lista_aulas
   */
  function get_aulas($start, $n, $id_turma=null)
  {
    global $CCAA;
    global $CASession;

    if (!$id_turma)
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }

    $query = "select ";
    $query.= "id_aula, dia, aulas ";
    $query.= "from aulas where id_turma=$id_turma ";
    $query.= "order by dia desc limit $start,$n ";

    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $Aulas = $BD->query_to_table($query);

    return $Aulas;
  }

  /**
   * @brief lista as aulas de uma turma
   * 
   * @param $start : Marca o inicio da lista de aulas (SQL)
   * @param $n : numero de aulas listadas
   * @para $id_turma : id da turma a ser listada
   */
  function ls_aulas($start, $n, $id_turma=null)
  {
    global $PGconf;
    global $CASession;
    global $CAconf;
    $Aulas;
    /* @brief Flag utilizado para chamar corretamente
       lista_aulas.php*/
    $LSAULAF=true;

    /* caso o objeto tenha sido instanciado e id_turma noa definido,
     entao este sera recuperado do objeto*/
    if (!$id_turma)
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }

    /* Primeiramente, deve se inicializar a lista de Aulas ou
     pega-la de uma lista pronta (do objeto)*/
    if (property_exists($this, 'Aulas') && $this->Aulas && $this->id_turma == $id_turma)
      $Aulas = $this->$Aulas;
    else
    {
      $Aulas = Turma::get_aulas($start, $n, $id_turma);
      if (property_exists($this, 'Aulas') && $this->id_turma == $id_turma)
	$this->Aulas = $Aulas;
    }


    /* impressao da tabela de aulas */
    foreach($Aulas as $aula)
    {
      /* variavel auxiliar de controle do css par e impar */
      if ($oddeven == 'par')
	$oddeven = 'impar';
      else
	$oddeven = 'par';

      extract($aula);
      /* impressao de uma aula */
      include($PGconf['basedir']."/templates/lista_aula.php");
    }
  }
  
  /**
   * @brief Checa se um dado aluno/aula...etc pertence
   * a uma dada turma;
   * @param $tipo : tipo de objeto (aluno, turma, etc..)
   * @param $valor : id do objeto a ser analizado
   * @param $id_turma : id da turma (pode ser omitido)
   * @see ProfClass::belongs
   */
  function belongs($tipo, $valor, $id_turma=null)
  {
    global $CCAA;
    global $CASession;

    if (!$id_turma && property_exists($this, 'id_turma'))
      $id_turma = $this->id_id_turma;
    else if (!$id_turma)
      $id_turma = $CASession->id_turma;

    /* formacao da query caso a caso */
    switch ($tipo)
    {
      case 'aluno' : $query = "select id_turma from alunos_turmas where ";
		     $query.= "id_turma=$id_turma and id_aluno = $valor";
		     break;
      case 'aula' : $query = "select id_turma from aulas where ";
		    $query.= "id_aula=$valor and id_turma=$id_turma";
		     break;
      case 'capitulo' : $query = "select id_turma from capitulos where ";
		    $query.= "id_capitulo=$valor and id_turma=$id_turma";
		     break;
      default : $query = false;
    }

    if ($query)
    {
      if (property_exists($this,'BD'))
	$BD =& $this->BD;
      else
	$BD =& $CCAA;

      if ($BD->query_to_val($query) == $id_turma)
	return true;
    }

    return false;
  }

  /**
   * @brief gera uma tabela contendo a lista de
   * capitulos de uma turma;
   *
   * @see Turma::get_aulas
   */
  function get_cap($id_turma=null)
  {
    global $CCAA;
    global $CASession;

    if (!$id_turma)
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }

    $query = "select ";
    $query.= "id_capitulo, capitulo, completado ";
    $query.= "from capitulos where id_turma=$id_turma ";
    $query.= "order by capitulo asc";

    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $Caps = $BD->query_to_table($query);

    return $Caps;
  }

  /**
   * @brief Busca por uma aula na lista de aulas
   * com uma posicao exata
   *
   * Passado $n negativo, sera gerada a lista na ordem
   * decrescente
   *
   * @param $n : ordem (pode ser negativo)
   * @param $id_turma id (pk) da turma a ser analisada
   */
  function get_id_aula_pos($n, $id_turma=null)
  {
    global $CCAA;
    global $CASession;

    /* busca pelo id_turma*/
    if (!$id_turma)
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }
    
    /* checagem da ordem dos dados */
    if ($n > 0)
      $order = " asc limit $n,1 ";
    else
    {
      $n=-1-$n;
      $order = " desc limit $n,1 ";
    }

    /* checagem da utilizacao do banco de dados */
    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $query = "select id_aula from aulas where id_turma=$id_turma order by dia $order";
    $id_aula = $BD->query_to_val($query);

    return $id_aula;
  }

  /**
   * @brief seleciona os capitulos de uma turma
   */
  function get_capitulos($id_turma=null)
  {
    global $CCAA;
    global $CASession;

    /* busca pelo id_turma*/
    if (!$id_turma)
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }

    $query = "select ";
    $query.= "capitulo, id_capitulo,completado ";
    $query.= "from capitulos where id_turma=$id_turma";

    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $Capitulos = $BD->query_to_table($query);

    return $Capitulos;
  }
}

?>
