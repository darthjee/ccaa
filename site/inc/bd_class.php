<?php
/**
 * @file bd_class.php
 * Classe de banco de dados @ref BDClass
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
/**
 * @brief Classe Banco de Dados
 *
 * Toda a comunicação com o banco de dados passa
 * atraves deste objeto.
 * A configuracao deste objeto exige que ele seja
 * instanciado
 */
class BDClass
{
  /** @brief senha do banco de dados */
  var $Pass;
  /** @brief usuario do banco de dados */
  var $User;
  /** @brief servidor do banco de dados */
  var $Host;
  /** @brief nome do banco do banco de dados */
  var $Banco;

  /** @brief ID do link com o banco */
  var $LinkID = 0;
  /** @brief ID da ultima Query */
  var $QueryID = 0;

  /** @brief codigo de erro */
  var $Errno = 0;
  /** @brief string de identificacao do erro */
  var $Error = "";

  /** @brief Array contendo os resultados tabelados */
  var $Results = array();
  /** @brief numero de linhas dos resultados */
  var $Rows = 0;
  /** @brief Quantas linhas formam afetadas no ultimo update/insert */
  var $Affected = 0;
  /** @brief ID retornado por uma operacao de insercao */
  var $NewID = 0;


  /**
   * @brief conecta ao banco de dados
   */
  function connect() 
  {
    if ( 0 == $this->LinkID  || !mysql_get_server_info($this->LinkID)) {
      $this->LinkID=mysql_connect($this->Host, $this->User, $this->Pass);
      if (!$this->LinkID) {
	$this->halt("Link-ID == false, falha de conexão");
      }
      if (!mysql_select_db($this->Banco)) {
	$this->halt("O banco ".$this->Banco." de dados n&atilde;o pode ser usado");
      }
    } 
  }

  /**
   * @brief para o sistema em caso de erro
   */
  function halt($msg)
  {
    die("$msg<br>Sistem indisponivel, tente mais tade<br>Sessao encerrada.<br>");
  }

  /**
   * @brief faz uma busca no banco retornando
   * o id da busca
   * @see BDClass::update
   * @see BDClass::insert
   * @see BDClass::delete
   */
  function query($query_str)
  {
    $this->connect();

    $this->QueryID = mysql_query($query_str);
    $this->Rows = mysql_num_rows($this->QueryID);
    $this->Affected = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    $this->Results = array();

    if ($this->Errno == 1062) {
      return FALSE;
    } elseif (!$this->QueryID) {
      $this->halt("Consulta 1 SQL invalida: ".$query_str);
    }

    return $this->QueryID;
  }


  /**
   * @brief Faz uma busca retornando o numero de rows
   * encontrados;
   */
  function check_query($querystr)
  {
    $this->query($querystr);
    return $this->Rows;
  }


  /**
   * @brief Fecha a conexao com o banco
   */
  function close()
  {
    if ($this->LinkID)
      mysql_close($this->LinkID);
    $this->LinkID = 0;
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em uma tabela
   */
  function query_to_table($query_str, $debug=null)
  {
    $this->query($query_str, $debug);

    $i=0;
    
    while($row = mysql_fetch_assoc($this->QueryID))
    {
      $this->Results[$i]=$row;
      $i++;
    }
    return $this->Results;
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em um array
   */
  function query_to_row($query_str, $debug=null)
  {
    $this->query_to_table($query_str, $debug);

    return $this->Results[0];
  }

  /**
   * @brief executa uma busca e retorna o resultado
   * em um valor
   */
  function query_to_val($query_str, $debug=null)
  {
    $Row = $this->query_to_row($query_str, $debug);

    if ($Row)
      $val = array_shift($Row);
    else
      $val = null;
    return $val;
  }

  /**
   * @brief faz uma operacao de update atualizando
   * as propriedades certas do Objeto
   * @param $update : string SQL de update
   * @see BDClass::query
   * @see BDClass::insert
   * @see BDClass::delete
   */
  function update($update)
  {
    $this->connect();

    mysql_query($update);
    $this->Affected = mysql_affected_rows($this->LinkID);
    $this->Rows = 0;
    $this->Results = array();
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();

    if ($this->Errno == 1062) {
      return FALSE;
    }
  }

  /**
   * @brief insere uma nova linha na tabela
   * atualizando as propriedades certas do
   * Objeto;
   * @param $insert : string SQL de insert
   * @see BDClass::query
   * @see BDClass::update
   * @see BDClass::delete
   */
  function insert($insert)
  {
    $this->connect();

    mysql_query($insert);
    $this->Affected = mysql_affected_rows($this->LinkID);
    $this->NewID = mysql_insert_id($this->LinkID);
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();

    return $this->NewID;
  }

  /**
   * @brief fazz uma operacao de delete sql
   * atualizando as proprieades certas do banco
   * @param $insert : string SQL de insert
   * @see BDClass::query
   * @see BDClass::update
   * @see BDClass::insert
   */
  function delete($delete)
  {
    $this->connect();

    mysql_query($delete);
    $this->Affected = mysql_affected_rows($this->LinkID);
    $this->Rows = 0;
    $this->Results = array();
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();

    if ($this->Errno == 1062) {
      return FALSE;
    }
  }

  /**
   * @brief ajusta os parametros iniciais do banco
   * @see BDClass
   */
  function set_param_banco($pass=null, $user=null, $host=null, $banco=null)
  {
    global $BDconf;

    if ($pass)
      $this->Pass=$pass;
    else if (!$this->Pass)
      $this->Pass=$BDconf['pass'];

    if ($user)
      $this->User=$user;
    else if (!$this->User)
      $this->User=$BDconf['user'];

    if ($host)
      $this->Host=$host;
    else if (!$this->Host)
      $this->Host=$BDconf['host'];

    if ($banc)
      $this->Banco=$banco;
    else if (!$this->Banco)
      $this->Banco=$BDconf['banco'];
  }

  /**
   * @brief copia os parametros de um banco para outro
   * @see BDClass
   */
  function cp_param_banco($BancoOBJ)
  {
    $this->Pass=$BancoOBJ->Pass;
    $this->User=$BancoOBJ->User;
    $this->Host=$BancoOBj->Host;
    $this->Banco=$BancoOBJ->Banco;
  }

  /**
   * @brief inicializa uma instancia de @ref BDClass
   */
  function BDClass($pass=null, $user=null, $host=null, $banco=null)
  {
    $this->set_param_banco($pass, $user, $host, $banco);
  }

}

?>
