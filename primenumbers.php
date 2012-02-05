<?php 
ini_set('max_execution_time','360');
?>

<form action="" method="get"> 
  <input name="tofound" type="text"  /> 
  <input name="submit" type="submit" value="Go!" /> 
</form> 
  
<?php 
$found = 0;
$start = 1;
$i = $start;
$now = time();

echo "Begin {$now} <br>";

if(isset($_GET["tofound"]))
{
  while($found < $_GET["tofound"])
  { 
  	$i++;
    if($i % 2 != 1) continue; 
    $d = 3; 
    $x = sqrt($i); 
    while ($i % $d != 0 && $d < $x) $d += 2; 
    if((($i % $d == 0 && $i != $d) * 1) == 0)
    { 
    	$found++;
    }
  } 
  
  echo "End " . time() . "<br>";
  
  echo "Todos primos encontrados em " . (time() - $now) . " segundos.";
}
?>