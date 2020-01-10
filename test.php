<?php
$connect= mysqli_connect('localhost','root','','attend');
$date = '2019-12-30';
//echo date("Y-m-d H:i:s",strtotime('+1 hour',strtotime($date)));
$sql = "select * from process where schedule = '' and date = '$date'";
$result = $connect->query($sql);
if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		$login_id = $row['login_id'];
		$date = $row['date'];

		$adjust_logout = date('H:i:s',strtotime('+2 hour',strtotime($row['logout_time'])));
		$login_time = date('Y-m-d H:i:s',strtotime('-2 hour',strtotime($row['date'] .$row['login_time'])));
		
		if ($row['login_time'] >= '15:00:00' and $row['login_time'] <= '23:00:00'){ // identfy betwwen shift

			$logout_time = date('Y-m-d H:i:s',strtotime('+1 Day',strtotime($row['date'] .$adjust_logout)));

		}else{// morining shift
		
			$logout_time = date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($row['date'] .$row['logout_time']))); 

		}


		$lost_time = "select * from cms where login_id = '$login_id' and login_date >= '$login_time' and logout_date <= '$logout_time'";
		
		$lost_time_result = $connect->query($lost_time);
		$lost_login = 0;
		$lost_logout = 0 ;
		while ($lost_row = $lost_time_result->fetch_assoc()) {
			if ($lost_logout == 0) {
				$lost_logout = strtotime(date('Y-m-d H:i:s',strtotime($lost_row['date'] . $lost_row['logout'])));
				continue;
			}
			$lost_login = strtotime(date('Y-m-d H:i:s',strtotime($lost_row['date'] . $lost_row['login'])));
			$lost_t = date('H:i:s',(($lost_login - $lost_logout) - (60*60)));

			if ($lost_t > '00:30:00') { // to echo time if more than 30 min 
				//echo $lost_row['date'] . '>> ' . $login_id . '><><>'. $lost_t . ' From : '. date('h:i:s A',$lost_logout) . ' To : ' . date('h:i:s A',$lost_login) . '<br>'; 
				echo $lost_row['date'] . '>> ' . $login_id . '><><>'. $lost_t . '<br>';	
			}

			$lost_logout = strtotime(date('Y-m-d H:i:s',strtotime($lost_row['date'] . $lost_row['logout'])));
			list($hour,$min,$sec) = explode(':',$lost_t);
			$hour1 += $hour ;
			$min1 += $min ;
			$sec1 += $sec ;
			

			//echo $lost_row['date'] . '>> ' . $login_id . '><><>'. $lost_t . '<br>';			
		}
	}

	echo 'Total : ' .  $hour1 . ':' . $min1 . ':' . $sec1 . '<br>';
}




?>