<style type="text/css">
	table ,th,td{
		border: 1px solid black;
		border-collapse: collapse;
		padding: 5px;
		text-align: center;;
	}

	.color{
		background-color: #24318e;
		color: white; 
	}
</style>

<?php
set_time_limit(3000);
$connect= mysqli_connect('localhost','root','','attend');
$html_date = $_POST['date'];
$output = '';
if (isset($_POST['date'])) {
	$sql = "select * from process where schedule = '' and date = '$html_date'";
}else{
	$sql = "select * from process where schedule = ''";
}

$result = $connect->query($sql);
if($result->num_rows > 0){
	$output .='
	<table>
	<tr>
		<th class="color">Shift Date</th>
		<th class="color">Login_id</th>
		<th class="color">Name</th>
		<th class="color">SCh Login & LogOut</th>
		<th class="color">Login_date</th>
		<th class="color">Tardy login</th>
		<th class="color">Act logout Date</th>
		<th class="color">Logout_Variance</th>
	</tr>';
	while($row = $result->fetch_assoc()){
		$login_id = $row['login_id'];
		$name = addslashes($row['name']);
		//$login_time = $row['login_time'];
		$date = $row['date'];
		$logout_time_increase = date('H:i:s',strtotime('+4 hour',strtotime($row['logout_time'])));

		$login_time = date('Y-m-d H:i:s',strtotime($row['date'] .$row['login_time']));
		$adjust_login_time = date('Y-m-d H:i:s',strtotime('-3 hour',strtotime($row['date'] .$row['login_time'])));
		$schedule = date("h:i A",strtotime($row['login_time'])) . ' : ' . date("h:i A",strtotime($row['logout_time'])) ;


		if ($row['login_time'] > $row['logout_time']){ // identfy betwwen shift
			$adjust_logout_time = date('Y-m-d H:i:s',strtotime('+1 Day',strtotime($row['date'] .$logout_time_increase)));

		}else{ // identfy morining shift
			$adjust_logout_time = date('Y-m-d H:i:s',strtotime('+4 hour',strtotime($row['date'] .$row['logout_time']))); 
		}
		

		$sql_cms = "select max(logout_date) as max_logout , min(login_date) as min_login from cms where login_id = '$login_id' and login_date >= '$adjust_login_time' and logout_date <= '$adjust_logout_time'"	;
		echo $sql_cms . '<br>';

		$result_cms = $connect->query($sql_cms);
		$row_cms = $result_cms->fetch_assoc();
		if($row_cms['max_logout'] == '' and $row_cms['min_login'] == ''){
			$output.='
				<tr>
					<td>'. $date .'</td>
					<td>'. $login_id .'</td>
					<td>'. $name .'</td>
					<td>'. date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) .'</td>
					<td></td>
					<td></td>
					<td></td>
					<td>Absent</td>

				</tr>
				';
				continue;
		}

		$cms_logout = $row_cms['max_logout'];
		$cms_login = $row_cms['min_login'];

		$schudle_logout = date('Y-m-d H:i:s',strtotime($date .$adjust_logout_time)); // get real time logout to get variance
		$schudle_login = date('Y-m-d H:i:s',strtotime($date .$row['login_time'])); // get real time login to get variance
		$variance_logout = ((strtotime($schudle_logout) - strtotime($cms_logout))) ;
		$variance_login = (strtotime($cms_login) - (strtotime($schudle_login) )) ;
		if ($variance_login < 360 and $variance_logout < 1800) { // if login less than 6 min and logout create than 30 min
			continue;
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

		$output.="
				<tr>
					<td>". $date ."</td>
					<td>". $login_id ."</td>
					<td>". $name ."</td>
					<td>". date('h:i A',strtotime($row['login_time'])) . ':' . date('h:i A',strtotime($row['logout_time'])) ."</td>
					<td>". $cms_login."</td>
					<td>". $variance_login."</td>
					<td>". $cms_logout ."</td>
					<td>". $variance_logout ."</td>
				</tr>";
				
	}
	$output .='
	</table>';
	header("content-type: application/'xls");
	header("content-disposition:attachment;filename=test.xls");

}
echo $output;
					
?>