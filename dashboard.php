<?php 
require_once "header.php";
if (!isset($_SESSION['admin_name'])) {
    echo "<script>window.location='index.php';</script>";
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

	.projectFactsWrap .item{
	  width: 25%;
	  height: 100%;
	  padding: 50px 0px;
	  text-align: center;
	}

	.projectFactsWrap .item:nth-child(1){
	  background: rgb(16, 31, 46);
	}

	.projectFactsWrap .item:nth-child(2){
	  background: rgb(18, 34, 51);
	}

	.projectFactsWrap .item:nth-child(3){
	  background: rgb(21, 38, 56);
	}

	.projectFactsWrap .item:nth-child(4){
	  background: rgb(23, 44, 66);
	}

	.projectFactsWrap .item p.number{
	  font-size: 40px;
	  padding: 0;
	  font-weight: bold;
	}

	.projectFactsWrap .item p{
	  color: rgba(255, 255, 255, 0.8);
	  font-size: 18px;
	  margin: 0;
	  padding: 10px;
	  font-family: 'Open Sans';
	}


	.projectFactsWrap .item span{
	  width: 60px;
	  background: rgba(255, 255, 255, 0.8);
	  height: 2px;
	  display: block;
	  margin: 0 auto;
	}


	.projectFactsWrap .item i{
	  vertical-align: middle;
	  font-size: 50px;
	  color: rgba(255, 255, 255, 0.8);
	}


	.projectFactsWrap .item:hover i, .projectFactsWrap .item:hover p{
	  color: white;
	}

	.projectFactsWrap .item:hover span{
	  background: white;
	}

	@media (max-width: 786px){
	  .projectFactsWrap .item {
	     flex: 0 0 50%;
	  }
	}
</style>
<?php

if($_SESSION['admin_type'] == 'APPROVER'){
	$where_in = "AND  p.workdivision='".$_SESSION['division']."' "; 
}
else{
	$where_in = '';
}
//echo $where_in;die;
// $totalRegistrationCount     = mysqli_num_rows(mysqli_query($dbhandle,"SELECT registration_id FROM registration_details"));
// $totalApplicationCount      = mysqli_num_rows(mysqli_query($dbhandle,"SELECT r.registration_id FROM registration_details as r JOIN payments as p ON p.registration_id = r.registration_id WHERE r.application_status = 'FILLED'"));
// $totalSuccessCount     		= mysqli_num_rows(mysqli_query($dbhandle,"SELECT * FROM `payments` WHERE status = 'Success'"));
// $totalPendingCount     		= mysqli_num_rows(mysqli_query($dbhandle,"SELECT * FROM `payments` WHERE status is NULL"));

$getRegistrationList        = mysqli_query($dbhandle,"SELECT e.*,p.* FROM rrc_scr_2023.emp_details as e JOIN personal_details as p on p.pf_nps_no = e.pf_nps_no where p.application_status='COMPLETED' ".$where_in." ");
if(mysqli_num_rows($getRegistrationList) > 0){
	$registrationList 		= array();
	while($row = mysqli_fetch_assoc($getRegistrationList)){
		array_push($registrationList,$row);
	}
}
//echo "<pre>"; print_r($registrationList);die;
?>


<div class="container">
	<h4 style="text-align:center;color: #000;font-size: 28px;margin-bottom: 17px;text-decoration: underline;">Successfull Registration Details</h4>
	<table class="table table-bordered" id="mytable">
		<thead>
			<th>S.no</th>
			<th>Registration Id</th>
			<th>Name</th>
			<th>Email</th>
			<th>Gender</th>
			<th>Community</th>
			<th>Division</th>
			<th>Documents</th>
			<?php if($_SESSION['admin_type'] == 'admin'){?>
			<th>Approved By</th>
			<th>Action</th>
			<th>Status</th>
			<th>Remarks</th>
			<?php }else{?>
			<th>Status</th>
			<th>Remarks</th>
			<?php }?>
		</thead>
		<tbody>
			<?php if(isset($registrationList) && !empty($registrationList)):$i=1;foreach($registrationList as $registers):
				
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $registers['registration_id'];?></td>
				<td><?php echo ucwords(strtolower($registers['employee_name']));?></td>
				<td><?php echo $registers['mail'];?></td>
				<td><?php echo ucfirst($registers['gender']);?></td>
				<td><?php echo $registers['community'];?></td>
				<td><?php echo str_replace('_',' ',$registers['workdivision']);?></td>
				<td><a href="documents.php?id=<?php echo $registers['registration_id'];?>">View Documents</a></td>
				<?php if($_SESSION['admin_type'] == 'admin'){?>
					
					<td><?php echo $registers['approved_by'];?></td>
				<td>
				
					<?php if($registers['updated'] == 1){?>
						<a class="btn btn-primary" href="enable_edit.php?id=<?php echo $registers['registration_id'];?>">Enable Edit</a>
					<?php }?>
				</td>
				<?php }?>
				<td><?php echo $registers['status'];?></td>
				<td><?php echo $registers['remarks'];?></td>
				
			</tr>
			<?php $i++;endforeach;endif;?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function() {
	var dataTable = $('#mytable').DataTable({
                "pageLength": 10,
                "buttons": [{
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'

                    ]
                }],
            });
});
</script>
