<?php

/**
 * @file aula.php
 * @brief mostra as informacoees sobre uma certa aula
 *
 * Data
 * Capitulos
 * Faltas
 * Licoes
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>


<div class='title'><?php Aula::link_aula(); ?></div>
<div id='caps'><?php include($CAconf['basedir']."/templates/lista_all_caps.php"); ?></div>
