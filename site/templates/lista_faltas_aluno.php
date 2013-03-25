<?php
/**
 * @file lista_faltas_aluno.php
 * @brief lista as faltas de um aluno
 */

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php
if (!$LSFALTAF)
{
  ?>
    <table class='list'>
      <tr>
        <?php Aluno::ls_faltas(0,20) ?>
      </tr>
    </table>
  <?php
}
else
{
  if ($HLSFALTAF)
  {
    ?>
      <th><?php echo $dia; ?></th>
    <?php
  }
  else
  {
    ?>
      <td><?php echo $faltas ?></td>
    <?php
  }
}
?>
