<style type="text/css">
	table ,tr,td{
		border: 1px solid black;
		border-collapse: collapse;
		padding: 5px;
	}
</style>


<?php
error_reporting(0);
$connect= mysqli_connect('localhost','root','','attend');

?>
<form action="" method="get" style="margin-bottom: 20px;">
	<span>Please write Date </span>
	<input type="text" name="date" placeholder="format like 2019-12-26">
	<input type="submit" name="search" value="Search" style="margin-right: 20px;margin-left: 10px;">
	<input type="submit" name="export" value="Export">

</form>

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
$html_date = $_GET['date'];
//echo $html_date;
set_time_limit(4000);

$sql = "select * from process where schedule = '' and date = '$html_date'";
$result = $connect->query($sql);
if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		$login_id = $row['login_id'];
		$date = $row['date'];
		$name = addslashes($row['name']);
		$logout_time = $row['logout_time'];
		$login_time = $row['login_time'];
		$next_day = date('Y-m-d',strtotime("+1 Day",strtotime($date)));
		$previous_day = date('Y-m-d',strtotime("-1 Day",strtotime($date)));
		$adjust_logout = date('H:i:s',strtotime("+2 Hour",strtotime($logout_time)));
		$adjust_login = date('H:i:s',strtotime("-3 Hour",strtotime($login_time)));
		$schedule = date("h:i A",strtotime($row['login_time'])) . ' : ' . date("h:i A",strtotime($row['logout_time'])) ;

		if($login_time >= '05:00:00' and $login_time <= '14:00:00'){  // identfy morining shift 
			$select = "select max(logout) as MAX ,min(login) as MIN  from cms where login_id = '$login_id' and date = '$date' and Login >= '$adjust_login' ";
			$result_select = $connect->query($select);
			$rows = $result_select->fetch_assoc();
			if ($rows['MAX'] == '' and $rows['MIN'] == '') {// detremain absent for montining shift
				//$sql = "insert into data (date,name,schedule,absent) values ('$date','$name','$schedule','Absent')";
				//$connect->query($sql);
			
				?>
				<tr>
					<td><?php echo $date ; ?></td>
					<td><?php echo $login_id ; ?></td>
					<td><?php echo date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) ; ?></td>
					<td></td>
					<td></td>
					<td></td>
					<td style="color:red;">Absent</td>
				</tr>	

				<?php
			
				
				continue;
			}else{

				
				$cms_logout = date('Y-m-d H:i:s',strtotime($date .$rows['MAX']));
				$cms_login = date('Y-m-d H:i:s',strtotime($date .$rows['MIN']));
				//echo $select . '<br>';
				$schudle_logout = date('Y-m-d H:i:s',strtotime($date .$logout_time)); // get real time logout to get variance
				$schudle_login = date('Y-m-d H:i:s',strtotime($date .$login_time)); // get real time login to get variance
				$variance_logout = ((strtotime($schudle_logout) - strtotime($cms_logout))) ;
				$variance_login = (strtotime($cms_login) - (strtotime($schudle_login) )) ;


			}
		}else if($login_time >= '15:00:00' and $login_time <= '23:00:00'){
			
			$select = "select min(login) as MIN  from cms where login_id = '$login_id' and date = '$date' and Login >= '$adjust_login' "; // get login in sam day
			$select_logout = "select max(logout) as MAX  from cms where login_id = '$login_id' and date = '$next_day' and logout <= '09:00:00' "; // get logout in next day

			$result_select = $connect->query($select);
			$row_login = $result_select->fetch_assoc();

			$result_logout = $connect->query($select_logout);
			$row_logout = $result_logout->fetch_assoc();
			if($row_login['MIN'] == '' and $row_logout['MAX'] == ''){
				//$sql = "insert into data (date,name,schedule,absent) values ('$date','$name','$schedule','Absent')";
				//$connect->query($sql);
				
			
			?>
			<tr>
				<td><?php echo $date ; ?></td>
				<td><?php echo $login_id ; ?></td>
				<td><?php echo date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) ; ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td style="color:red;">Absent</td>
			</tr>	

			<?php
		
				continue;

			}else{
				if ($row_logout['MAX'] == '') { // if agent logout before next day
					$select_logout = "select max(logout) as MAX  from cms where login_id = '$login_id' and date = '$date'"; // get logout in same day
					$result_logout = $connect->query($select_logout);
					$row_logout = $result_logout->fetch_assoc();
				}

				$cms_logout = date('Y-m-d H:i:s',strtotime($date .$row_logout['MAX']));
				$cms_login = date('Y-m-d H:i:s',strtotime($date .$row_login['MIN']));
				//echo $select . '<br>';
				$schudle_logout = date('Y-m-d H:i:s',strtotime($date .$logout_time)); // get real time logout to get variance
				$schudle_login = date('Y-m-d H:i:s',strtotime($date .$login_time)); // get real time login to get variance
				$variance_logout = ((strtotime($schudle_logout) - strtotime($cms_logout))) ;
				$variance_login = (strtotime($cms_login) - (strtotime($schudle_login) )) ;


			}

		}else{/// shift from 00:00:00
		

			$select_logout = "select max(logout) as MAX  from cms where login_id = '$login_id' and date = '$date' and logout <= '11:00:00' ";
			$result_logout = $connect->query($select_logout);
			$row_logout =$result_logout->fetch_assoc();

			$select = "select max(login) as MIN  from cms where login_id = '$login_id' and date = '$previous_day' and Login >= '23:00:00' ";
			$result_select = $connect->query($select);
			$row_login = $result_select->fetch_assoc();

			if ($row_login['MIN'] == '' and $row_logout['MAX'] == '') {
				//$sql = "insert into data (date,name,schedule,absent) values ('$date','$name','$schedule','Absent')";
				//$connect->query($sql);
			
				?>
				<tr>
					<td><?php echo $date ; ?></td>
					<td><?php echo $login_id ; ?></td>
					<td><?php echo date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) ; ?></td>
					<td></td>
					<td></td>
					<td></td>
					<td style="color:red;">Absent</td>
				</tr>
				<?php
				
	
				continue;
			}else{
				if ($row_login['MIN'] == '') {
					$select = "select min(login) as MIN  from cms where login_id = '$login_id' and date = '$date'";
					$result_select = $connect->query($select);
					$row_login = $result_select->fetch_assoc();
					$cms_login = date('Y-m-d H:i:s',strtotime($date .$row_login['MIN']));
				}

				$cms_logout = date('Y-m-d H:i:s',strtotime($date .$row_logout['MAX']));
				$cms_login = date('Y-m-d H:i:s',strtotime($previous_day .$row_login['MIN']));
				//echo $select . '<br>';
				$schudle_logout = date('Y-m-d H:i:s',strtotime($date .$logout_time)); // get real time logout to get variance
				$schudle_login = date('Y-m-d H:i:s',strtotime($date .$login_time)); // get real time login to get variance
				
				$variance_logout = ((strtotime($schudle_logout) - strtotime($cms_logout))) ;
				$variance_login = (strtotime($cms_login) - (strtotime($schudle_login) )) ;
				if ($variance_login < 360 and $variance_logout < 1800) {
					continue;
				}
				
			}


		}



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
			<td><?php echo date('Y-m-d',strtotime($date)) ;?></td>
			<td><?php echo $login_id ; ?></td>
			<td><?php echo date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time']))?></td>
			<td><?php echo $cms_login ;?></td>
			<td style="<?php echo $color_login; ?>"><?php echo $variance_login ; ?></td>
			<td><?php echo $cms_logout ; ?></td>
			<td style="<?php echo $color; ?>"><?php echo $variance_logout ; ?></td>
		</tr>
		<?php

		


		//$sql = "insert into data (date,name,schedule,login_time,tardy,logout_time,early_leave) values ('$date','$name','$schedule','$cms_login','$variance_login','$cms_logout','$variance_logout')";
		//$connect->query($sql);
		
	}
}

if ($_GET['export']) {
	?>
	<script>
		window.location.href='export.php?date=<?php echo $html_date ?>';
	</script>
	<?php
}