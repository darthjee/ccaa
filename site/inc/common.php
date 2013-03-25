<?php

// no direct access
defined('_JEXEC') or die('Acesso Restrito');
?>

<?php

function vertical_day($dia)
{
  $vals=explode('-', $dia);
  $str = "";
  
  foreach ($vals as $val)
  {
    $num=$val%100;
    if ($num < 10)
      $num="0$num";
    $str .= "$num<br>";
  }
  return $str;
}

?>
