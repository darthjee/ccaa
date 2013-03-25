<?php

/**
 * @file aluno_info.php
 * @brief mostra os dados de um aluno
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<div class='title'><?php Aluno::link(); ?></div>
<div id='faltas'><?php include($CAconf['basedir']."/templates/lista_faltas_aluno.php"); ?></div>
<div id='licoes'><?php include($CAconf['basedir']."/templates/lista_licoes_aluno.php"); ?></div>
