<?php
/**
 * @file lista_licoes_aluno.php
 * @brief lista as licoes de um aluno
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
if (!$LSLICAOF)
{
  ?>
    <table class='list'>
      <tr>
        <th colspan=2>Capitulos</th>
        <th colspan=2>Licoes</th>
        <th>LCPs</th>
      </tr><tr>
        <th>Num</th>
        <th>Compl.</th>
        <th>Status</th>
        <th>Data</th>
        <th>Status</th>
      </tr>
        <?php Aluno::ls_licoes() ?>
    </table>
  <?php
}
else
{
  ?>
    <tr>
      <th><?php echo $capitulo ?></th>
      <td></td>
      <td><?php Licao::status_link("S1", $status, $id_licao, $id_aluno, $id_capitulo, $status, $id_aula) ?></td>
      <td><?php echo $dia ?></td>
      <td></td>
    </tr>
  <?php
}
?>
