<?php

require_once "header.php";

error_reporting(0);


//$reg_id = 20000050;

$reg_id = $_GET['id'];


$query="select * from registration_details r, payments p where r.registraionid=p.registraionid and r.registraionid=".$reg_id;

$result=mysqli_query($dbhandle,$query);

$row = mysqli_fetch_assoc($result);

$Userinfo_sql=mysqli_query($dbhandle,"select username,password from userinfo Where regid=$reg_id");

$Userinfo = mysqli_fetch_assoc($Userinfo_sql);

if (!isset($_SESSION['admin_name'])) {
    echo "<script>window.location='index.php';</script>";
}







$statid1= $row['state'];

$districtid1= $row['district'];

$statid2= $row['state1'];

$districtid2= $row['district1'];



$state_sql=mysqli_query($dbhandle,"select statename from state_master Where stcode='".$statid1."'");

$stateinfo = mysqli_fetch_assoc($state_sql);



$district_sql=mysqli_query($dbhandle,"select districtname from district_master Where distcode='".$districtid1."'");

$districtinfo = mysqli_fetch_assoc($district_sql);



$altr_state_sql=mysqli_query($dbhandle,"select statename from state_master Where stcode='".$statid2."'");

$altr_stateinfo = mysqli_fetch_assoc($altr_state_sql);



$altr_district_sql=mysqli_query($dbhandle,"select districtname from district_master Where distcode='".$districtid2."'");

$altr_districtinfo = mysqli_fetch_assoc($altr_district_sql);



/*check image exists*/

$get_upload_file_sts = "SELECT * FROM certificates WHERE registraionid = '".$registerd_id."'";

$resultsquery=mysqli_query($dbhandle,$get_upload_file_sts);

if($rows_sts=mysqli_fetch_assoc($resultsquery))

$get_image_path_photo=$rows_sts['photo'];

$get_image_path_signature=$rows_sts['signature'];



?>



<div class="container containerfff">

	<div class="row">    

		<div class="col-md-12"> 

			<div class="panel registrationDetails"   id="divToPrint" >

				<!-- <h2 style="text-align:center;text-transform: uppercase;">Acknowledgement</h2> -->

				<div class="panel-body ">

					<style>

						table, th, td {

							border: 1px solid black;

							border-collapse: collapse;

						}

						th, td {

							padding: 5px;

							text-align: left;

						}

						td b {

							font-size: 14px;

						}

					</style>

					<style type="text/css" media="print">

						@page {

							size: auto;   /* auto is the initial value */

							margin: 0;  /* this affects the margin in the printer settings */

						}

					</style>

					<table style="width:100%;color:#000">

						<tr colspan="12">

							<th colspan="12"> 

								<div class="pull-left" style="float: left;width: 90%; margin: 0 auto;">

									<div style="font-size: 14px;line-height: 20px;width:100%;text-align:center;padding:10px 0px 10px 0px;"> 

										<img src="../images/rrc_logo.jpg" style="width: 10%;height:60px;vertical-align: middle;float: left;">

										<?php 



										echo '<span style="width: 90%;overflow: hidden;line-height: normal;float: left;margin: 20px 0 0;">APPLICATION FORM FOR SPORTS QUOTA RECRUITMENT 2021-22, SOUTH CENTRAL RAILWAY</span></div>';;

										?>

									</div> 

									

								</th>

							</tr>

							<tr>

								<th colspan="4" style="text-align: center;">Applicant's No <br/ ><?=$row['registraionid'];?></th>

								<?php /*
								<td colspan="2" style="text-align: center;"><img src="http://localhost/rrc_bhu/callletter/b/barcode.php?text=<?=$row['registraionid'];?>&size=50" /></td>
								*/ ?>
								<!-- <td colspan="2" style="text-align: center;"></td> -->

								<td rowspan="8" style="text-align: center;">

									<?php

									$img="https://www.ifinish.in/rrc_scr_sports/certificates/".$reg_id."_photo.jpg";



									?>

									<img src="<?php echo $img; ?>" style="max-width:200px;min-width:auto;height: 150px;">

								</td>

							</tr>

							<tr>

								<td colspan="" style="width: 16%;" >Applicant's Name</td>

								<td colspan="3"><b><?php echo strtoupper($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']);?></b></td>



							</tr>

							<tr>

								<td>Father's Name</td>

								<td colspan="3"><b><?php echo strtoupper($row['father_name']);?></b></td>

							</tr>

							<tr>

							</tr>

							<tr>

								<td>Mother's Name</td>

								<td colspan="3"><b><?php echo strtoupper($row['mother_name']);?></b></td>

							</tr>							

							<tr>

								<td>Marital Status </td>

								<td><b><?php if($row['marrital_status']==1) echo 'MARRIED'; else echo 'UNMARRIED';?></b></td>

								<td>Email Id</td>

								<td><b><?php echo $row['emailaddress'];



								?></b></td>

							</tr><tr></tr>

							<tr>

								<td>Mobile</td>

								<td><b><?php echo $row['mobileNumber'];?></b></td>

								<td>Post Applied For <br/></td>

								<td><b><?php echo strtoupper(str_replace('_',' ',$row['post_applied'])); ?> </b></td>

							</tr>

							<tr>

								<td>Gender<br/ > <b><?php echo strtoupper($row['sex']);?></b></td>

								<td>Date of Birth<br/ > <b><?php echo date('d-m-Y',strtotime($row['dob']));?></b></td>

								<td>Category</td><td> <b> <?php switch ($row['community']) {

									case "SC":

									echo "Scheduled Caste";

									break;

									case "GENERAL":

									echo "Unreserved";

									break;

									case "ST":

									echo "Scheduled Tribe";

									break;

									case "OBC":

									echo "Backward Caste";

									break;

									case "MUSLIMS":

									echo "Minority";

									break;

									case "CHRISTIANS":

									echo "Minority";

									break;

									case "SIKHS":

									echo "Sikhs";

									break;

									case "BUDDHISTS":

									echo "Buddhists";

									break;

									case "ZOROASTRIANS":

									echo "Zoroastrians";

									break;

									case "JAIN":

									echo "Jain";

									break;

								} ?> </b></td>



								<td></td>

							</tr>

							

							<tr>

								<td colspan="3">Identification Marks 1</td>

								<td colspan="3">Identification Marks 2</td>

							</tr>

							<tr>

								

								<td colspan="3"><b><?php echo $row['identity_marks'];?></b></td>



								

								<td colspan="3"><b><?php echo $row['identity_marks2'];?></b></td>

							</tr>



							<tr>

								<td colspan="3"><b>Permanent Address</b></td>

								<td colspan="3"><b>Mailing Address</b></td>

							</tr>

							<tr>

								<td colspan="1">Name & complete Permanent Address</td>

								<td colspan="2"><b><?php echo $row['address'];?></b></td>



								<td colspan="1">Name & complete Mailing Address</td>

								<td colspan="2"><b><?php echo $row['address1'];?></b></td>

							</tr>



							<tr>

								<td colspan="1">Land Mark</td>

								<td colspan="2"><b><?php echo $row['landmark'];?></b></td>



								<td colspan="1">Land Mark</td>

								<td colspan="2"><b><?php echo $row['landmark1'];?></b></td>

							</tr>





							<tr>

								<td colspan="1">Post Office</td>

								<td colspan="2"><b><?php echo $row['postoffice']; ?></b></td>



								<td colspan="1">Post Office</td>

								<td colspan="2"><b><?php echo $row['postoffice1']; ?></b></td>

							</tr>

							<tr>

								<td colspan="1">Police Station</td>

								<td colspan="2"><b><?php echo $row['ps']; ?></b></td>



								<td colspan="1">Police Station</td>

								<td colspan="2"><b><?php echo $row['ps1']; ?></b></td>

							</tr>

							<tr>

								<td colspan="1">State</td>

								<td colspan="2"><b><?php echo $stateinfo['statename']; ?></b></td>



								<td colspan="1">State</td>

								<td colspan="2"><b><?php echo $altr_stateinfo['statename']; ?></b></td>

							</tr>

							<tr>

								<td colspan="1">District</td>

								<td colspan="2"><b><?php echo $districtinfo['districtname']; ?></b></td>



								<td colspan="1">District</td>

								<td colspan="2"><b><?php echo $altr_districtinfo['districtname']; ?></b></td>

							</tr>

							<tr>

								<td colspan="1">Pin Code</td>

								<td colspan="2"><b><?php echo isset($row['pincode'] )&& $row['pincode']>0 ? $row['pincode'] : "";?></b></td>



								<td colspan="1">Pin Code</td>

								<td colspan="2"><b><?php echo isset($row['pincode1'] )&& $row['pincode1']>0 ? $row['pincode1'] : "";?></b></td>

							</tr>

							<tr>

								<td colspan="12"><b>Declaration</b></td>  

							</tr>

							<tr>

								<td colspan="4"  style="padding: 0 5px;">

									<p style="text-align:justify;padding-left: 5px;">

											1) I hereby declare that all statement made in this application are true, complete and correct to the best of

											my knowledge and belief. In the event of any information being 

											found false or incorrect, or ineligibility being detected before or after the examination, action can be taken

											against me by the Department.<br>

										</p>

										<p style="text-align:justify; padding-left: 5px;">

											2) I have read the advertisement notice carefully and I hereby undertake to abide by conditions.I further declare

											that I fullfill all conditions of eligibility regarding age limit, education qualification Prescribed for recruitment test / examination.

<!--2) I have read the provision in the rules and the Notice of the recruitment carefully and 

I hereby undertake to abide by them. I further declare that all the conditions of 

eligibility regarding age limit, education qualification Prescribed for admission to the examination.--><br>

	

<!--<?php if($row['in_service'] == 1  ){?>

<p style="text-align:justify; padding-left: 5px;">

3) 1 have informed my Head of Office/Department in writing that I am applying for this examination.<br></p>



<p>

	<?php } ?>	-->	

	<td width="15%" valign="top" style="line-height: 21px;text-align: center;">

		<img src="<?='https://www.ifinish.in/rrc_scr_sports/certificates/'.$reg_id.'_sign.jpg';?>" style="max-width:200px;min-width:auto;height: 90px;">

		<br><br><br>

		<b>Candidate Signature</b>

		<p></p>

		<p><b>Date : <?=date('d-m-Y',strtotime($row['date_created']));?></b></p>

	</td>  

</tr>



<?php /*
<tr>

	<td colspan="12"><p style="text-align:justify">

		Transaction Id. : <b>

			<?=$row['id'];?></b>, Registration Date & Time :<b> <?php  echo $row['date_created']; ?>

		</b></p></td>  

	</tr>
*/ ?>

</table>



</div>



</div>

<div class="text-center" style="margin-bottom:20px;">

	<input type="submit" class="submit-btn" name="print" id="print" Value="Print" onclick="PrintDiv();">

</div>

</div>

</div>



</div>

<script type="text/javascript">     

	function PrintDiv() {    

		var divToPrint = document.getElementById('divToPrint');

		var popupWin = window.open('', '_blank');

		popupWin.document.open();

		popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');

		popupWin.document.close();

	}

</script>
<?php

require "footer.php";

?>
