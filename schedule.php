<?php

$connect =  mysqli_connect('localhost','root','','attend');
//error_reporting(0);
set_time_limit(4000);

//$file = $_FILES['file'];

$stream = fopen($_FILES['file']['tmp_name'], "r");
if (empty($stream)) {
	echo 'no file found';

}else{
	
	$delete = "delete from sch";
	$delete_process = "delete from process";
	$connect->query($delete);
	$connect->query($delete_process);

	while(!feof($stream)){// to read the file till ends
		
		$line = ltrim(rtrim(fgets($stream)))   ;
		$f_line = addslashes($line);
		if (preg_match('/Shift|Night|Created|Sunday|Powered|Afternoon|Evening/', $f_line) or $f_line == '9:00') {
			continue;
		}
		$in_date = strtotime($f_line);
		$date = date('Y-m-d',$in_date);
		if($date > 1970){
			$n_date = strtotime($f_line); // assing date to new variable to keep pervoiuse variable
			$new_date = date('Y-m-d',$n_date);
		}else{
			$login_id = substr($f_line, -4);
			if (is_numeric($login_id)) {
				$agent_name = $f_line; //  assign name to new variable 
				$final_loginID = substr($f_line, -6);
				if (is_numeric($final_loginID)) {
					$final_loginID = substr($f_line, -6);
				}else{
					$final_loginID = substr($f_line, -5);
				}
				
			}else{
				if (substr($f_line,-2) == 'PM' or substr($f_line,-2) == 'AM' ) {// insert shift
					list($login,$logout) = explode('--', $f_line); // explode time to login and logout
					$n_login = strtotime($login);
					$f_login = date('H:i:s ',$n_login);
					$n_logout = strtotime($logout);
					$f_logout = date('H:i:s ',$n_logout);
					echo $new_date . '>' . $agent_name . '<br>';
					$insert = "insert into process (date,login_id,name,login_time,logout_time)values('$new_date','$final_loginID','$agent_name','$f_login','$f_logout')";
					$connect->query($insert);
					
				}else{
					$schedule = $f_line;
					$insert = "insert into process (date,login_id,name,schedule)values('$new_date','$final_loginID','$agent_name','$schedule')";
					$connect->query($insert);
				}
			}	
		}
	}	
		echo $f_line . '<br>';
		//$sql = "insert into sch(txt)values('$f_line')";
		//$connect->query($sql);
		
		

	}




fclose($stream);
/*
$delete = "delete from sch where txt = '9:00' or txt like '%Shift%' or txt like '%Created%' or txt like '%Powered%' or txt like '%Sunday%' or txt like '%Night%' or txt like '%Evening%' or txt like '%Afternoon%'";
$connect->query($delete);
echo $connect->error;

$sql = "select * from sch";
$result = $connect->query($sql);
if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		$in_date = strtotime($row['txt']);
		$date = date('Y-m-d',$in_date);
		
		if($date > 1970){
			$n_date = strtotime($row['txt']); // assing date to new variable to keep pervoiuse variable
			$new_date = date('Y-m-d',$n_date);

		}else{
			$login_id = substr($row['txt'], -6);
			
			if (is_numeric($login_id)) {
				$agent_name = $row['txt']; //  assign name to new variable 
				$final_loginID = substr($row['txt'], -6);
			}else{
				if (substr($row['txt'],-2) == 'PM' or substr($row['txt'],-2) == 'AM' ) {// insert shift
					list($login,$logout) = explode('--', $row['txt']); // explode time to login and logout
					$n_login = strtotime($login);
					$f_login = date('H:i:s ',$n_login);
					$n_logout = strtotime($logout);
					$f_logout = date('H:i:s ',$n_logout);

					//echo $agent_name .'>>'. $new_date . 'login_time' . $f_login . ' logOut'. '>'. $logout . '>'.'<br>'; // shift
					$insert = "insert into process (date,login_id,name,login_time,logout_time)values('$new_date','$final_loginID','$agent_name','$f_login','$f_logout')";
					$connect->query($insert);
					
				}else{
					$schedule = $row['txt'];
					$insert = "insert into process (date,login_id,name,schedule)values('$new_date','$final_loginID','$agent_name','$schedule')";
					$connect->query($insert);
				}

			}

		}
	}
}
}

*/


?>
<script>
	window.location.href='index1.php';
</script>
