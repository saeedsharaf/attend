<?php
$file = $_FILES['file']['name'];
$file_it = fopen($_FILES['file']['tmp_name'],'r');
if (!feof($file_it)) {
	$x = fgets($file_it);
	echo $x . '<br>';
}



?>