<?php
/**
 * @file session_class.php
 * @brief Classe Session
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
/**
 * @brief Classe que controla e representa uma
 * linha da tabela session
 */
class Session
{
  var $id_professor;
  var $id_turma = 0;
  var $id_aluno = 0;
  var $id_aula = 0;
  var $id_info = "";

  var $BD = null;

  /**
   * @brief Cria uma instancia de @ref Session
   */
  function Session($id_prof=null, $BDObj=null)
  {
    global $CCAA;

    if ($id_prof)
      $this->id_professor=$id_prof;
    else
    {
      $this->id_professor = ProfClass::get_id();
      $id_prof = $this->id_professor;
    }

    if ($BDObj)
      $this->BD =& $BDObj;
    else
      $this->BD =& $CCAA;


    $query = "select * from session where id_professor = $id_prof limit 1";
    $session = $this->BD->query_to_table($query);

    if($session)
    {
      foreach ($session[0] as $key => $row)
	$this->$key = $row;
    }
  }

  /**
   * @Brief Injeta na Sessão os valores nos campos
   */
  function inject($campo, $valor, $id_prof=null)
  {
    global $CCAA;

    /* checagem do id_professor responsavel */
    if (!$id_prof)
    {
      if (property_exists($this,'id_professor'))
	$id_prof = $this->id_professor;
      else
	$id_prof = ProfClass::get_id();
    }
      
    $update = "update session set id_$campo='$valor' where id_professor = $id_prof";
    
    /* checagem da BD */
    if ($this->BD)
      $BD =& $this->BD;
    else
      $BD =& $CCAA;

    /* primeira tentativa, update */
    $BD->update($update);

    /* segunda tentativa, insert */
    if (!$BD->Affected)
    {
      $insert="insert into session set id_$campo='$valor', id_professor=".$this->id_professor;
      $BD->insert($insert);
    }

    /* update final dlo campo no objeto */
    if (property_exists($this,$campo))
      $this->$campo=$valor;
  }
}

?>
