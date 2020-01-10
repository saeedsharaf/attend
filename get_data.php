<style type="text/css">
	table ,tr,td{
		border: 1px solid black;
		border-collapse: collapse;
		padding: 5px;
	}
</style>


<?php

$connect= mysqli_connect('localhost','root','','attend');
?>

<table style="">
	<tr>
		<td>Shift Date</td>
		<td>Login_id</td>
		<td>SCh Login & LogOut</td>
		<td>Login_date</td>
		<td>Tardy login</td>
		<td>Act logout Date</td>
		<td>Logout_Variance</td>
	</tr>

<?php


$sql = "select * from process where schedule = ''";
$result = $connect->query($sql);
if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		$login_id = $row['login_id'];
		$date = $row['date'];
		$logout_time = $row['logout_time'];
		$login_time = $row['login_time'];
		$next_day = date('Y-m-d',strtotime("+1 Day",strtotime($date)));
		$adjust_logout = date('H:i:s',strtotime("+2 Hour",strtotime($logout_time)));


		if($login_time >= '15:00:00' and ($logout_time >= '00:00:00' and $logout_time <= '09:00:00')){

			
			$select = "select max(logout) as MAX from cms where login_id = '$login_id' and date = '$next_day' and logout <= '$adjust_logout' ";
			$result1 = $connect->query($select);
			//echo $select . '<br>';
			$rows = $result1->fetch_assoc();
		
			if ($rows['MAX'] != "") { // if result not meet the condtion 
				//echo $result1->num_rows ;
				$login_overnight = "select min(login) as MIN from cms where login_id = '$login_id' and date = '$date' and Login > '09:00:00' ";
				$result_overnight = $connect->query($login_overnight);
				$rows_overnight = $result_overnight->fetch_assoc();

				// to inquery if agen login or not and get variance if yes and get absent if no
					$cms_logout = date('m/d/Y H:i:s',strtotime($next_day .$rows['MAX']));
					$cms_login = date('m/d/Y H:i:s',strtotime($date .$rows_overnight['MIN']));
					//echo $login_overnight . '<br>';

					$schudle_login = date('Y-m-d H:i:s',strtotime($date .$login_time));
					$schudle_logout = date('Y-m-d H:i:s',strtotime($next_day .$logout_time)); // get real time logout to get variance
					$variance_time = (strtotime($schudle_logout) - strtotime($cms_logout))  ;//$variance_time = $schudle_logout - $cms_logout ;
					$variance_login = (strtotime($cms_login) - (strtotime($schudle_login) )) ; 
					
					if ($variance_time > 0) {
					 	$color = 'color:red;';
					 	$variance_time = date('H:i:s',($variance_time -(60*60)));

					}else{
						$color = 'color:black;';
						$variance_time = $variance_time;	
					}

					if ($variance_login > 0) {
					 	$color_login = 'color:red;';
					 	$variance_login = date('H:i:s',($variance_login -(60*60)));
					}else{
						$color_login = 'color:black;';
						$variance_login = $variance_login;	
					}




					?>
				
						<tr>
							<td><?php echo date('m/d/Y',strtotime($date)) ;?></td>
							<td><?php echo $login_id ; ?></td>
							<td><?php echo date("h:i A",strtotime($row['login_time'])) . ' : ' . date("h:i A",strtotime($row['logout_time'])) ; ?></td>
							<td><?php echo $cms_login ;?></td>
							<td style="<?php echo $color_login; ?>"><?php echo $variance_login ; ?></td>
							<td><?php echo date('m/d/Y h:i A',strtotime($cms_logout)) ; ?></td>
							<td style="<?php echo $color; ?>"><?php echo $variance_time  ; ?></td>
						</tr>

						<?php
			}else{  // that will give us the real logout time 
			
				
				$select_again = "select max(logout) as MAX from cms where login_id = '$login_id' and date = '$date'";
				$result_again = $connect->query($select_again);
			
				
				if($result_again->num_rows > 0){ // to inquery if agen login or not and get variance if yes and get absent if no

					$row_again = $result_again->fetch_assoc();
					$cms_logout = date('Y-m-d H:i:s',strtotime($date .$row_again['MAX']));
					
					$schudle_logout = date('Y-m-d H:i:s',strtotime($date .$logout_time)); // get real time logout to get variance
					$variance_time = ((strtotime($schudle_logout) - strtotime($cms_logout))) ;
					//$variance_time = $schudle_logout - $cms_logout ;
					if ($variance_time > 0) {
					 	$color = 'color:red;';
					 	$variance_time = date('H:i:s',($variance_time -(60*60)));
					}else{
						$color = 'color:black;';
						$variance_time = $variance_time;	
					}	
					?>
				
						<tr>
							<td><?php echo date('m/d/Y',strtotime($date)) ;?></td>
							<td><?php echo $login_id ; ?></td>
							<td><?php echo date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) ; ?></td>
							<td></td>
							<td></td>
							<td><?php echo $cms_logout ; ?></td>
							<td style="<?php echo $color; ?>"><?php echo $variance_time ; ?></td>
						</tr>
				

					<?php

			
					//echo $date . ' > ' . $login_id . ' > ' . $schudle_logout . ' > ' . $cms_logout . ' >' .date('H:i:s',$variance_time) . '<br>';
					
				}else{
					//// here to echo absent
					echo 'absent' . '<br>';
				}
				
			}
			
			
			
		}else{ //// here for normal shift
			//continue;
			
			$select = "select max(logout) as MAX ,min(login) as MIN  from cms where login_id = '$login_id' and date = '$date' and Login > '05:00:00'";
			$result_select = $connect->query($select);
			$rows = $result_select->fetch_assoc();
			if ($rows['MAX'] == '' and $rows['MIN'] == '') {
				echo 'absent';
			}else{
				$cms_logout = date('m/d/Y H:i:s',strtotime($date .$rows['MAX']));
				$cms_login = date('m/d/Y H:i:s',strtotime($date .$rows['MIN']));
				//echo $select . '<br>';
				
				$schudle_logout = date('m/d/Y H:i:s',strtotime($date .$logout_time)); // get real time logout to get variance
				$schudle_login = date('m/d/Y H:i:s',strtotime($date .$login_time)); // get real time login to get variance

				$variance_logout = ((strtotime($schudle_logout) - strtotime($cms_logout))) ;
				$variance_login = (strtotime($cms_login) - (strtotime($schudle_login) )) ;
				//$variance_logout = $schudle_logout - $cms_logout ;
				if ($variance_logout > 0) {
				 	$color = 'color:red;';
				 	$variance_logout = date('H:i:s',($variance_logout -(60*60)));
				}else{
					$color = 'color:black;';
					$variance_logout = $variance_logout;	
				}

				if ($variance_login > 0) {
				 	$color_login = 'color:red;';
				 	$variance_login = date('H:i:s',($variance_login -(60*60)));
				}else{
					$color_login = 'color:black;';
					$variance_login = $variance_login;	
				}



				?>
		
				<tr>
					<td><?php echo date('m/d/Y',strtotime($date)) ;?></td>
					<td><?php echo $login_id ; ?></td>
					<td><?php echo date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) ; ?></td>
					<td><?php echo $cms_login ;?></td>
					<td style="<?php echo $color_login; ?>"><?php echo $variance_login ; ?></td>
					<td><?php echo $cms_logout ; ?></td>
					<td style="<?php echo $color; ?>"><?php echo $variance_logout ; ?></td>
				</tr>
				

				<?php

			}

			
		}


	}
}




?>
	</table>