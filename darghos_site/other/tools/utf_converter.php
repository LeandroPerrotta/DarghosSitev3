<?php 
$output = "";

if($_POST["output"])
{
	$output = utf8_decode($_POST["output"]);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Conversor de codificação</title>
</head>
<body>
	<form action="" method="POST">
		<textarea name="output" rows="50" cols="100"><?php echo stripslashes($output); ?></textarea>
		<input type='submit' value='Enviar' />
	</form>
</body>
</html>