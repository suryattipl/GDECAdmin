<?php 
require_once "header.php";
if (!isset($_SESSION['admin_name'])) {
    echo "<script>window.location='index.php';</script>";
}
if($_SERVER['HTTP_HOST'] == 'localhost'){
	$BASE_URL = 'http://localhost/GDECAdmin/';
}
else{
	$BASE_URL = 'https://www.ifinish.in/GDECAdmin/';
}

// echo md5('6^GS9"N<NTd/(^}q').'<br>';
// echo md5('7H]gK>x<3vzN]+^W').'<br>';
// echo md5('khyg+3p@DsV5,zW"').'<br>';
// echo md5('S+,.tW%`qKMFW?9$').'<br>';
// echo md5('Z@v*t<(-[uft8bJ!').'<br>';

$adminName = $_SESSION['admin_name']; 
$adminId  = $_SESSION['admin_id']; 
    
$formError = '';
if(isset($_POST['saveChanges']) && $_POST['saveChanges'] == 'Submit'){
  if(isset($_POST['status']) && !empty($_POST['status'])){
    $regId = $_GET['id'];
    date_default_timezone_set('Asia/Kolkata');
   // echo 'INSERT INTO approval_status (remarks,status,approved_by,registrationid) VALUES("'.$_POST['remarks'].'","'.$_POST['status'].'","'.$adminName.'",  '.$regId.') ';die;
    mysqli_query($dbhandle,'INSERT INTO approval_status (remarks,status,approved_by,approver_id,registration_id,ip_address,date) VALUES("'.$_POST['remarks'].'","'.$_POST['status'].'","'.$adminName.'","'.$adminId.'",  '.$regId.',"'.$_SERVER['REMOTE_ADDR'].'","'.date('Y-m-d h:i:s A').'") ');
    mysqli_query($dbhandle,'UPDATE personal_details SET remarks = "'.$_POST['remarks'].'",status = "'.$_POST['status'].'",approved_by = "'.$adminName.'",approver_id = "'.$adminId.'",updated=1 WHERE registration_id = '.$regId.' ');
   
    echo "<script>alert('Status Updated !!!');window.location='dashboard.php';</script>";
  }
  else{
    $formError = 'Please Choose Status.';
  }
}
?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script> 
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>  
<script type="text/javascript" src = "https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src = "https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src = "//cdn.datatables.net/buttons/1.1.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src = "//cdn.datatables.net/buttons/1.1.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src = "//cdn.datatables.net/buttons/1.1.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src = "https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
<style type="text/css">
	@import url(https://fonts.googleapis.com/css?family=PT+Sans+Narrow);
	body {
	  font-family: Open Sans, "Helvetica Neue", "Helvetica", Helvetica, Arial,   sans-serif;
	  font-size: 13px;
	  color: #666;
	  position: relative;
	  -webkit-font-smoothing: antialiased;
	  margin: 0;
	}

	* {
	  -webkit-box-sizing: border-box;
	  -moz-box-sizing: border-box;
	  box-sizing: border-box;
	}

	body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, form, p, blockquote, th, td {
	  margin: 0;
	  padding: 0;
	  font-size: 13px;
	  direction: ltr;
	}

	.sectionClass {
	  padding: 20px 0px 50px 0px;
	  position: relative;
	  display: block;
	}

	.fullWidth {
	  width: 100% !important;
	  display: table;
	  float: none;
	  padding: 0;
	  min-height: 1px;
	  height: 100%;
	  position: relative;
	}


	.sectiontitle {
	  background-position: center;
	  margin: 30px 0 0px;
	  text-align: center;
	  min-height: 20px;
	}

	.sectiontitle h2 {
	  font-size: 30px;
	  color: #222;
	  margin-bottom: 0px;
	  padding-right: 10px;
	  padding-left: 10px;
	}


	.headerLine {
	  width: 160px;
	  height: 2px;
	  display: inline-block;
	  background: #101F2E;
	}


	.projectFactsWrap{
	    display: flex;
	  margin-top: 30px;
	  flex-direction: row;
	  flex-wrap: wrap;
	}


	#projectFacts .fullWidth{
	  padding: 0;
	}

	.projectFactsWrap .item {
        width: 23%;
        height: 236px;
        padding: 40px 0px;
        margin-right: 19px;
        text-align: center;
        border: 3px solid #000;
        background: #fff;
        margin-bottom: 19px;
    }


	.projectFactsWrap .item p.number{
        margin-top: 13px;
        padding: 0;
	  font-weight: bold;
	}

	.projectFactsWrap .item p{
	  color: #000;
	  font-size: 18px;
	  margin: 0;
	  padding: 10px;
	  font-family: 'Open Sans';
	}


	.projectFactsWrap .item span{
	  width: 60px;
	  background: #000;
	  height: 2px;
	  display: block;
	  margin: 0 auto;
	}


	.projectFactsWrap .item i{
	  vertical-align: middle;
	  font-size: 50px;
	  color: #000;
	}


	.projectFactsWrap .item:hover i, .projectFactsWrap .item:hover p{
	  color: #000;
	}

	.projectFactsWrap .item:hover span{
	  background: #000;
	}
    p#number2 a {
    color: #000;
    font-size: 16px;
}

	@media (max-width: 786px){
	  .projectFactsWrap .item {
	     flex: 0 0 50%;
	  }
	}
</style>
<?php
if(isset($_GET['id']) && !empty($_GET['id'])):
  $rows_sts = array();
  $get_upload_file_sts = "SELECT * FROM certificates WHERE registration_id = '".$_GET['id']."' and filepath !='' ORDER BY id DESC";
  $resultsquery=mysqli_query($dbhandle,$get_upload_file_sts);
  while($rows=mysqli_fetch_assoc($resultsquery)){
    $rows_sts[] = $rows;
  };

  $rows_sts1 = array();
  $get_upload_file_sts1 = "SELECT * FROM education_details WHERE registration_id = '".$_GET['id']."' ORDER BY id DESC";
  $resultsquery1=mysqli_query($dbhandle,$get_upload_file_sts1);
  while($rows=mysqli_fetch_assoc($resultsquery1)){
    $rows_sts1[] = $rows;
  };
  // echo "<pre>";print_r($rows_sts1);die;

  $get_profile = "SELECT * FROM personal_details WHERE registration_id = '".$_GET['id']."'";
  $profilequery=mysqli_query($dbhandle,$get_profile);
  $profileData=mysqli_fetch_assoc($profilequery);



  $get_image_path_photo=$profileData['photo_path'];
  $get_image_path_signature=$profileData['sign_path'];

endif;
//echo "<pre>"; print_r($registrationList);die;
?>

<br><br>
<div class="container">
<p style="text-align:center;color: red;font-size: 18px;margin-bottom: 17px;text-decoration: underline;"><?php echo $formError?></p> 
<h4 style="text-align:center;color: #000;font-size: 28px;margin-bottom: 17px;text-decoration: underline;">Documents</h4>
<a class="btn btn-primary" target="_blank" href="https://gdce.iroams.com/RRC_ADMIN/acknowledge?registerId=<?php echo $_GET['id'];?>" style="text-align:center;color: #fff;font-size: 18px;margin-bottom: 17px;">View Application</a>
	<div id="projectFacts" class="sectionClass">
	    <div class="fullWidth eight columns">
	        <div class="projectFactsWrap ">
	            
              <?php			
                if (isset($rows_sts1) && (!empty($rows_sts1))) foreach($rows_sts1 as $certi){
                ?>
                  <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
                    <p id="number1" class="number">
                          <p><?=$certi['exam_passed'];?></p>
                          <span></span>
                          <p id="number2" class="number"><a href="https://gdce.iroams.com/gdce_2024/s3image?folder=certificates/&name=<?php echo $certi['certificate_path']; ?>" target="_blank">Click here to view Certificate</a></p>      
                      </p>
                  </div>
                <?php
                }
                ?>
                
                <?php			
                if (isset($rows_sts) && (!empty($rows_sts))) foreach($rows_sts as $certi){
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                         <p><?=$certi['filename'];?></p>
                        <span></span>
                        <p id="number2" class="number"><a href="https://gdce.iroams.com/gdce_2024/s3image?folder=certificates/&name=<?php echo $certi['filepath']; ?>" target="_blank">Click here to view Certificate</a></p>      
                    </p>
	              </div>
                <?php
                }
                ?>
	        </div>
	    </div>
	</div>
  
  <?php if($_SESSION['admin_type'] != 'admin'){?>
  <?php
  
  $query1="select * from personal_details r where r.registration_id=".$_GET['id'];
  $result1=mysqli_query($dbhandle,$query1);
  $row = mysqli_fetch_assoc($result1);
// print_r($row);
  ?>
  <div class="row" style="margin-bottom:10px;">
  <div class="col-2"></div>
  <form style="margin-bottom: 10%;" id="applicationSubmit" name="applicationSubmit" method="post" action=''> 

    <div class="form-group">
        <label>Remarks</label>
        <textarea style="width:700px" cols="8" rows="8" class="form-control" name="remarks" ><?php echo $row['remarks']?></textarea>
    </div>
    <div class="form-group">
      <label>Status</label><br>
        
        <input <?php if($row['status'] == 'Accepted'){echo "checked";}?> required style="margin-right: 12px;" type="radio" name="status" value="Accepted"><span style="font-size: 16px;margin-right: 14px;">Accept</span>
        <input <?php if($row['status'] == 'Rejected'){echo "checked";}?> style="margin-right: 12px;" type="radio" name="status" value="Rejected"><span style="font-size: 16px;margin-right: 14px;">Reject</span>
        <input <?php if($row['status'] == 'Onhold'){echo "checked";}?> style="margin-right: 12px;" type="radio" name="status" value="Onhold"><span style="font-size: 16px;margin-right: 14px;">On hold </span>
        
      </div>
    
    
      <div class="form-group">
      <input checked required type="checkbox" name="accept" value="yes"><span style="font-size: 16px;color: #000;"> Approved By <b><?php echo $_SESSION['admin_name'];?><b></span><br><br>
      <!-- <img style="width:28%" src="<?php echo $signature;?>"> -->
    </div>
   
   
    <?php if($row['updated'] == '' || $row['updated'] == '0'){?>
      <div class="form-group">
        <input type="submit" style="padding: 10px 40px 10px 40px;font-size: 16px;" class="btn btn-primary" name="saveChanges" value="Submit">
      </div>
      <?php }?>
  </form>
  <?php }?>
    
    
  <div class="col-4"></div>
  <div class="col-4"></div>
    
  </div>
</div>

