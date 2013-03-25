<?php
/**
 * @file aula_class.php
 * @brief Classe @ref Aula
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

/**
 * @brief Classe que controla e representa uma linha da tabela aulas
 */
class Aula
{
  var $id_aula;
  var $dia;
  var $id_turma;
  var $aulas;
  var $last;

  var $BD;
  var $Faltas=null;

  /**
   * @brief Cria uma instancia de @ref Aula
   * @param $id_aula : id (Primary Key) do objeto
   * @param $BDObj : Objeto banco de dados (@ref bd_class)
   */
  function Aula($id_aula=null, $BDObj=null)
  {
    global $CCAA;
    global $CASession;

    if ($id_aula)
      $this->id_aula = $id_aula;
    else
    {
      $this->id_aula = $CASession->id_aula;
      $id_aula = $this->id_aula;
    }

    if ($BDObj)
      $this->BD =& $BDObj;
    else
    {
      $this->BD =& $CCAA;
      $BDObj =& $this->BD;
    }

    $this->Faltas=null;

    $query = "select * from aulas where id_aula = $id_aula limit 1";
    $aula = $BDObj->query_to_table($query);

    if ($aula)
      foreach ($aula[0] as $campo => $valor)
	$this->$campo = $valor;
  }

  /**
   * @brief pega um campo da tabela aulas
   * 
   * Caso o campo esteja definido e o id_aula seja o mesmo
   * do objeto, o campo é retornado apartir do objeto,
   * caso contrario, o campo sera buscado na tabela
   * @see Aluno::get_campo
   */
  function get_campo($id_aula, $campo)
  {
    global $CCAA;

    /* caso Aula tenha sido instanciado e tenha id_aula igual,
     o campo sera retornadao partindo da instancia */
    if (property_exists($this,$campo) && $this->id_aula == $id_aula)
      $value = $this->$campo;
    /* Caso seja necessario, o campo sera buscado do banco de dados */
    else
    {
      $query = "select $campo from aulas where id_aula = $id_aula limit 1";

      if ($this->BD)
	$BD =& $this->BD;
      else
	$BD =& $CCAA;

      $value = $BD->query_to_val($query);
    }

    return $value;
  }

  /**
   * @brief gera uma tabela contendo a lista de faltas
   * de uma aula;
   * @see Turma::get_aulas
   * @see Aula::ls_faltas
   */
  function get_faltas($id_aula=null)
  {
    global $CCAA;
    global $CASession;

    if(!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    
    $id_turma = Aula::get_campo($id_aula,'id_turma');

    $query = "select ";
    $query.= " f.id_aluno as id_aluno, ";
    $query.= " a.nome as nome, ";
    $query.= " f.id_aula as id_aula, ";
    $query.= " f.faltas as faltas ";
    $query.= " from faltas as f, alunos as a ";
    $query.= " where f.id_aula=$id_aula ";
    $query.= " and f.id_aluno=a.id_aluno ";
    $query.= " and f.id_aluno in (select id_aluno from alunos_turmas where ";
    $query.= " id_turma=".$id_turma.") ";

    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $Faltas = $BD->query_to_table($query);

    return $Faltas;
  }

  /**
   * @brief lista as faltas de uma aula
   * @param $id_aula : id (PK) da aula
   * @param $id_turma : id da turma a ser analizada
   * @see Turma::ls_aulas
   * @see Aula::get_aulas
   */
  function ls_faltas($id_aula=null)
  {
    global $PGconf;
    $Faltas;

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if (!$id_aula && property_exists($this,'id_aula'))
      $id_aula = $this->id_aula;

    /* A lista de faltas entao eh buscada do objeto ou od banco de dados */
    if (property_exists($this, 'Faltas') && $this->Faltas && $this->id_aula == $id_aula)
      $Faltas = $this->Faltas;
    else
    {
      $Faltas = Aula::get_faltas($id_aula);
      /* quando buscada no banco de dados, esta é atualizada no objeto */
      if (property_exists($this,'Faltas') && $this->id_aula == $id_aula)
	$this->Faltas = $Faltas;
    }

    /* a impressao é feita usando o template lista_faltas.php */
    $LSFALTASF=true;
    if ($Faltas)
      foreach($Faltas as $falta)
      {
	extract($falta);
	include($PGconf['basedir']."/templates/lista_faltas.php");
      }
    $LSFALTASF=false;
  }

  /**
   * @brief gera uma nova data para as aulas
   */
  function new_data_aula($id_turma=null)
  {
    global $CASession;

    /* checagem do parametro inicial */
    if (!isset($id_turma))
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }

    /* busca das 3 ultimas aulas */
    for ($i = 1; $i <= 3; $i++)
    {
      $id_aula = Turma::get_id_aula_pos(-$i);
      if ($id_aula)
	$id_aulas[$i] = $id_aula;
      else
	$i = 3;
    }

    /* analise de geracao da nova data */
    $n = count($id_aulas);
    switch ($n)
    {
      case 0:
	      $time = time();
	      break;
      case 1:
	      $time = strtotime(Aula::get_campo($id_aulas[1], 'dia'))+7*24*3600;
	      break;
      case 2:
	      $time = 2*strtotime(Aula::get_campo($id_aulas[1], 'dia')); 
	      $time -= strtotime(Aula::get_campo($id_aulas[2], 'dia'));
	      break;
      default:
	      $time =  strtotime(Aula::get_campo($id_aulas[1], 'dia')); 
	      $time += strtotime(Aula::get_campo($id_aulas[2], 'dia')); 
	      $time -= strtotime(Aula::get_campo($id_aulas[3], 'dia'));
    }
    $day = date('Y-m-d',$time);
    return $day;
  }

  /**
   * @brief cria uma nova aula no banco
   */
  function create_aula($dia=null, $id_turma=null, $aulas=null, $last=null)
  {
    global $CASession;
    global $CCAA;

    /* buscando os dados de criacao */
    if (!isset($id_turma))
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = $CASession->id_turma;
    }

    if (!isset($dia))
    {
      if (property_exists($this,'dia'))
	$dia = $this->dia;
      else
	$dia = Aula::new_data_aula($id_turma);
    }

    if(!isset($aulas))
    {
      if (property_exists($this,'aulas'))
	$aulas = $this->aulas;
      else
	$aulas = Aula::get_campo(Turma::get_id_aula_pos(-1,$id_turma),'aulas');
    }

    if(!isset($last))
      $last="";

    /* criando query de insercao */
    $insert = "insert into aulas set ";
    $insert.= "id_turma = $id_turma, ";
    $insert.= "dia = '$dia', ";
    $insert.= "aulas = $aulas,";
    $insert.= "last = '$last'";
    
    /* setando banco de dados */
    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $BD->insert($insert);
  }

  /**
   * @brief imprime o html contendo o dia e o link
   * de exibicao
   *
   * O link e impresso atraves do template
   * @ref data_aula.php
   */
  function link_aula($id_aula=null, $dia=null)
  {
    global $PGconf;
    global $CASession;

    /* checagem do id_aula */
    if (!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    /* checagem do dia */
    if (!$dia)
    {
      if (property_exists($this,'dia') && $this->id_aula == $id_aula)
        $dia = $this->dia;
      else
        $dia = Aula::get_campo($id_aula, 'dia');
    }
    
    include($PGconf['basedir']."/templates/data_aula.php");
  }

  /**
   * @brief gera uma lista de todos os capitulos de uma aula
   * @param $id_aula : id da aula
   * @param $all : indica se todos os capitulos
   * devem ser listados (destacando os capitulos da aula)
   * ou nao
   * @param $id_turma : id da turma (utilizado para gerar a lista completa)
   */
  function ls_caps($all=false, $id_aula=null, $id_turma=null)
  {
    global $CCAA;
    global $CASession;
    global $PGconf;

    /* checagem do id_aula */
    if (!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    /* set do id_turma */
    if (!isset($id_turma))
    {
      if (property_exists($this,'id_turma'))
	$id_turma = $this->id_turma;
      else
	$id_turma = Aula::get_campo($id_aula,'id_turma');
    }

    /* set do banco de dados */
    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* quando all eh verdadeiro, temos uma busca de todos os capitulos  */
    if ($all)
    {
      $Capitulos = Turma::get_capitulos($id_turma);
      if ($Capitulos)
      {
	foreach ($Capitulos as $i => $capitulo)
	{
	  $query = "select completado from aulas_capitulos where ";
	  $query.= "id_aula=$id_aula and id_capitulo =".$capitulo['id_capitulo'];
	  $Aux = $BD->query_to_table($query);
	  
	  /* quando a busca nao da resultado, significa que o capitulo nao
	     foi lecionado nesta aula */
	  if (!$Aux)
	    $Aux['completado'] = 'I';

	  array_merge($Capitulos[$i],$Aux);
	}
      }
    }
    else
      $Capitulos = Aula::get_capitulos($id_aula);

    /* saida final */
    $LSCAPSF=true;
    if ($Capitulos)
    {
      foreach($Capitulos as $caps)
      {
	extract($caps);
	if ($all)
	  include($PGconf['basedir']."/templates/lista_all_caps.php");
	else
	  include($PGconf['basedir']."/templates/lista_caps.php");
      }
    }
    $LSCASPF=false;
  }

  /**
   * seleciona todos os capitulos pertencentes a uma aula
   */
  function get_capitulos($id_aula=null)
  {
    global $CCAA;
    global $CASession;

    /* checagem do id_aula */
    if (!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }

    /* set do banco de dados */
    if (property_exists($this,'BD'))
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $query = "select ";
    $query.= "c.capitulo as capitulo, ";
    $query.= "c.id_capitulo as id_capitulo,";
    $query.= "ac.completado as completado ";
    $query.= "from capitulos as c, aulas_capitulos as ac ";
    $query.= "where ac.id_aula=$id_aula and c.id_capitulo = ac.id_capitulo";

    $Capitulos = $BD->query_to_table($query);

    return $Capitulos;
  }

  /**
   * @brief lista as licoes de uma aula
   * @param $id_aula : id (PK) da aula
   * @see Aula::get_licoes
   */
  function ls_alunos_licoes($entr=null, $id_aula=null, $id_turma=null)
  {
    global $PGconf;
    global $CASession;

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if(!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    if(!$id_turma)
    {
      if (property_exists($this,'id_turma') && $this->id_aula==$id_aula)
	$id_turma = $this->id_turma;
      else
	$id_turma = Aula::get_campo($id_aula,'id_turma');
    }
    if (!$entr)
      $entr = "S','N";

    /* para se imprimir, deve-se procurar os alunos */
    $Alunos = Aula::get_alunos_licoes($entr,$id_aula,$id_turma);

    $LSALUF=true;
    if ($Alunos)
      foreach($Alunos as $aluno)
      {
	extract($aluno);
	include($PGconf['basedir']."/templates/lista_licoes_aula.php");
      }
    $LSALUF=false;
  }

  /**
   * @brief busca todos os alunos que tem licoes
   * para uma dada aula com um dado status
   */
  function get_alunos_licoes($lc_entr=null, $id_aula=null, $id_turma=null)
  {
    global $PGconf;
    global $CASession;
    global $CCAA;

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if(!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    if(!$id_turma)
    {
      if (property_exists($this,'id_turma') && $this->id_aula==$id_aula)
	$id_turma = $this->id_turma;
      else
	$id_turma = Aula::get_campo($id_aula,'id_turma');
    }
    if (!$lc_entr)
      $lc_entr = "S','N";

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    $query ="select id_aluno,nome from alunos where id_aluno in ";
    $query.= "(select id_aluno from licoes where ";
    $query.= "entregue in ('$lc_entr') and entr_id_aula=$id_aula) ";
    $query.= "and id_aluno in ";
    $query.= "(select id_aluno from alunos_turmas where ";
    $query.= "id_turma=$id_turma)";
    $Alunos = $BD->query_to_table($query);

    return $Alunos;
  }


  /**
   * @brief mostra todas as licoes de um
   * determinado aluno em uma determinada aula
   */
  function ls_licoes_aluno($entr=null, $id_aluno=null, $id_aula=null, $id_turma=null)
  {
    global $PGconf;
    global $CASession;

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if(!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    if(!$id_turma)
    {
      if (property_exists($this,'id_turma') && $this->id_aula==$id_aula)
	$id_turma = $this->id_turma;
      else
	$id_turma = Aula::get_campo($id_aula,'id_turma');
    }
    if(!$id_aluno)
      $id_aluno = $CASession->id_aluno;
    if (!$entr)
      $entr = "S','N";

    $Licoes = Aula::get_licoes_aluno($entr, $id_aluno, $id_aula, $id_turma);
    $LSLICF=true;
    if ($Licoes)
      foreach($Licoes as $licao)
      {
	extract($licao);
	include($PGconf['basedir']."/templates/lista_licoes_aula.php");
      }
    $LSLICF=false;
  }

  /**
   * @brief mostra todas as licoes de um
   * determinado aluno em uma determinada aula
   */
  function get_licoes_aluno($entr=null, $id_aluno=null, $id_aula=null, $id_turma=null)
  {
    global $CASession;
    global $CCAA;

    /* caso nao tenham sido passados os parametros,
     estes serao buscados do objeto */
    if(!$id_aula)
    {
      if (property_exists($this,'id_aula'))
	$id_aula = $this->id_aula;
      else
	$id_aula = $CASession->id_aula;
    }
    if(!$id_turma)
    {
      if (property_exists($this,'id_turma') && $this->id_aula==$id_aula)
	$id_turma = $this->id_turma;
      else
	$id_turma = Aula::get_campo($id_aula,'id_turma');
    }
    if(!$id_aluno)
      $id_aluno = $CASession->id_aluno;
    if (!$lc_entr)
      $lc_entr = "S','N";

    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;
    
    $query = "select ";
    $query.= "l.id_licao as id_licao,";
    $query.= "l.status as status, ";
    $query.= "c.capitulo as capitulo ";
    $query.= "from licoes as l, capitulos as c ";
    $query.= "where l.id_aluno=$id_aluno and c.id_turma=$id_turma and ";
    $query.= "l.id_capitulo=c.id_capitulo and ";
    $query.= "l.entr_id_aula=$id_aula and ";
    $query.= "l.entregue in ('$entr')";

    $Licoes = $BD->query_to_table($query);

    return $Licoes;
  }
}
?>
