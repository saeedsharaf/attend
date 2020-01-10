<?php

$connect =  mysqli_connect('localhost','root','','attend');
//error_reporting(0);
set_time_limit(10000);
$file = $_POST['file'];
echo 'PLease Wait';
	$stream = fopen('cms.txt', "r");
	
	if (empty($stream)) {
		echo "no file found";
	}else{
		$delete = "delete from cms";
		$connect->query($delete);

		$x= 0;
		while(!feof($stream)){// to read the file till ends

			$line = ltrim(rtrim(fgets($stream)));
			$f_line = addslashes($line);
			list($name,$login_id,$ex,$nohing,$login_time,$nothing1,$logout_time,$date) = explode(";", $f_line);
			$f_login_time = date('H:i:s',strtotime($login_time));
			$f_logout_time = date('H:i:s',strtotime($logout_time));
			$f_date = date('Y-m-d',strtotime($date));

			if ($f_login_time > $f_logout_time and $f_date > '1970-01-01') {
				$adjust_date = date('Y-m-d',strtotime('-1 Day',strtotime($f_date)));
				//echo $login_id . ' > ' . $date . ' > '. $adjust_date . '<br>';
				$add_row = "insert into cms (date,login_id,login)values('$adjust_date','$login_id','$f_login_time')";
				$connect->query($add_row);
			}else{
				if($f_date <= '1970-01-01'){
				$f_date = date('Y-m-d');
				}

			$sql = "insert into cms(date,login_id,login,logout)values('$f_date','$login_id','$f_login_time','$f_logout_time')";
			$connect->query($sql);
			}
			
		
		}

	fclose($stream);

	}	

?>


<script>
	window.location.href='new.php';
</script>
