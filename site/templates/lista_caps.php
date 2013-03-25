<?php

/**
 * @file lista_caps.php
 * brief gera uma lista de capitulos de uma aula
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
/* quando o flag nao foi definido,
   eh gerado o cabecalho */
if (!$LSCAPSF)
{
  Aula::ls_caps(false, $id_aula);
}
/* com o flag definido, eh hora
 de gerar cada item (linha/coluna)*/
else
{
?>
  <?php Capitulo::cap_aula_link($id_capitulo, $capitulo, $id_aula); ?> &nbsp;
<?php
}
?>
