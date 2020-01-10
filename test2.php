<?php
$connect = mysqli_connect('localhost','root','','test');
set_time_limit(0);
//////////// below code using transaction method to insert in table //////////////////////
/*
$file = fopen('s1.txt','r');

$sql = "insert into sch (txt) values ";
$sql1 = '';

while(!feof($file)){
	$ff = fgets($file);
	$sql1 .= "('$ff'),";

}

$sql1 = mb_substr($sql1,0,-1) ;
$sql = $sql.$sql1;
echo $sql . '<br';
$connect->query($sql);
echo $connect->error . '<br>';

*/
/////////////////////////////////////////////
?>
<form  method="post" enctype="multipart/form-data" id="t">
<input type="file" name="file" id="ss">
<input type="submit" name="submit" id='submit'>
</form>
<div id='result'>
	
</div>
<?php
/*
if($_POST['submit']){
	$file = $_FILES['file']['tmp_name'];
	//echo $file . '<br>' ;


	$target_dir = "D:/Xampp/mysql/data/test/"; // location of file you want to move it
	$file = $_FILES['file']['name']; // name of file and extention 
	$path = pathinfo($file); // to get info from file like path info and name and extention 
	$filename = $path['filename']; // get file name from path  info
	$ext = $path['extension']; // get extention from pathingo 
	$temp_name = $_FILES['file']['tmp_name']; // the file it self
	$path_filename_ext = $target_dir.$filename.".".$ext; // complete file info + target path
	
	if (file_exists($path_filename_ext)) {
	 echo "Sorry, file already exists.";
 	}else{
 	move_uploaded_file($temp_name,$path_filename_ext);
 	echo "Congratulations! File Uploaded Successfully.";
 	}

 		$sql = "load data infile '$file' into table sch 
 		LINES TERMINATED BY '\n'
 		
 		(txt)";
 		echo $sql . '<br>';

 		//FIELDS TERMINATED BY '\t' // below code if you want terminated tabed field
	//$connect->query($sql);
	//echo $connect->error ;

}
*/


 

?>
<script src="jquery.js"></script>
<script>
	$(function(){
		$('#t').submit(function(e){
			e.preventDefault();

			var t = new FormData(this);
			$.ajax({
				url : 'test3.php',
				type : 'post',
				//contentType : 'multipart/form-data',
				processData: false,
				contentType: false,
       			cache: false,
				data : t,
				success : function(txt,status,xhr){
					console.log(txt);
					$('#result').html(txt);
				},

				error : function(xhr,status,error){
					console.log(xhr);
					console.log(status);
					console.log(error);

				}
			})
		})
	})
</script>