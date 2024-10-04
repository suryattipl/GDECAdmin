<?php 
require_once "header.php";
if (!isset($_SESSION['admin_name'])) {
    echo "<script>window.location='index.php';</script>";
}
if($_SERVER['HTTP_HOST'] == 'localhost'){
	$BASE_URL = 'http://localhost/rrc_scr_sports/';
}
else{
	$BASE_URL = 'https://www.ifinish.in/rrc_scr_sports/';
}


if($_SESSION['admin_type'] == 'D1'){
  $adminName = "Sri S.M.Naim, Sr. Sports/SCRSA/SC"; 
  $signature = "https://www.ifinish.in/rrc_scr_sports/admin/images/naim.png";
}
elseif($_SESSION['admin_type'] == 'D2'){
  $adminName = "Sri G.Balamurlaidhar, Sr.Sports/SCRSA/SC"; 
  $signature = "https://www.ifinish.in/rrc_scr_sports/admin/images/bm.png";
}
elseif($_SESSION['admin_type'] == 'D3'){
  $adminName = "Smt J.J.Shobha, OSD/Sports"; 
  $signature = "https://www.ifinish.in/rrc_scr_sports/admin/images/shobha.png";
}
else{
  $adminName = '';
  $signature = '';
}
    
$formError = '';
if(isset($_POST['saveChanges']) && $_POST['saveChanges'] == 'Submit'){
  if(isset($_POST['status']) && !empty($_POST['status'])){
    $regId = $_GET['id'];
    if($_SESSION['admin_type'] == 'S1'){
      mysqli_query($dbhandle,'UPDATE registration_details SET remarks1 = "'.$_POST['remarks'].'",approved_by_mohan = "'.$_POST['status'].'" WHERE registraionid = '.$regId.' ');
    }
    else{
      mysqli_query($dbhandle,'UPDATE registration_details SET remarks = "'.$_POST['remarks'].'",status = "'.$_POST['status'].'",approved_by="'.$adminName.'" WHERE registraionid = '.$regId.' ');
    }
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
	$get_upload_file_sts = "SELECT * FROM certificates WHERE registraionid = '".$_GET['id']."' ORDER BY id DESC";
	$resultsquery=mysqli_query($dbhandle,$get_upload_file_sts);
	$rows_sts=mysqli_fetch_assoc($resultsquery);

	$get_image_path_photo=$rows_sts['photo'];
$get_image_path_signature=$rows_sts['signature'];
$get_image_path_sports_achev=$rows_sts['sports_achev'];

$get_image_path_ebc_certificate=$rows_sts['ebc_certificate'];
$get_image_path_sc_st_certificate=$rows_sts['bc_certificate'];
$get_image_path_obc_certificate=$rows_sts['obc_certificate'];
$get_image_path_obc_creamy_certificate=$rows_sts['obc_creamy_certificate'];
$get_image_path_minority_certificate=$rows_sts['minority_certificate'];

$get_image_path_matric_certificate=$rows_sts['inter_certificate'];
$get_image_path_degree_certificate=$rows_sts['degree_certificate'];
$get_image_path_pg_certificate=$rows_sts['pg_certificate'];

$get_image_path_exserv_certificate=$rows_sts['exserv_certificate'];
$get_image_path_pwd_certificate=$rows_sts['pwd_certificate'];

$get_image_path_iti_certificate=$rows_sts['iti_certificate'];
$get_image_path_ssc_certificate=$rows_sts['ssc_certificate'];
$get_image_path_matri_certificate=$rows_sts['matriculation_certificate'];
	//echo $get_image_path_signature;
endif;
//echo "<pre>"; print_r($registrationList);die;
?>

<br><br>
<div class="container">
<p style="text-align:center;color: red;font-size: 18px;margin-bottom: 17px;text-decoration: underline;"><?php echo $formError?></p> 
<h4 style="text-align:center;color: #000;font-size: 28px;margin-bottom: 17px;text-decoration: underline;">Documents</h4>
<a class="btn btn-primary" target="_blank" href="finalDetails.php?id=<?php echo $_GET['id'];?>" style="text-align:center;color: #fff;font-size: 18px;margin-bottom: 17px;">View Application</a>
	<div id="projectFacts" class="sectionClass">
	    <div class="fullWidth eight columns">
	        <div class="projectFactsWrap ">
	            
                
                <?php			
                if (!empty($get_image_path_matri_certificate)) {
                    $matric_sts=$BASE_URL.$get_image_path_matri_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Matriculation</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $matric_sts; ?>" target="_blank">Click here to view Matriculation Certificate</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>
                <?php			
                if (!empty($get_image_path_iti_certificate)) {
                    $set_iti_sts=$BASE_URL.$get_image_path_iti_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>ITI Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $set_iti_sts; ?>" target="_blank">Click here to view ITI Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_ssc_certificate)) {
                    $set_iti_sts=$BASE_URL.$get_image_path_ssc_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>10+2 Cerificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $set_iti_sts; ?>" target="_blank">Click here to view 10+2 Cerificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>
	            
                <?php			
                if (!empty($get_image_path_degree_certificate)) {
                    $degree_sts=$BASE_URL.$get_image_path_degree_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Degree</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $degree_sts; ?>" target="_blank">Click here to view Degrees</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_pg_certificate)) {
                    $pg_sts=$BASE_URL.$get_image_path_pg_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Post Graduation</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $pg_sts; ?>" target="_blank">Click here to view Post Graduations</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_sports_achev)) {
                    $set_sport_sts=$BASE_URL.$get_image_path_sports_achev;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Sport Achievement</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $set_sport_sts; ?>" target="_blank">Click here to view Sport Achievements</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_ebc_certificate) && $getUserRegDetails['is_ebc'] == 1) {
                    $ebc_sts=$BASE_URL.$get_image_path_ebc_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>EBC Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $ebc_sts; ?>" target="_blank">Click here to view EBC Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_sc_st_certificate)) {
                    $scst_sts=$BASE_URL.$get_image_path_sc_st_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>SC/ST Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $scst_sts; ?>" target="_blank">Click here to view SC/ST Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_obc_certificate)) {
                    $obc_sts=$BASE_URL.$get_image_path_obc_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>OBC Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $obc_sts; ?>" target="_blank">Click here to view OBC Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_obc_creamy_certificate)) {
                    $obc_creamy_sts=$BASE_URL.$get_image_path_obc_creamy_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>OBC NON-Creamy Layer Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $obc_creamy_sts; ?>" target="_blank">Click here to view OBC NON-Creamy Layer Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_exserv_certificate)) {
                    $exserv_sts=$BASE_URL.$get_image_path_exserv_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Ex-serviceman Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $exserv_sts; ?>" target="_blank">Click here to view Ex-serviceman Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_pwd_certificate)) {
                    $pwd_sts=$BASE_URL.$get_image_path_pwd_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Persons with Disabilities Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $pwd_sts; ?>" target="_blank">Click here to view Persons with Disabilities Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>

                <?php			
                if (!empty($get_image_path_minority_certificate) && $getUserRegDetails['is_minority'] == 1) {
                    $set_min_sts=$BASE_URL.$get_image_path_minority_certificate;
                ?>
                <div class="item wow fadeInUpBig animated animated" style="visibility: visible;">
	                <p id="number1" class="number">
                        <p>Minority Certificate</p>
                        <span></span>
                        <p id="number2" class="number"><a href="<?php echo $set_min_sts; ?>" target="_blank">Click here to view Minority Certificates</a></p>      
                    </p>
	            </div>
                <?php
                }
                ?>
	        </div>
	    </div>
	</div>
  <?php
  $query1="select * from registration_details r where r.registraionid=".$_GET['id'];
  $result1=mysqli_query($dbhandle,$query1);
  $row = mysqli_fetch_assoc($result1);

  ?>
  <div class="row" style="margin-bottom:10px;">
  <div class="col-2"></div>
  <form style="margin-bottom: 10%;" id="applicationSubmit" name="applicationSubmit" method="post" action=''> 
  <?php if($_SESSION['admin_type'] == 'S1'){?>
    <p>Approval status By <?php echo $row['approved_by'];?> : <b><?php echo $row['status']?$row['status']:"Not yet updated";?></b></p>
    <p>Remarks : <?php echo $row['remarks']?$row['remarks']:"Not yet updated";?><p>
  <?php }?>
    <div class="form-group">
        <label>Remarks</label>
        <textarea style="width:700px" cols="8" rows="8" class="form-control" name="remarks" ><?php if($_SESSION['admin_type'] == 'S1'){echo $row['remarks1'];}else{echo $row['remarks'];}?></textarea>
    </div>
    <div class="form-group">
      <label>Status</label><br>
        <input <?php if($_SESSION['admin_type'] == 'S1'){if($row['approved_by_mohan'] == 'Accepted'){echo "checked";}}else{if($row['status'] == 'Accepted'){echo "checked";}}?> required style="margin-right: 12px;" type="radio" name="status" value="Accepted"><span style="font-size: 16px;margin-right: 14px;">Accept</span>
        <input <?php if($_SESSION['admin_type'] == 'S1'){if($row['approved_by_mohan'] == 'Rejected'){echo "checked";}}else{if($row['status'] == 'Rejected'){echo "checked";}}?> style="margin-right: 12px;" type="radio" name="status" value="Rejected"><span style="font-size: 16px;margin-right: 14px;">Reject</span>
    </div>
    <?php if($_SESSION['admin_type'] == 'S1'){?>
    <div class="form-group">
      <input checked  required type="checkbox" name="accept" value="yes"><span style="font-size: 16px;color: #000;"> Approved By <b>Sri J.Madan Mohan Reddy, SPO/IR<b></span><br><br>
      <img style="width:100px" src="https://www.ifinish.in/rrc_scr_sports/admin/images/mohan.png">
    </div>
    <?php }else{?>
    
      <div class="form-group">
      <input checked required type="checkbox" name="accept" value="yes"><span style="font-size: 16px;color: #000;"> Approved By <b><?php echo $adminName;?><b></span><br><br>
      <img style="width:100px" src="<?php echo $signature;?>">
    </div>
    <?php }?>
    <div class="form-group">
      <input type="submit" style="padding: 10px 40px 10px 40px;font-size: 16px;" class="btn btn-primary" name="saveChanges" value="Submit">
    </div>
    
  </form>
  <div class="col-4"></div>
  <div class="col-4"></div>
    
  </div>
</div>

