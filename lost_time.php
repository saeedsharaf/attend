<style>
	table ,th,td{
		border: 1px solid black;
		border-collapse: collapse;
		padding: 5px;
		text-align: center;;
	}

	.color{
		background-color: blue;
		color: white; 
	}
</style>
<?php

//$date = '2019-12-30';
//echo date("Y-m-d H:i:s",strtotime('+1 hour',strtotime($date)));
//$sql = "select * from process where schedule = '' and login_id = '638926'";


$result = $connect->query($sql);
if($result->num_rows > 0){
	$output .='
	<table>
	<tr>
		<th class="color">Shift Date</th>
		<th class="color">Login_id</th>
		<th class="color">Name</th>
		<th class="color">SCh Login & LogOut</th>
		<th class="color">From</th>
		<th class="color">To</th>
		<th class="color">Duration</th>
	</tr>';

	while($row = $result->fetch_assoc()){
		$login_id = $row['login_id'];
		$date = $row['date'];
		$name = $row['name'];
		$schedule = date("h:i A",strtotime($row['login_time'])) . ' : ' . date("h:i A",strtotime($row['logout_time'])) ;

		$adjust_logout = date('H:i:s',strtotime('+2 hour',strtotime($row['logout_time'])));
		$login_time = date('Y-m-d H:i:s',strtotime('-2 hour',strtotime($row['date'] .$row['login_time'])));
		
		if ($row['login_time'] >= '15:00:00' and $row['login_time'] <= '23:00:00'){ // identfy betwwen shift

			$logout_time = date('Y-m-d H:i:s',strtotime('+1 Day',strtotime($row['date'] .$adjust_logout)));

		}else{// morining shift
		
			$logout_time = date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($row['date'] .$row['logout_time']))); 

		}


		$lost_time = "select * from cms where login_id = '$login_id' and login_date >= '$login_time' and logout_date <= '$logout_time'";
		//$lost_time = "select * from cms where login_id = '638366' and login_date >= '2019-12-30 06:00:00' and logout_date <= '2019-12-30 19:00:00'";
	
		$lost_time_result = $connect->query($lost_time);
		$lost_login = 0;
		$lost_logout = 0 ;
		while ($lost_row = $lost_time_result->fetch_assoc()) {
			if ($lost_logout == 0) { // skip first loob to get value of logout at the end of code 
				$lost_logout = strtotime($lost_row['logout_date']);
				continue;
			}
			$lost_login = strtotime($lost_row['login_date']);
			
			$lost_n = ($lost_login - $lost_logout) ;

			
			if ($lost_n > 0) {
				$lost_t = date('H:i:s',(($lost_login - $lost_logout) - (60*60)));
				if ($lost_t >= '00:30:00') {// to echo time if more than 30 min 

			
				//echo $lost_row['date'] . '>> ' . $login_id . '><><>'. $lost_t . ' From : '. date('h:i:s A',$lost_logout) . ' To : ' . date('h:i:s A',$lost_login) . '<br>'; 
	//echo $lost_time . ' >>> ' . $lost_t. ' >'. date('Y-m-d H:i:s',$lost_login) . ' > '. $lost_row['id']. ' : ' . date('Y-m-d H:i:s',$lost_logout).'>' . $logout_id .'<br>';
				
				
				list($hour,$min,$sec) = explode(':',$lost_t);
				$output.='
				<tr>
					<td>'. $date .'</td>
					<td>'. $login_id .'</td>
					<td>'. $name .'</td>
					<td>'. date("h:i A",strtotime($row['login_time'])) . ':' . date("h:i A",strtotime($row['logout_time'])) .'</td>
					<td>'. date('h:i:s A',$lost_logout) .'</td>
					<td>'. date('h:i:s A',$lost_login).'</td>
					<td>'. $lost_t .'</td>

				</tr>
				';

				$hour1 += $hour ;
				$min1 += $min ;
				$sec1 += $sec ;

			}	
		}	
		
			
			$lost_logout = strtotime($lost_row['logout_date']);


			//echo $lost_row['date'] . '>> ' . $login_id . '><><>'. $lost_t . '<br>';			
		}
		
	}
	$output .='
		</table>';
		header("content-type: application/'xls");
		header("content-disposition:attachment;filename=LostTime.xls");

	//echo 'Total : ' .  $hour1 . ':' . $min1 . ':' . $sec1 . '<br>';
}
	echo $output;



?>