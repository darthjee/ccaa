<?php
/**
 * @file aluno_class.php
 *
 * @brief classe @ref Aluno
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

require_once('licao_class.php');
require_once('common.php');

/**
 * @brief Classe que controla e representa uma linha
 * da tabela alunos
 */
class Aluno
{
  var $id_aluno;
  var $nome;
  var $last;
  var $nasc;
  var $sexo;
  var $idade;
  var $tel;
  var $cel;
  var $work;
  var $email;

  var $BD;

  /**
   * @brief Cria uma instancia de Aluno
   */
  function Aluno($id_aluno=null, $BDObj=null)
  {
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

    $query = "select * from alunos where id_aluno = $id_aluno limit 1";
    $aluno = $BDObj->query_to_table($query);

    foreach ($aluno[0] as $campo => $valor)
      $this->$campo = $valor;
  }

  /**
   * @brief pega um campo da tabela aluno
   * 
   * Caso o campo esteja definido e o id_aluno seja o mesmo
   * do objeto, o campo é retornado apartir do objeto,
   * caso contrario, o campo sera buscado na tabela
   */
  function get_campo($id_aluno, $campo)
  {
    global $CCAA;

    /* caso Aluno tenha sido instanciado e tenha id_aluno igual,
     o campo sera retornadao partindo da instancia */
    if (property_exists($this,$campo) && $this->id_aluno == $id_aluno)
      $value=$this->$campo;
    /* Caso seja necessario, o campo sera buscado do banco de dados */
    else
    {
      $query = "select $campo from alunos where id_aluno = $id_aluno limit 1";
      if ($this->BD)
	$BD =& $this->BD;
      else
	$BD =& $CCAA;

      $alunos = $BD->query_to_table($query);
      $value = $alunos[0][$campo];
    }

    return $value;
  }

  /**
   * @brief imprime o html contendo o nome do aluno e o link
   * de exibicao
   *
   * O link e impresso atraves do template @ref nome_aluno.php
   */
  function link($id_aluno=null, $nome=null)
  {
    global $PGconf;
    global $CASession;

    /* Caso o bjeto tenha sido instanciado,
       e nao tenha sido fornecido um id_aluno, as
       informacoes vem dele */
    if (!$id_aluno)
    {
      if (property_exists($this,'id_aluno'))
	$id_aluno = $this->id_aluno;
      else
	$id_aluno = $CASession->id_aluno;
    }
    /* Caso o nome nao tenha sido informado,
     ele devera ser buscado do objeto ou do banco */
    if (!$nome)
    {
      /* quando o id_aluno confere com o do objeto, o nome vem do objeto */
      if (property_exists($this,'nome') && $this->id_aluno == $id_aluno)
        $nome = $this->nome;
      /* quando eh nescessario, o campo eh buscado */
      else
        $nome = Aluno::get_campo($id_aluno, 'nome');
    }
    
    include($PGconf['basedir']."/templates/nome_aluno.php");
  }

  /**
   * @brief lista as faltas de um aluno
   * @param $id_aluno : id (PK) do aluno
   * @param $id_turma : id_turma (PK) da turma
   * @see Aula::ls_faltas
   * @see Turma::ls_aulas
   */
  function ls_faltas($start, $n, $id_aluno=null, $id_turma=null)
  {
    global $PGconf;
    global $CASession;
    $Faltas;
    /* Flag utilizado para chamar corretamente
       lista_aulas.php*/
    $LSFALTAF=true;
    /* Flag utilizado para chamar corretamente
       lista_aulas.php para mostrar o cabecalho*/
    $HLSFALTAF=true;

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if (!$id_aluno)
    {
      if (property_exists($this,'id_aluno'))
	$id_aluno = $this->id_aluno;
      else
	$id_aluno = $CASession->id_aluno;
    }

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if (!$id_turma)
      $id_turma = $CASession->id_turma;

    $Faltas = Aluno::get_faltas($start, $n, $id_aluno, $id_turma);

    /* a impressao é feita usando o template lista_faltas.php */
    if ($Faltas)
    {
      foreach($Faltas as $falta)
      {
	extract($falta);
	$dia = vertical_day($dia);
        include($PGconf['basedir']."/templates/lista_faltas_aluno.php");
      }
      ?></tr><tr><?php
      $HLSFALTAF=false;
      foreach($Faltas as $falta)
      {
	extract($falta);
        include($PGconf['basedir']."/templates/lista_faltas_aluno.php");
      }
    }
  }

  /**
   * @brief gera uma tabela contendo a lista de faltas
   * de um aluno;
   * 
   * @see Turma::get_aulas
   * @see Aula::get_faltas
   * @see Aluno::ls_faltas
   */
  function get_faltas($start, $n, $id_aluno=null,$id_turma=null)
  {
    global $CCAA;
    global $CASession;

    /* busca do id_aluno */
    if(!$id_aluno)
    {
      if (property_exists($this,'id_aluno'))
	$id_aluno = $this->id_aluno;
      else
	$id_aluno = $CASession->id_aluno;
    }
    
    /* busca do id_turma */
    if(!$id_turma)
      $id_turma = $CASession->id_turma;

    $Faltas = Turma::get_aulas($start,$n,$id_turma);

    $i=0;
    if ($Faltas)
    {
      /* config do BD */
      if (property_exists($this,'BD'))
        $BD =& $this->BD;
      else
        $BD =& $CCAA;

      foreach($Faltas as $falta)
      {
	$query = "select faltas from faltas where ";
	$query.= "id_aula = ".$falta['id_aula']." and ";
	$query.= "id_aluno = $id_aluno limit 1";
	$Aux = $BD->query_to_table($query);
	if ($Aux)
	  $Faltas[$i]['faltas'] = $Aux[0]['faltas'];
	else
	  $Faltas[$i]['faltas'] = 0;
	$i++;
      }
    }

    return $Faltas;
  }

  /**
   * @brief lista as licoes do aluno
   * @param $id_aluno : id (PK) do aluno
   * @param $id_turma : id_turma (PK) da turma
   * @see Aula::ls_faltas
   * @see Turma::ls_aulas
   * @see Aluno::ls_faltas
   * @see Aluno::get_licoes
   */
  function ls_licoes($id_aluno=null, $id_turma=null)
  {
    global $CASession;
    global $PGconf;
    /* Flag utilizado para chamar corretamente
       lista_aulas.php*/
    $LSLICAOF=true;

    if (!$id_aluno)
    {
      if (property_exists($this,'id_aluno'))
	$id_aluno = $this->id_aluno;
      else
	$id_aluno = $CASession->id_aluno;
    }

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if (!$id_turma)
      $id_turma = $CASession->id_turma;

    $Licoes = Aluno::get_licoes($id_aluno, $id_turma);

    if ($Licoes)
      foreach ($Licoes as $licao)
      {
	extract($licao);
        include($PGconf['basedir']."/templates/lista_licoes_aluno.php");
      }
  }

  /**
   * @brief gera uma tabela contendo a lista de
   * Licoes de um aluno;
   * 
   * @see Turma::get_aulas
   * @see Aula::get_faltas
   * @see Aluno::get_faltas
   * @see Aluno::ls_licoes
   */
  function get_licoes($id_aluno=null,$id_turma=null)
  {
    global $CCAA;
    global $CASession;

    /* busca do id_aluno */
    if(!$id_aluno)
    {
      if (property_exists($this,'id_aluno'))
	$id_aluno = $this->id_aluno;
      else
	$id_aluno = $CASession->id_aluno;
    }
    
    /* busca do id_turma */
    if(!$id_turma)
      $id_turma = $CASession->id_turma;

    /* busca dos capitulos */
    $Licoes = Turma::get_cap($id_turma);

    $i=0;
    if ($Licoes)
    {
      /* config do BD */
      if (property_exists($this,'BD'))
        $BD =& $this->BD;
      else
        $BD =& $CCAA;

      /* busca dos parametros que faltam sobre as licoes */
      foreach($Licoes as $licao)
      {
        $id_capitulo = $licao['id_capitulo'];
	$query = "select ";
	$query.= "l.id_licao as id_licao, ";
	$query.= "l.entr_id_aula as id_aula, ";
	$query.= "l.id_aluno as id_aluno, ";
	$query.= "l.status as status, ";
	$query.= "a.dia as dia ";
	$query.= "from licoes as l, aulas as a ";
	$query.= "where l.entr_id_aula = a.id_aula and ";
	$query.= "l.id_capitulo = $id_capitulo and ";
	$query.= "l.id_aluno = $id_aluno ";
	$query.= "limit 1";

	$Aux = $BD->query_to_table($query);
	/* quando a licao eh encontrada, os vetores se mergem */
	if ($Aux)
	{
	  foreach ($Aux[0] as $campo => $valor)
	    $Licoes[$i][$campo] = $valor;
	}
	/* caso contrario, uma licao falsa (para) exibicao
	 eh criada */
	else
	{
	  $Aux = Licao::fake_licao($licao['id_capitulo']);
	  $Aux['dia'] = Aula::get_campo($Aux['id_aula'],'dia');
	  $Licoes[$i] = array_merge($Licoes[$i],$Aux);
	}
	$i++;
      }
    }

    return $Licoes;
  }

}
?>
