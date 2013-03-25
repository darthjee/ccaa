<?php

/**
 * @file licao_class.php
 * @brief Classe @ref Licao
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
/**
 * @brief Classe que controla e representa
 * uma linha da tabela licoes
 */
class Licao
{

  var $BD;

  /**
   * @brief cria uma instancia de @ref Licao
   */
  function Licao($id_aula=null, $id_aluno=null, $BDObj=null)
  {
    if ($id_aula)
      $this->id_aula = $id_aula;
    else
    {
      $this->id_aula = $CASession->id_aula;
      $id_aula = $this->id_aula;
    }

    if ($id_aluno)
      $this->id_aluno = $id_aluno;
    else
    {
      $this->id_aluno = $CASession->id_aluno;
      $id_aluno = $this->id_aluno;
    }

    if ($BDObj)
      $this->BD =& $BDObj;
    else
    {
      $this->BD =& $CCAA;
      $BDObj =& $this->BD;
    }

    $query = "select * from licoes where id_aluno = $id_luno and id_aula=$id_aula limit 1";
    $licao = $BDObj->query_to_table($query);

    foreach ($licao[0] as $campo => $valor)
      $this->$campo = $valor;
  }

  /**
   * @brief Cria uma nova licao sem
   * inserir no banco
   */
  function fake_licao($id_capitulo, $id_aluno=null, $id_aula=null, $status=null, $entregue=null)
  {
    global $CASession;

    if (!$id_aluno)
      $id_aluno=$CASession->id_aluno;
    if (!$id_aula)
      $id_aula = Turma::get_id_aula_pos(-1);

    if (!$status)
      $status = 'NP';

    if(!$entregue)
      $entregu='N';

    $Licao['id_licao'] = 0;
    $Licao['id_capitulo'] = $id_capitulo;
    $Licao['id_aluno'] = $id_aluno;
    $Licao['id_aula'] = $id_aula;
    $Licao['status'] = $status;
    $Licao['entregue'] = $entregue;

    return $Licao;
  }

  /**
   * @brief pega um campo da tabela licoes
   * 
   * Caso o campo esteja definido e os id_capitulo
   * e id_aluno sejam os mesmos valores
   * do objeto, o campo é retornado apartir do objeto,
   * caso contrario, o campo sera buscado na tabela
   */
  function get_campo($id_licao, $campo)
  {
    global $CCAA;

    /* caso Aluno tenha sido instanciado e tenha id_aluno igual,
     o campo sera retornadao partindo da instancia */
    if (property_exists($this,$campo) && $this->id_licao == $id_licao)
      $value=$this->$campo;
    /* Caso seja necessario, o campo sera buscado do banco de dados */
    else
    {
      $query = "select $campo from licoes where id_licao = $id_licao limit 1";

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
  function status_link($updater, $str, $id_licao=null, $id_aluno=null, $id_capitulo=null, $status=null, $id_aula=null)
  {
    global $PGconf;

    /* Busca dos parametros */
    if (!isset($id_licao))
    {
      if(property_exists($this, 'id_licao'))
	$id_licao = $this->id_licao;
    }
    if (!isset($id_aluno))
    {
      if(property_exists($this, 'id_aluno') && $this->id_licao == $id_licao)
	$id_aluno = $this->id_aluno;
      else
	$id_aluno = Licao::get_campo($id_licao, 'id_aluno');
    }
    if (!isset($id_capitulo))
    {
      if(property_exists($this, 'id_capitulo') && $this->id_licao == $id_licao)
	$id_capitulo = $this->id_capitulo;
      else
	$id_capitulo = Licao::get_campo($id_licao, 'id_capitulo');
    }

    if (!isset($status))
    {
      if(property_exists($this, 'status') && $this->id_licao == $id_licao)
	$status = $this->status;
      else
	$status = Licao::get_campo($id_licao, 'status');
      if (!isset($status))
	$status='NP';
    }
    if (!isset($id_aula))
    {
      if(property_exists($this, 'id_aula') && $this->id_licao == $id_licao)
	$id_aula = $this->id_aula;
      else
	$id_aula = Licao::get_campo($id_licao, 'entr_id_aula');
    }

    /* apos terem sido setadas as variaveis, eh hora de imprimir o link */
    include($PGconf['basedir']."/templates/status_licao.php");
  }

  /**
   * @brief cria uma linha na tabela licoes
   */
  function create_licao($id_aluno, $id_capitulo, $id_aula=null, $status=null)
  {
    global $CCAA;

    if (!$id_aula)
      $id_aula = Turma::get_id_aula_pos(-1);
    if (!isset($status))
      $status = 'PD';

    $query = "insert into licoes set id_aluno=$id_aluno, id_capitulo=$id_capitulo, ";
    $query.= "entr_id_aula=$id_aula, status='$status'";

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $id_licao = $BD->insert($query);
    Licao::fix_entr_licao($id_licao);

    return $id_licao;
  }

  /**
   * @brief modifica o estatus de um licao
   */
  function mod_status($up, $id_licao=null, $status=null)
  {
    global $CCAA;

    if (!isset($id_licao))
    {
      if(property_exists($this, 'id_licao'))
	$id_licao = $this->id_licao;
    }
    if (!isset($status))
    {
      if(property_exists($this, 'status') && $this->id_licao == $id_licao)
	$status = $this->status;
      else
	$status = Licao::get_campo($id_licao, 'status+0 as status');
    }

    if (property_exists($this, 'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* para se trabalhar com o status, eh nescessario que ele seja numerico */
    if (!is_numeric($status))
    {
      $query = "select distinct status+0 from licoes where status = '$status'";
      $status = $BD->query_to_val($query);
    }

    $status = (($status+5+$up)%6)+1;

    $update = "update licoes set status=$status where id_licao = $id_licao limit 1";
    $BD->update($update);
    Licao::fix_entr_licao($id_licao);

  }

  /**
   * @brief Arruma o campo entregue de uma licao
   */
  function fix_entr_licao($id_licao=null, $status=null)
  {
    global $CCAA;

    if (!isset($id_licao))
    {
      if(property_exists($this, 'id_licao'))
	$id_licao = $this->id_licao;
    }
    if (!isset($status))
    {
      if(property_exists($this, 'status') && $this->id_licao == $id_licao)
	$status = $this->status;
      else
	$status = Licao::get_campo($id_licao, 'status+0 as status');
    }

    if (property_exists($this, 'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* para se trabalhar com o status, eh nescessario que ele seja numerico */
    if (!is_numeric($status))
    {
      $query = "select distinct status+0 from licoes where status = '$status'";
      $status = $BD->query_to_val($query);
    }

    if ($status < 4)
      $entr = 'N';
    else
      $entr = 'S';

    $update = "update licoes set entregue='$entr' where id_licao = $id_licao limit 1";
    $BD->update($update);
  }


  /**
   * @brief faz um link para a alteracao de uma licao
   */
  function aula_link($id_licao=null, $capitulo=null)
  {
    global $PGconf;
    global $CCAA;

    /* Busca dos parametros */
    if (!isset($id_licao))
    {
      if(property_exists($this, 'id_licao'))
	$id_licao = $this->id_licao;
    }
    if (!isset($capitulo))
    {
      $id_capitulo = Licao::get_campo($id_licao,'id_capitulo');
      $capitulo = Capitulo::get_campo($id_capitulo,'capitulo');
    }

    if (property_exists($this, 'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $query = "select distinct status+0 from licoes where id_licao=$id_licao";
    $status = $BD->query_to_val($query);

    $arrow="&lt;";
    $updater="E-1";
    $class='good';
    include($PGconf['basedir']."/templates/arrow_licao.php");

    if (($status-1)%3==0)
      $updater="C02";
    else
      $updater="C-1";

    Licao::status_link($updater, $capitulo, $id_licao);

    $arrow="&gt;";
    $updater="E01";
    $class='bad';
    include($PGconf['basedir']."/templates/arrow_licao.php");
  }

  /**
   * @brief modifica data e status de entrega de uma licao[
   * @param $mod : modificador (positivo atrasa, negativo entrega)
   * @param $id_licao : id(pk) da licao
   */
  function mod_entrega($mod=null, $id_licao=null)
  {
    if (!isset($id_licao))
    {
      if(property_exists($this, 'id_licao'))
	$id_licao = $this->id_licao;
    }
    $status = Licao::get_campo($id_licao,'status+0');
    if (!isset($mod))
    {
      if ($status > 3)
	$mod = 1;
      else
	$mod = -1;
    }

    /*preciso melhorar este trecho*/
    if ($mod > 0 && $status > 3)
      $up = $mod+2;
    else if ($mod > 0 && $status < 3 && $status > 0)
      $up = $mod;
    else if ($mod < 0 && $status < 4)
      $up = 4+$mod;
    else if ($mod < 0 && $status > 4 && $status < 7)
      $up = $mod;
    else $up=0;

    Licao::mod_status($up, $id_licao, $status);
    if (($mod < 0 && $status > 3) || ($mod > 0 && $status < 4))
    Licao::mod_data($mod, $id_licao);
  }

  
  /**
   * @brief Modifica a data de uma licao
   */
  function mod_data($up, $id_licao=null)
  {
    global $CCAA;

    if (!isset($id_licao))
    {
      if(property_exists($this, 'id_licao'))
	$id_licao = $this->id_licao;
    }

    if (property_exists($this, 'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $id_aula = Licao::get_campo($id_licao, 'entr_id_aula');
    $data = Aula::get_campo($id_aula, 'dia');
    $id_turma = Aula::get_campo($id_aula, 'id_turma');
    $limit = abs($up+0)+1;

    /* busca da nova data */
    $query = "select id_aula from aulas where id_turma=$id_turma and ";
    if ($up > 0)
      $query.= "dia >= '$data' order by dia asc ";
    else
      $query.= "dia <= '$data' order by dia desc ";
    $query.="limit $limit";

    /* busca da nova data */
    $Aulas = $BD->query_to_table($query);
    $aula = array_pop($Aulas);
    $new_id_aula = $aula['id_aula'];

    /* atualizacao da data */
    $update = "update licoes set entr_id_aula=$new_id_aula where id_licao=$id_licao limit 1";

    $BD->update($update);
  }
}
?>
