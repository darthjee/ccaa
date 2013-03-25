<?php

/**
 * @file lista_all_caps.php
 * brief gera uma lista de capitulos
 *
 * TOdos os capitulos de ua turma ao impressos
 * sendo que os capitulos de uma determinada aula
 * destacados, e possue recursos de manipulacao de
 * capitulos
 */
// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
/* quando o flag nao foi definido,
   eh gerado o cabecalho */
if (!$LSCAPSF)
{
?>
    <table class='list'>
      <tr>
        <?php Aula::ls_caps(true); ?>
      </tr>	
    </table>
<?php
}
/* com o flag definido, eh hora
 de gerar cada item (linha/coluna)*/
else
{
?>
  <td><?php Capitulo::cap_aula_link($id_capitulo, $capitulo, $id_aula); ?></td>
<?php
}
?>
