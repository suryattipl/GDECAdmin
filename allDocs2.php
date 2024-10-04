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
?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script> 
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>  
<?php

$get_upload_file_sts = "SELECT c.* FROM ifinish_rrc_sec_sports.registration_details as r JOIN ifinish_rrc_sec_sports.certificates as c ON c.registraionid = r.registraionid JOIN ifinish_rrc_sec_sports.payments as p ON p.registraionid = r.registraionid WHERE p.status = 'Success' AND p.payment_status = 1 GROUP BY r.registraionid;";
$resultsquery=mysqli_query($dbhandle,$get_upload_file_sts);
while($rows_sts=mysqli_fetch_assoc($resultsquery)){
    $docsArray[] = $rows_sts;
}
//echo "<pre>"; print_r($docsArray);die;
// $get_image_path_photo=$rows_sts['photo'];
// $get_image_path_signature=$rows_sts['signature'];
// $get_image_path_sports_achev=$rows_sts['sports_achev'];

// $get_image_path_ebc_certificate=$rows_sts['ebc_certificate'];
// $get_image_path_sc_st_certificate=$rows_sts['bc_certificate'];
// $get_image_path_obc_certificate=$rows_sts['obc_certificate'];
// $get_image_path_obc_creamy_certificate=$rows_sts['obc_creamy_certificate'];
// $get_image_path_minority_certificate=$rows_sts['minority_certificate'];

// $get_image_path_matric_certificate=$rows_sts['inter_certificate'];
// $get_image_path_degree_certificate=$rows_sts['degree_certificate'];
// $get_image_path_pg_certificate=$rows_sts['pg_certificate'];

// $get_image_path_exserv_certificate=$rows_sts['exserv_certificate'];
// $get_image_path_pwd_certificate=$rows_sts['pwd_certificate'];

// $get_image_path_iti_certificate=$rows_sts['iti_certificate'];
// $get_image_path_ssc_certificate=$rows_sts['ssc_certificate'];
// $get_image_path_matri_certificate=$rows_sts['matriculation_certificate'];
	//echo $get_image_path_signature;

//echo "<pre>"; print_r($registrationList);die;
?>
<div class="container containerfff" id="mainWrapper">
   <div class="row">
      <div class="col-md-12">
         <div class="panel">
            <div class="panel-body">
               <!-- marquee style='color:red'>Registrations for Scouts and Guides will open tentatively from 10.12.19 12:00 hrs.</marquee-->
               <div class="row">
                  <div class="col-md-12" align='center'>
                     <div class="panel panel-default">
                        <div class="panel-body">
                           <div class="row">
                              <?php if(isset($docsArray) && !empty($docsArray)):$i=1; foreach($docsArray as $certi):
                                 
                                 if($certi['ebc_certificate']){
                                    $getExtension = explode('.',$certi['ebc_certificate']);
                                    @$extension = $getExtension[1];
                                    if($extension == 'pdf'){
                                       $path2 = "<iframe src=".$BASE_URL.$certi['ebc_certificate']."></iframe>"; 
                                    }
                                    else{
                                       $path2 = "<img width='50%' src=".$BASE_URL.$certi['ebc_certificate'].">";
                                    }
   
                                    ?>
                                
                                  <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                    <p><?php echo $certi['registraionid']?></p>
                                    <p>EBC Certificate</p>
                                    <?php echo $path2;?>
                                  </div>
                                  <?php }?>

                                  <?php
                                  if($certi['bc_certificate']){
                                     $getExtension = explode('.',$certi['bc_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['bc_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['bc_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>BC Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['obc_certificate']){
                                     $getExtension = explode('.',$certi['obc_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['obc_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['obc_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>OBC Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['minority_certificate']){
                                     $getExtension = explode('.',$certi['minority_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['minority_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['minority_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>Minority Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['matriculation_certificate']){
                                     $getExtension = explode('.',$certi['matriculation_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['matriculation_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['matriculation_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>Matriculation Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['iti_certificate']){
                                     $getExtension = explode('.',$certi['iti_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['iti_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['iti_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>ITI Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['ssc_certificate']){
                                     $getExtension = explode('.',$certi['ssc_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['ssc_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['ssc_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>10+2 Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['degree_certificate']){
                                     $getExtension = explode('.',$certi['degree_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['degree_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['degree_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>Degree Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>

                                   <?php
                                  if($certi['pg_certificate']){
                                     $getExtension = explode('.',$certi['pg_certificate']);
                                     @$extension = $getExtension[1];
                                     if($extension == 'pdf'){
                                        $path2 = "<iframe src=".$BASE_URL.$certi['pg_certificate']."></iframe>"; 
                                     }
                                     else{
                                        $path2 = "<img width='50%' src=".$BASE_URL.$certi['pg_certificate'].">";
                                     }
    
                                     ?>
                                 
                                   <div style="border: 2px solid #000;" class="certificates col-md-4 col-sm-4 col-lg-4">
                                     <p><?php echo $certi['registraionid']?></p>
                                     <p>PG Certificate</p>
                                     <?php echo $path2;?>
                                   </div>
                                   <?php }?>
                                  
                                  
                              <?php $i++;endforeach;endif;?>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

