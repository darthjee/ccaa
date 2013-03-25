<?php

/**
 * @file lista_faltas.php
 * @brief lista as faltas de uma aula
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
if (!$LSFALTASF)
{
  Aula::ls_faltas($id_aula);
}
else
{
  ?>
    <?php Aluno::link($id_aluno,$nome) ?>
    (<?php echo $faltas; ?>)<br>
  <?php
}
