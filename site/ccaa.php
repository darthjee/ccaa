<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
require_once('inc/bd_class.php');
require_once('inc/bd_prof_class.php');
require_once('inc/session_class.php');
require_once('inc/turma_class.php');
require_once('inc/aula_class.php');
require_once('inc/aluno_class.php');
require_once('inc/licao_class.php');
require_once('inc/capitulo_class.php');
require_once('inc/common.php');
require_once('ccaa-conf.php');

  ?>
  <head>
  <link rel="stylesheet" type="text/css" href="<?php echo $PGconf['comdir'] ?>/templates/ccaa.css">
  <script language="javascript" type="text/javascript">
  <!--
  <?php include($CAconf['basedir']."/scripts/ccaa.js") ?>
  -->
  </script>
  </head>
  <?php

if (ProfClass::is_prof())
{
  if ($_POST['target'])
  {
    $target = $_POST['target'];
    include("engine/$target.php");
    include('ccaa-conf.php');
  }
  $ccaa_modulo = $_GET['ccaa_modulo'];
  if (in_array($ccaa_modulo, $CAmodulos))
  {
    include('modulos/'.$ccaa_modulo.'.php');
  }
}
else
  include('templates/subs.php');
?>
