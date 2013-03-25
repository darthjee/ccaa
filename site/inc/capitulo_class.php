<?php

/**
 * @file capitulos_class.php
 * @brief Classe @ref Capitulo
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
/**
 * @brief Classe que controla e representa
 * uma linha da tabela capitulos
 */
class Capitulo
{

  var $BD;

  /**
   * @brief cria uma instancia de @ref Licao
   */
  function Capitulo($id_capitulo=null, $BDObj=null)
  {
    if ($id_capitulo)
      $this->id_capitulo = $id_capitulo;
    else
    {
      $this->id_capitulo = $CASession->id_capitulo;
      $id_capitulo = $this->id_capitulo;
    }

    if ($BDObj)
      $this->BD =& $BDObj;
    else
    {
      $this->BD =& $CCAA;
      $BDObj =& $this->BD;
    }

    $query = "select * from capitulos where id_capitulo = $id_capitulo limit 1";
    $Capitulo = $BDObj->query_to_row($query);

    foreach ($Capitulo as $campo => $valor)
      $this->$campo = $valor;
  }

  /**
   * @brief pega um campo da tabela capitulos
   */
  function get_campo($id_capitulo, $campo)
  {
    global $CCAA;

    /* caso o capitulo tenha sido instanciado corretamente,
     buscaremos os dados do objeto*/
    if (property_exists($this,$campo) && $this->id_capitulo == $id_capitulo)
      $value=$this->$campo;
    /* Caso seja necessario, o campo sera buscado do banco de dados */
    else
    {
      $query = "select $campo from capitulos where id_capitulo=$id_capitulo limit 1";

      if ($this->BD)
	$BD =& $this->BD;
      else
	$BD =& $CCAA;

      $value = $BD->query_to_val($query);
    }

    return $value;
  }


  /**
   * @brief Gera um link para fazer um update da licao
   * @see Aluno::link
   */
  function cap_aula_link($id_capitulo=null, $capitulo=null, $id_aula=null)
  {
    global $PGconf;
    global $CASession;

    /* Busca dos parametros */
    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_capitulo == $id_capitulo)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = $CASession->id_capitulo;
    }

    if (!isset($id_aula))
      $id_aula = $CASession->id_aula;

    if (!isset($capitulo))
    {
      if(property_exists($this, 'capitulo') && $this->capitulo == $capitulo)
	$capitulo = $this->capitulo;
      else
	$capitulo = Capitulo::get_campo($id_capitulo, 'capitulo');
    }

    $status = Capitulo::get_relation($id_capitulo, $id_aula);

    /* apos terem sido setadas as variaveis, eh hora de imprimir o link */
    include($PGconf['basedir']."/templates/link_aula_cap.php");
  }

  /**
   * @brief pega a relacao entre uma aula e um capitulo
   * (N S I) (N - Nao completado, S - Completada, I - Inexistente)
   */
  function get_relation($id_capitulo=null, $id_aula=null)
  {
    global $CCAA;
    global $CASession;

    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_capitulo == $id_capitulo)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = $CASession->id_capitulo;
    }
    if (!isset($id_aula))
      $id_aula = $CASession->id_aula;

    $query = "select completado from aulas_capitulos where ";
    $query.= "id_capitulo =$id_capitulo and id_aula=$id_aula limit 1";

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $rel =$BD->query_to_val($query);

    if (!$rel)
      $rel = 'I';

    return $rel;
  }

  /**
   * @brief Cria uma linha na tabela capitulos_aulas
   */
  function create_aula($id_capitulo=null, $id_aula=null)
  {
    global $CCAA;
    global $CASession;
    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_capitulo == $id_capitulo)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = $CASession->id_capitulo;
    }
    if (!isset($id_aula))
      $id_aula = $CASession->id_aula;

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $insert = "insert into aulas_capitulos set id_capitulo=$id_capitulo, id_aula=$id_aula, completado='N'";
    $BD->insert($insert);
  }

  /**
   * @brief marca o capitulo como completado naquela aula
   */
  function complete($id_capitulo=null, $id_aula=null)
  {
    global $CCAA;
    global $CASession;
    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_capitulo == $id_capitulo)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = $CASession->id_capitulo;
    }
    if (!isset($id_aula))
      $id_aula = $CASession->id_aula;

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* completa a licao */
    $update = "update capitulos set completado='S' where id_capitulo=$id_capitulo limit 1";
    $BD->update($update);
    /* ja cria as licoes dos alunos */
    Capitulo::create_licoes($id_capitulo);

    /* quando temos id_aula, devemos forcar o capitulo a ser fechada naquela aula */
    if ($id_aula)
    {
      /* forca nao haver aula onde o capitulo foi completado */
      $update = "update aulas_capitulos set completado='N' where id_capitulo=$id_capitulo";
      $BD->update($update);

      /* da um update setando o capitulo como completado naquela aula */
      $update = "update aulas_capitulos set completado='S' where id_capitulo=$id_capitulo and id_aula=$id_aula";
      $BD->update($update);
      /* caso o update nao tenha funcionado, a linha eh inserida */
      if (!$BD->Affected)
      {
        $insert = "insert into aulas_capitulos set completado='S', id_capitulo=$id_capitulo, id_aula=$id_aula";
        $BD->update($insert);
      }
    }
  }


  /**
   * @brief destroi a relacao aula - capitulo
   */
  function del_aula_caps($id_capitulo=null, $id_aula=null, $status=null)
  {
    global $CCAA;
    global $CASession;
    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_capitulo == $id_capitulo)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = $CASession->id_capitulo;
    }
    if (!isset($id_aula))
      $id_aula = $CASession->id_aula;

    if (!$status)
      $status = get_relation($id_capitulo, $id_aula);

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* status 'I' ja significa sem relacao */
    if ($status != 'I')
    {
      $delete = "delete from aulas_capitulos where ";
      $delete .="id_capitulo=$id_capitulo and id_aula=$id_aula and completado='$status' limit 1";
      $BD->delete($delete);

      /* torna o capitulo nao completado */
      if ($status == 'S')
      {
	$update = "update capitulos set completado='N' where id_capitulo=$id_capitulo limit 1";
	$BD->update($update);
      }
    }
  }


  /**
   * @brief cria as licoes para todos os alunos
   * que assisitiram o capitulo
   */
  function create_licoes($id_capitulo=null)
  {
    global $CCAA;
    global $CASession;

    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_capitulo == $id_capitulo)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = $CASession->id_capitulo;
    }
    $id_turma = Capitulo::get_campo($id_capitulo, 'id_turma');

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* busca de todos os alunos que nao tem faltas */
    $query = "select id_aluno from alunos_turmas where ";
    $query.= "id_turma=$id_turma and ";
    $query.= "id_aluno not in( ";
    $query.=   "select distinct id_aluno from faltas where id_aula in ";
    $query.=     "(select distinct id_aula from aulas_capitulos where id_capitulo=$id_capitulo)";
    $query.= ")";
    $Alunos = $BD->query_to_table($query);

    foreach ($Alunos as $aluno)
    {
      extract($aluno);
      Licao::create_licao($id_aluno, $id_capitulo);
    }
  }
}
?>
