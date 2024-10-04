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

$where_in = "AND  r.registraionid IN(48266,41945,46861,40642,43455,40676,54582,43314,42917,47472,45732,45484,43662,43839,40895,43529,40964,41007,41445,40868,54691,53332,53478,53753,54071,50922,51654,41277,48225,45800,41665,43159,42969,41257,41115,41065,41053,41989,41492,41376,54317,47462,45825,54874,54979,53564,52819,53140,52530,52514,51689,50505,51222,49834,49890,46208,45535,42471,41701,41643,41507,40677,40674,41015,53409,53578,51516,50558,47501,54344,52983,52999,53075,48166,51734,51579,49824,49807,49780,49760,49733,49452,47703,46343,45970,45985,45952,54930,54048,53313,51730,51633,42448,45524,43591,41543,41489,41312,41512)"; 
//echo $where_in;die;

$getRegistrationList        = mysqli_query($dbhandle,"SELECT r.*,p.payment_amount,p.paid_amount,p.paid_on FROM registration_details as r JOIN payments as p ON p.registraionid = r.registraionid WHERE p.status = 'Success' ".$where_in." ORDER BY p.paid_on DESC");
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
			<th>Documents</th>
		</thead>
		<tbody>
			<?php if(isset($registrationList) && !empty($registrationList)):$i=1;foreach($registrationList as $registers):
				
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $registers['registraionid'];?></td>
				<td><?php echo ucwords(strtolower($registers['first_name'].' '.$registers['last_name']));?></td>
				<td><?php echo $registers['emailaddress'];?></td>
				<td><?php echo ucfirst($registers['sex']);?></td>
				<td><?php echo $registers['community'];?></td>
				
				<td><a href="documents.php?id=<?php echo $registers['registraionid'];?>">View Documents</a></td>
				
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
