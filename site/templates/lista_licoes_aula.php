<?php

/**
 * @file lista_licoes.php
 * @brief lista as faltas de uma aula
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
if (!$LSALUF && !$LSLICF)
{
  Aula::ls_alunos_licoes($entr, $id_aula);
}
else
{
  if (!$LSLICF)
  {
    ?>
      <?php Aluno::link($id_aluno,$nome) ?>
      (<?php Aula::ls_licoes_aluno($entr, $id_aluno, $id_aula, $id_turma);?>)<br>
    <?php
  }
  else
  {
    ?>
      <div class='hidden_table'><?php Licao::aula_link($id_licao, $capitulo); ?></div>
    <?php
  }
}
