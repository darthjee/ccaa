<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>
<div id='ccaa_page' class="ccaa_page">
 <div id='ccaa_leftcont'>
   <?php include($CAconf['basedir']."/templates/lista_turma.php");?>
 </div>
 <div id='ccaa_rightcont'>
   <?php
     if ($CASession->id_info)
     {
       $tipo=$CASession->id_info;
       $campo="id_".$tipo;
       if(ProfClass::belongs($tipo,$CASession->$campo) && in_array($tipo,$CAdisplays))
       {
	 if (Turma::belongs($tipo,$CASession->$campo))
	   include($CAconf['basedir']."/displays/$tipo"."_info.php");
       }
     }
   ?>
 </div>
 <div id='ccaa_bottomcont'>
   <?php include($CAconf['basedir']."/templates/lista_aula.php");?>
 </div>
</div>
