<?php
//error_reporting(0);

?>
<style>
.error{
  width :500px;
  background-color: #efefef;
  padding: 20px;
  border: 1px solid #efefef;
  border-radius: 10px;
  margin: 0 auto;
  margin-top: 30px;
} 

.form-group{
  width: 90%;
  height: 50px;
  margin: 0 auto;
  text-align: center;
  border-radius: 5px;
  border: 1px solid #0000002e;
  line-height: 45px;
  margin-bottom: 50px;
  margin-top: 20px;
}

.info{
  width:90% ;
  height:100px;
  margin: 0 auto;
  background-color: #efefef;
  text-align: center;
  padding: 20px;
  border: 1px solid #efefef;
  border-radius: 10px;
}
.error_txt{
  color: red;
  text-align: center;
}


.copyright{
  position: fixed;
  bottom: 10;
  margin: 0 auto;
  text-align: center;
  width: 100%;
  font-size: 13px;
  height: 20px;
  background-color: #efefef;
  border: 1px solid #efefef;
  border-radius: 10px;

}

</style>
<script src="jquery.js"></script>


<div class="form-group" >

  <form enctype="multipart/form-data" method="post" role="form" id="form">
    <label for="exampleInputFile">File Upload</label>
    <input type="file" name="file" id="file" size="150" required="required">
    <button type="submit" class="btn btn-default" name="Import" value="Import" id="submit">Upload</button>

  </form>

</div>



	<div class="info">
		 
       <span> Activation_AR;Activation_EN;Activation_postpaid;CAM_INT;CAM_PRIM;CAM_PRM;CAM_STR;CCVIP;Corp_ Activation;Cutomer_support;Data_Cust_supp;IndegoADSL;M_Backoffice;M_Email;M_Postpaid_ar;M_Postpaid_en;Postpaid Assist;WALLET_BO;Credit_Control;EU_INT;EU_PRIM;EU_PRM;EU_STR;SPOC_Sup_INT;
       </span>
       <span>
       	SPOC_Sup_PRIM;SPOC_Sup_PRM;SPOC_Sup_STR;TravelBack_AR;TravelBack_EN;WALLET_NonSUB;WALLET_SUB;M_Outbound
       </span>
	</div>






<div class="form-group"  >

  <form enctype="multipart/form-data" method="post" role="form" action="attend/export1.php">
    <label for="exampleInputFile">Choose Date</label>
    <input type="date" name="date" id="file" size="150" >
    <button type="submit" class="btn btn-default" name="export" value="export" id="submit">Export Data</button>
    <button type="submit" class="btn btn-default" name="lost_time" value="lost_time" id="submit" >Get Lost Time</button>

  </form>


</div>

<div id='result'>
  
</div>


<script>

    $(function(){
    $('#form').submit(function(e){
      e.preventDefault();

      var t = new FormData(this);
      $.ajax({
        url : 'attend/upload.php',
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

<div class="copyright">
  Development by Saeed Sharaf
</div>


