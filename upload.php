
<?php
session_start();
error_reporting(0);

set_time_limit(10000);
$connect =  mysqli_connect('localhost','root','','attend');
$file = fopen($_FILES['file']['tmp_name'],'r');
$file_name = $_FILES['file']['name'];
$file_type = $_FILES['file']['type'];



	if($file_type !== 'text/plain'){ // check file type 

		echo 	'<div class="error">
					<span class="error_txt">Note : Please only txt file can be uploaded </span>
				</div>' ;
	}else{
		if($file_name == 'cms.txt'){ /// here to upload cms data 
			$delete = "delete from cms";
			$connect->query($delete);
			$sql = "insert into cms (date,login_id,login,logout,login_date,logout_date) values ";
			$sql1 = '';
			while(!feof($file)){// to read the file till ends

				$line = ltrim(rtrim(fgets($file)));
				$f_line = addslashes($line);
				list($name,$login_id,$ex,$nohing,$login_time,$nothing1,$logout_time,$date) = explode(";", $f_line);
				$f_login_time = date('H:i:s',strtotime($login_time));
				$f_logout_time = date('H:i:s',strtotime($logout_time));
				$f_date = date('Y-m-d',strtotime($date));
				

				if ($f_login_time > $f_logout_time and $f_date > '1970-01-01') {
					$zero_hour = '23:59:00';
					$adjust_date = date('Y-m-d',strtotime('-1 Day',strtotime($f_date)));
					$login_date = date('Y-m-d H:i:s',strtotime($adjust_date . $f_login_time));
					$real_logout_time = date('Y-m-d H:i:s',strtotime($f_date . $f_logout_time));
					$real_login_time = date('Y-m-d H:i:s',strtotime($f_date . '00:00:00'));
					$tmp_logout_date = date('Y-m-d H:i:s',strtotime($adjust_date . $zero_hour));
					
					//echo $login_id . ' > ' . $date . ' > '. $adjust_date . '<br>';
					//$add_row = "insert into cms (date,login_id,login,login_date,logout,logout_date)values('$adjust_date','$login_id','$f_login_time','$login_date','$zero_hour','$tmp_logout_date')"; // adjust row data 
					$sql1 .= "('$adjust_date','$login_id','$f_login_time','$zero_hour','$login_date','$tmp_logout_date'),('$f_date','$login_id','00:00:00','$f_logout_time','$real_login_time','$real_logout_time'),";

					//$add_row1 = "insert into cms (date,login_id,login,login_date,logout,logout_date)values('$f_date','$login_id','00:00:00','$real_login_time','$f_logout_time','$real_logout_time')"; // insert real data 

					//$connect->query($add_row);
					//$connect->query($add_row1);

					//$connect->query("START TRANSACTION");
					//$connect->query($add_row);
					//$connect->query($add_row1);
					//$connect->query("COMMIT");
			
					//echo $add_row . ' > ' .$connect->error . '<br>'; 

				}else{
					if($f_date <= '1970-01-01'){
					$f_date = date('Y-m-d');
					}

					$login_date = date('Y-m-d H:i:s',strtotime($f_date . $f_login_time));
					$logout_date = date('Y-m-d H:i:s',strtotime($f_date . $f_logout_time));
					//echo $login_id . ' > ' . $f_date . ' > '. $f_login_time . '<br>';
					//$sql = "insert into cms(date,login_id,login,logout,login_date,logout_date)values('$f_date','$login_id','$f_login_time','$f_logout_time','$login_date','$logout_date')";
					$sql1 .="('$f_date','$login_id','$f_login_time','$f_logout_time','$login_date','$logout_date'),";
					//$connect->query($sql);
					//$connect->query("START TRANSACTION");
					//$connect->query($sql);
					//$connect->query("COMMIT");
				}
			}
			$sql1 = mb_substr($sql1,0,-1) ;
			$sql = $sql.$sql1;
			$connect->query($sql);
			fclose($file);
	
			echo '<div class="error">
					Note : CMS Data uploaded 
				</div>' ;

		}else if ($file_name == 'sch.txt'){// here to upload schedule data
			$delete = "delete from sch";
			$delete_process = "delete from process";
			$connect->query($delete);
			$connect->query($delete_process);
			$sql = "insert into process (date,login_id,name,login_time,logout_time,schedule) values" ;
			$sql1 = '';
			while(!feof($file)){// to read the file till ends
			
				$line = ltrim(rtrim(fgets($file)))   ;
				$f_line = addslashes($line);
				if(preg_match('/Shift|Night|Created|Sunday|Powered|Afternoon|Evening/', $f_line) or $f_line == '9:00') {
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
						
							//$insert = "insert into process (date,login_id,name,login_time,logout_time)values('$new_date','$final_loginID','$agent_name','$f_login','$f_logout')";
							$sql1 .= "('$new_date','$final_loginID','$agent_name','$f_login','$f_logout',''),";
					
						
						}else{
							$schedule = $f_line;
							//$insert = "insert into process (date,login_id,name,schedule)values('$new_date','$final_loginID','$agent_name','$schedule')";
							$sql1 .= "('$new_date','$final_loginID','$agent_name','','','$schedule'),";
					
						}
					}	
				}
			}
			$sql1 = mb_substr($sql1,0,-1) ;
			$sql = $sql.$sql1;
			$connect->query($sql);
			echo$connect->error;
			fclose($file);

			echo 	'<div class="error">
					Note : Schedule Data uploaded 
					</div>' ;

		}else{// here if the names doesnt match
			?>
				<script>
					/*
					var error = 1;//'<div class="error">';
  				//'<span class="error_txt">Note : Please rename schedule data to (sch) & CMS Data to (cms) </span>'
				//'</div>';
					document.getElementById('result').error; 
				*/
				</script>
			<?php
			echo '<div class="error">
  				<span class="error_txt">Note : Please rename schedule data to (sch) & CMS Data to (cms) </span>
				</div>';	
		}
	}






?>





