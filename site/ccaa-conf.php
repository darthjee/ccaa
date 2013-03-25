<?php

/**
 * @file ccaa-conf.php
 * Configuracao e inicializacao do componente
 *
 * Aqui sao inicializados os arrays e objetos
 * de configuracao
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

/*************
 Arrays
 *************/
/** Array com campos de configuracao para
 * o banco de dados */
global $BDconf;
/** Array com a configuracao da pagina (diretorios) */
global $PGconf;
/** Array com a configuracao geral */
global $CAconf;
/** Array contendo todos os modulos */
global $CAmodulos;
/** Array contendo todass os displays */
global $CAdisplays;

/*************
 Objetos
**************/
/** Objeto Usuario Joomla*/
global $User;
/** Objeto Banco de dados */
global $CCAA;
/** Objeto Sessao */
global $CASession;

$CAconf['pass'] = '';
$CAconf['user'] = '';
$CAconf['host'] = '';
$CAconf['banco'] = '';

$CAconf['comdirstr'] = "components/com_ccaa";

$diraux = $_SERVER['PHP_SELF'];
ereg("\/.*\/",$diraux,$diraux);
$CAconf['comdir'] = $diraux[0].$CAconf['comdirstr'];

$CAconf['jdir'] = '/var/www/joomla';
$CAconf['basedir'] = $CAconf['jdir'].'/'.$CAconf['comdirstr'];

$BDconf=$CAconf;
$PGconf=$CAconf;

$User = JFactory::getUser();
$CCAA = new BDClass();
$CASession = new Session();

$CAmodulos = array(
    'alunos',
    'aulas'
    );

$CAdisplays = array(
    'aluno',
    'aula'
    );

?>
