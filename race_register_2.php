<?php
require_once 'templates/header.php';
require_once "includes/PFBC/Form.php";
echo '
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>';

$e = $db->getRow("SELECT `name`,CONCAT('EL_',md5(id),'.jpg') as event_logo,`shortcode`,`date` FROM `ifinish_events` WHERE id = " . $QS_Values['event']);
if (empty($_POST) && (!isset($QS_Values['reg_id']) && !isset($QS_Values['bulk_reg_id']))) {
	redirect(WEB_ROOT . 'event_details/' . $e['shortcode']);
}
if (isset($_POST['event_distance']) && count($_POST['event_distance']) > 0) {
	$_POST['event_distance'] = array_merge(array_diff($_POST['event_distance'], ["other"]));
}
if (isset($_POST['ngo_id']) && !empty($_POST['ngo_id'])) {
	$ngo_id = $_POST['ngo_id'];
} else {
	$ngo_id = 0;
}

if (isset($_POST['purchase_charity_bib']) && !empty($_POST['purchase_charity_bib']) && $_POST['purchase_charity_bib'] == 1 && $_POST['event_id'] == '9678') {
	$purchase_charity_bib = $_POST['purchase_charity_bib'];
} else {
	$purchase_charity_bib = 0;
}

if (isset($_POST['spoky_id']) && !empty($_POST['spoky_id'])) {
	$spoky_id = $_POST['spoky_id'];
} else {
	$spoky_id = 0;
}

if (isset($_POST['submission_type']) && !empty($_POST['submission_type'])) {
	$submission_type = $_POST['submission_type'];
} else {
	$submission_type = 0;
}
if (isset($_POST["extra_field"]["714"]) && !empty($_POST["extra_field"]["714"]) && $_POST["extra_field"]["714"][0] == "Yes") {
	$is_student = 1;
} else {
	$is_student = 0;
}
//echo "<pre>";print_r($_POST);die;
$val = isset($_POST['count_div']) ? $_POST['count_div'] : "";
echo "<input type='hidden' name='reg-inner_count' value='" . $val . "' />";
$event_id = isset($_POST['event_id']) ?  $_POST['event_id'] : $QS_Values['event'];
$e = $db->getRow("SELECT `name`,`date`,`amount_international` FROM `ifinish_events` WHERE id = " . $event_id);

if (isset($QS_Values['event']) && isset($_POST['runner_race'])) {

	$data = array();

	if (is_array($_POST['runner_race']) && count($_POST['runner_race']) > 1) {
		$is_bulk =  1;
	} else {
		$is_bulk =  0;
	}
	if (isset($_POST['bulk_id'])) {
		$data['id'] = $_POST['bulk_id'];
	}
	// Guest user login
	if (isset($_SESSION['guest'])) {
		$user_data1 = array();
		if ($is_bulk == 1) {
			$user_data1['mobile'] 		= $_POST['head_mobile'];
			$user_data1['email'] 		= $_POST['head_email'];
			$user_data1['first_name'] 	= $_POST['head_name'];
			$_POST['user_id'] 			= $data['user_id'] 			= getUserInfo($user_data1);
			//get the userId from db
		}
	}
	$data['event_id'] = $_POST['event_id'];

	######################## For Bulk Registration #############################
	if ($is_bulk == 1) {
		$data['name'] 			= $_POST['head_name'];
		$data['email'] 			= $_POST['head_email'];
		$data['user_id'] 		= $_POST['user_id'];
		$data['mobile'] 		= $_POST['head_mobile'];
		$data['country'] 		= $_POST['head_country'];
		$data['organization'] 	= isset($_POST['organization']) ? $_POST['organization'] : null;
		if ($event_id == 9607 || $event_id == 9434 || $event_id == 9602 || $event_id == 9682 || $e['amount_international'] == 1) {
			if (isset($_POST['country']) && !empty($_POST['country'])) {
				if (strtolower($_POST['country'][0]) != 'india') {
					$data['paid_in'] = "USD";
				}
			}
		}
		if ($event_id == 9612) {
			$raceIdsInter = array('7371','7372','7373');
			if (isset($_POST['runner_race']) && !empty($_POST['runner_race'])) {
				if (in_array($_POST['runner_race'][0], $raceIdsInter)) {
					$data['paid_in'] = "USD";
					$_SESSION['currentInternationalRace'] = 'USD';
				}
			}
		}
		$bulk = new bulkRegistrations();
		$bulk->load($data);
		$bulk_regid = $bulk->save();
		unset($_POST['head_name']);
		unset($_POST['head_email']);
		unset($_POST['head_mobile']);
		unset($_POST['head_mobile']);
		unset($_POST['head_countryname']);
	} else {
		$bulk_regid = 0;
	}
	######################## For Bulk Registration End ##########################

	$data = array();
	$extra_cols = $db->getrows("SELECT * FROM `ifinish_registration_extra_columns` WHERE event_id = " . $event_id, MYSQLI_ASSOC);
	foreach ($_POST['runner_race'] as $key => $race) {
		$userCountry = $_POST['country'][$key];

		$logData = $_POST['event_id'] . '-' . $race . '-(' . $_POST['dob'][$key] . ')-' . $_POST['mobile'][$key] . '-' . $_POST['first_name'][$key] . '-' . $_POST['email'][$key];

		file_put_contents(LOGS_DOC_ROOT . "/register/" . date('Y-m-d') . ".log", date('h:i:s A') . ' Data ' . json_encode($_POST) . "\n", FILE_APPEND);
		$regArray = array(
			'event_id'		=> $_POST['event_id'],
			'race_id'		=> $race,
			'bulk_id'		=> $bulk_regid,
			'user_IP'		=> get_client_ip(),
			'browser'		=> $_SERVER['HTTP_USER_AGENT']
		);
		if (@$category = findCategory($race, $_POST['dob'][$key], $_POST['gender'][$key], $_POST['event_id'])) {
			$regArray['category_id'] = $category['id'];
		}
		$user_details = array(
			'mobile'	=> isset($_POST['mobile']) ? $_POST['mobile'][$key] : "",
			'email'		=> isset($_POST['email']) ? $_POST['email'][$key] : "",
			'first_name' => $_POST['first_name'][$key]
		);

		if (isset($_POST['reg_id'])) {
			$regArray['id'] = $_POST['reg_id'];
			$reg_data_id = $db->getValue("SELECT id FROM `ifinish_registration_data` WHERE reg_id = " . $_POST['reg_id']);
		}
		if (isset($_POST['spoky_id'])) {
			$regArray['spoky_id'] = $_POST['spoky_id'];
		}
		$regArray['app_type'] = $submission_type;

		$regArray['user_id'] = getUserInfo($user_details);
		if (isset($_POST['bulk_id'])) {
			$regArray['id'] = $_POST['bulk_reg_id'][$key];
			$reg_data_id = $db->getValue("SELECT id FROM `ifinish_registration_data` WHERE reg_id = " . $regArray['id']);
		}
		
		if ($event_id == 9447 || $event_id == 9607 || $event_id == 9434 || $event_id == 9602 || $event_id == 9682 || $e['amount_international'] == 1) {
			if (isset($_POST['country']) && !empty($_POST['country'])) {
				if (strtolower($_POST['country'][0]) != 'india') {
					$regArray['paid_in'] = "USD";
				}
			}
		}
		if ($event_id == 9612) {
			$raceIdsInter = array('7371','7372','7373');
			if (isset($_POST['runner_race']) && !empty($_POST['runner_race'])) {
				if (in_array($_POST['runner_race'][0], $raceIdsInter)) {
					$regArray['paid_in'] = "USD";
					$_SESSION['currentInternationalRace'] = 'USD';
				}
			}
		}
		
		$reg = new Registration();
		$reg->load($regArray);
		$reg_id = $reg->save();


		foreach ($_POST as $Postkey => $dataValue1) {
			if (isset($dataValue1) and is_array($dataValue1)) {
				if (isset($dataValue1[$key])) {
					$regDataArray[$Postkey] = $dataValue1[$key];
				}
			}
			if ($Postkey == "target_finish_time") {
				$regDataArray['target_finish_time'] = str_replace(" ", "", $dataValue1[$key]);
			}
			if ($Postkey == "completed_timing") {
				$regDataArray['completed_timing'] = str_replace(" ", "", $dataValue1[$key]);
			}
			if ($Postkey == "dob") {
				$regDataArray['dob'] = date('Y-m-d', strtotime($dataValue1[$key]));
			}
		}
		$regDataArray['reg_id'] = $reg_id;
		if (isset($reg_data_id)) {
			$regDataArray['id'] = $reg_data_id;
		}
		$regDataArray['idproof'] = 'ID_' . md5($reg_id) . '.jpg';
		$regDataArray['country_code']	=	$_POST['country_code'];
		
		######################## Save Registration Data ##########################
		$regData = new RegistrationData();
		$regData->load($regDataArray);
		$regData_id = $regData->save();
		######################## Save Registration Data ##########################
		if (isset($_POST['pasta'][$key])) {
			$db->query("insert INTO `ifinish_registration_extra_values` (`registration_id`, `column_id`, `value`) VALUES ('" . $reg_id . "', '7', '" . $_POST['pasta'][$key] . "')");
		}
		if (!file_exists('uploads/' . $_POST['event_id'])) {
			mkdir('uploads/' . $_POST['event_id'], 0777, true);
		}
		$is_event_folder = $_POST['event_id'];
		$upload_name = $reg_id;
		/* Registration Photograph */
		$regDataArray = array();
		foreach ($extra_cols as $id => $value) {
			if ($value['label_type'] == 'File') {
				//echo "Hello____".$_FILES['extra_field']['type'][$value['id']][$key];die;
				if (isset($_FILES['extra_field']['type'][$value['id']][$key])) {
					if ($_FILES['extra_field']['type'][$value['id']][$key] == 'image/jpeg' or $_FILES['extra_field']['type'][$value['id']][$key] == 'image/png') {

						$file_destination = 'uploads/' . $is_event_folder . '/EX' . $value['id'] . '_' . md5($upload_name) . '.jpg';

						$tmp_name = $_FILES["extra_field"]["tmp_name"][$value['id']][$key];
						// 	printr($file_destination);
						// exit;
						move_uploaded_file($tmp_name, $file_destination);
						$db->query("REPLACE INTO `ifinish_registration_extra_values` (`registration_id`, `column_id`, `value`) VALUES ('" . $reg_id . "', '" . $value['id'] . "', '" . $file_destination . "')");
					}
					elseif ($_FILES['extra_field']['type'][$value['id']][$key] == 'application/pdf') {

						$file_destination = 'uploads/' . $is_event_folder . '/EX' . $value['id'] . '_' . md5($upload_name) . '.pdf';

						$tmp_name = $_FILES["extra_field"]["tmp_name"][$value['id']][$key];
						// 	printr($file_destination);
						// exit;
						move_uploaded_file($tmp_name, $file_destination);
						$db->query("REPLACE INTO `ifinish_registration_extra_values` (`registration_id`, `column_id`, `value`) VALUES ('" . $reg_id . "', '" . $value['id'] . "', '" . $file_destination . "')");
					}
				}
			}

			if (isset($_POST['extra_field'][$value['id']][$key]) and $_POST['extra_field'][$value['id']][$key] != "") {
				$db->query("REPLACE INTO `ifinish_registration_extra_values` (`registration_id`, `column_id`, `value`) VALUES ('" . $reg_id . "', '" . $value['id'] . "', '" . $_POST['extra_field'][$value['id']][$key] . "')");
			}
		}

		if (isset($_FILES['address_proof']['type'][$key])) {
			if ($_FILES['address_proof']['type'][$key] == 'image/jpeg' or $_FILES['address_proof']['type'][$key] == 'image/png') {

				$file_destination = 'uploads/' . $is_event_folder . '/AID_' . md5($upload_name) . '.jpg';

				$tmp_name = $_FILES["address_proof"]["tmp_name"][$key];
				move_uploaded_file($tmp_name, $file_destination);
			}
		}

		if (isset($_FILES['crop_IDphoto']['type'][$key])) {
			$file_des = 'uploads/' . $is_event_folder . '/ID_' . md5($upload_name) . '.jpg';
			if ($_FILES['crop_IDphoto']['type'][$key] == 'image/jpeg' or $_FILES['crop_IDphoto']['type'][$key] == 'image/jpg' or $_FILES['crop_IDphoto']['type'][$key] == 'image/png') {

				$tmp_name = $_FILES["crop_IDphoto"]["tmp_name"][$key];
				move_uploaded_file($tmp_name, $file_des);
			} else if (!isset($_POST['reg_id']) && !isset($_POST['bulk_id'])) {
				$useridproof_exist = 'uploads/ID_' . md5($upload_name) . '.jpg';
				$header_response2 = @get_headers($useridproof_exist);
				if ($header_response2[0] != 'HTTP/1.1 404 Not Found') {
					copy($useridproof_exist, $file_des);
				}
			}
		}

		if (isset($_FILES['crop_photo']['type'][$key])) {
			$file_destination1 = 'uploads/' . $is_event_folder . '/UPI_' . md5($upload_name) . '.jpg';
			file_put_contents(LOGS_DOC_ROOT . "/register/" . date('Y-m-d') . ".log", date('h:i:s A') . ' Image zero logs - ' . $reg_id . "\n----\n\n", FILE_APPEND);
			if ($_FILES['crop_photo']['type'][$key] == 'image/jpeg' or  $_FILES['crop_photo']['type'][$key] == 'image/jpg' or $_FILES['crop_photo']['type'][$key] == 'image/png') {

				file_put_contents(LOGS_DOC_ROOT . "/register/" . date('Y-m-d') . ".log", date('h:i:s A') . ' Image first logs - ' . $reg_id . "\n----\n\n", FILE_APPEND);
				$tmp_name = $_FILES["crop_photo"]["tmp_name"][$key];
				move_uploaded_file($tmp_name, $file_destination1);
			} else if (!isset($_POST['reg_id']) && !isset($_POST['bulk_id'])) {
				$useridproof_exist = 'uploads/UPI_' . md5($regArray['user_id']) . '.jpg';
				$header_response2 = @get_headers($useridproof_exist);
				if ($header_response2[0] != 'HTTP/1.1 404 Not Found') {
					copy($useridproof_exist, $file_destination1);
				}
				file_put_contents(LOGS_DOC_ROOT . "/register/" . date('Y-m-d') . ".log", date('h:i:s A') . ' Image 2nd logs - ' . $reg_id . "\n----\n\n", FILE_APPEND);
			} else {
				file_put_contents(LOGS_DOC_ROOT . "/register/" . date('Y-m-d') . ".log", date('h:i:s A') . ' Image 3rd logs - ' . $reg_id . "\n----\n\n", FILE_APPEND);
			}
		}

		if (isset($_FILES['timingcer_proof']['type'])) {

			if ($_FILES['timingcer_proof']['type'][$key] == 'image/jpeg' or $_FILES['timingcer_proof']['type'][$key] == 'image/jpg' or  $_FILES['timingcer_proof']['type'][$key] == 'image/png') {
				$file_destination = 'uploads/' . $_POST['event_id'] . '/CER_' . md5($reg_id) . '.jpg';

				$tmp_name = $_FILES["timingcer_proof"]["tmp_name"][$key];
				move_uploaded_file($tmp_name, $file_destination);
			}
		}

		if (isset($_POST['profile_update']) && $_POST['profile_update'] == 1) {
			if (isset($regArray['user_id']) and $regArray['user_id'] > 0) {

				unset($regDataArray['email']);
				
				$userBasic = new Profile();
				$regDataArray['id'] = $regArray['user_id'];

				$userBasic->load($regDataArray);
				$userBasic->save();

				$user_details_id = $db->getOne("SELECT id FROM `ifinish_user_details` WHERE user_id = " . $regArray['user_id']);
				if ($user_details_id > 0) {
					$regDataArray['id'] = $user_details_id;
				} else {
					unset($regDataArray['id']);
				}
				if (isset($file_des)) {
					$copy_destination = 'uploads/ID_' . md5($regArray['user_id']) . '.jpg';
					copy($file_des, $copy_destination);
				}
				if (isset($file_destination1)) {
					$copy_destination_UPI = 'uploads/UPI_' . md5($regArray['user_id']) . '.jpg';
					copy($file_destination1, $copy_destination_UPI);
				}

				$regDataArray['user_id'] = $regArray['user_id'];
				unset($regDataArray['reg_id']);
				unset($regDataArray['runner_race']);
				$userDetails = new UserDetails();
				$userDetails->load($regDataArray);
				$userDetails->save();
			}
		}
	}
} else {
	if (isset($QS_Values['bulk_reg_id'])) {
		$bulk_regid = $QS_Values['bulk_reg_id'];
	} else if (isset($QS_Values['reg_id'])) {
		$reg_id = $QS_Values['reg_id'];
		$bulk_regid = 0;
	}
}
if (isset($bulk_regid) and $bulk_regid > 0) {
	$where = 'AND R.id =  RD.reg_id
		AND R.bulk_id = "' . $bulk_regid . '"';
	$bul_reg = $db->getRow("SELECT country FROM `ifinish_registration_bulk` WHERE id = " . $bulk_regid);
	$regId = 0;
	$ahm_user_discount = $_SESSION['users']['id'];
} else {
	$regId = $reg_id;
	$where = 'AND R.id = ' . $reg_id . ' and R.id =  RD.reg_id';
	/*AHM 9 years discount counts for 2020 */
	$ahm_user_discount = $regArray['user_id'];
}
if (isset($event_id) && $event_id == 9407 || isset($event_id) && $event_id == 9502 ) {
	$ahm_9years_data_count = $db->getValue("select count(*) as count from ifinish_results_attached a, ifinish_results r where  r.id=a.result_id and r.event_id IN (21 , 38, 74, 114, 197, 324, 417, 571, 753)  and a.user_id='" . $ahm_user_discount . "' and tagged_status in (0,2) ");
	if (isset($ahm_9years_data_count) && $ahm_9years_data_count > 0) {
		$ahm_discount = $db->getRow("select percentage,coupon_name from ahm2020_discounts where count=$ahm_9years_data_count");
	}
}
$query = "
	SELECT 
		RD.reg_id,RD.first_name, RD.middle_name, RD.last_name,R.race_id,R.id,R.user_id,
		RD.gender, RD.dob, RD.email, RD.mobile, RD.tshirt_size,RD.country,
		RACE.name as race_name,RACE.charity_amount,RACE.purchase_bib_amt,
		RACE.amount as race_amount, RACE.`amount_international`
	FROM 
		`ifinish_event_race` RACE, 
		`ifinish_registration` R,
		`ifinish_registration_data` RD
	WHERE  
		RACE.id =  R.race_id 
		" . $where;

$races = $db->getRows($query);

$totalcount = 0;
$e = $db->getRow("SELECT processing_fee,tax,tax_type,swachh_bharat_cess,name,iscoupon_reg_start,`tags`,amount_international FROM `ifinish_events` WHERE id = " . $event_id);
function array_implode_with_keys($array)
{
	$return = '';
	if (count($array) > 0) {
		foreach ($array as $key => $value) {
			$return .= $key . '||||' . $value . '----';
		}
		$return = substr($return, 0, strlen($return) - 4);
	}
	return $return;
}

function array_explode_with_keys($string)
{
	$return = array();
	$pieces = explode('----', $string);
	foreach ($pieces as $piece) {
		$keyval = explode('||||', $piece);
		if (count($keyval) > 1) {
			$return[$keyval[0]] = $keyval[1];
		} else {
			$return[$keyval[0]] = '';
		}
	}
	return $return;
}
?>
<style type="text/css">
	.custom-form-control-placeholder {
		opacity: 1;
		padding: 10px 0 0 0px;
		transform: translate3d(0, -3rem, 0);
	}

	.charitypicker {
		width: 100%;
		border: 1px solid #ddd;
		border-radius: 10px;
		padding: 12px;
		outline: unset;
	}

	.reg-accordion-block .row {
		margin: 0px !important;
	}

	.amount-select .option-one,
	.amount-select .option-last {
		padding: 4px 15px;
		border: 1px solid #eee;
		border-radius: 10px;
		box-sizing: content-box;
		margin: 10px 0;
	}

	.amount-select .donation-box {
		border-radius: 10px;
		display: flex;
		justify-content: space-between;
		cursor: pointer;
		outline: 0;
	}

	.donation-box .input-circle {
		width: 20px;
		height: 20px;
		margin-right: 10px;
		flex-basis: 20px;
		border-radius: 50%;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		border: 1px solid #b6c6ac;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
	}

	.amount-select .donation-box .input-circle i {
		display: none;
	}

	.amount-select .donation-box.active .input-circle i {
		display: block;
		color: #e0a806;
		font-size: 24px;
	}

	.donation-box.active .input-circle {
		border: unset;
	}

	.donation-box label input[type=number] {
		width: 95px;
		max-width: 95px;
		height: 20px;
		padding: 0 !important;
		font-size: 1em;
		border: 0;
		background: transparent;
		overflow: hidden;
		text-align: right;
	}

	.donation-box label input::-webkit-input-placeholder {
		font-size: 15px;
	}

	input:focus::-webkit-input-placeholder {
		color: transparent;
	}

	.donation-box.enter-amount label {
		padding-bottom: 0;
		line-height: 20px;
		border: 1px solid #dadada;
		background: #fff;
		padding: 6px;
		border-radius: 4px;
		justify-content: space-between;
		align-items: center;
	}

	.donation-box label {
		padding-bottom: 4px;
		line-height: 26px;
	}

	.donation-box.active label input[type=number] {
		color: #0b0b0b;
		outline: 0;
	}

	.donation-box label input[type=number] {
		width: 120px;
		max-width: 124px;
		height: 20px;
		padding: 0 !important;
		border: 0;
		background: transparent;
		overflow: hidden;
		text-align: right;
		outline: 0;
	}

	input[type=number]::-webkit-inner-spin-button,
	input[type=number]::-webkit-outer-spin-button {
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		margin: 0;
	}

	.donation {
		display: none
	}

	.remove_coupon {
		color: #ec482fd6;
		font-size: 11px;
		position: absolute;
		cursor: pointer;
	}

	.remove_coupon:hover {
		color: #ec482f;
	}
</style>
<section class="userContent eventList">
	<div class="container content-wrapper">
		<div class="homeContainer">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="reg-accordion" style="padding-top: 40px;">
						<div class="reg-accordion-block">

							<div class="clearfix"></div>
							<?php if ($event_id == 9447) { ?>
								<div class="row" style="display: flex;align-items: center;">
									<div class="col-md-8">
										<h4 class="reg_header"><?= $e['name']; ?></h4>
									</div>
									<div class="col-md-1"></div>
									<div class="col-md-2">
										<img src="https://www.ifinish.in/images/vm_logo.png" class="pull-right mr-5" width="100">
									</div>
									<div class="col-md-1"></div>
								</div>
							<?php } else { ?>
								<h4 class="reg_header"><?= $e['name']; ?></h4>
							<?php } ?>

							<p style="text-align: center;font-size: 15px;color: #ca420f;padding: 10px 10px 10px 0px;font-weight: bold;display:none" id="amtmismatch_err"></p>

							<?php
							if (isset($QS_Values['payment']) and $QS_Values['payment'] == 'failed') { ?>
								<div class="alert alert-danger">
									<button class="close"><i class="icon-remove"></i></button>
									Payment Transaction Failed, Please Try Again.
								</div>
							<?php }


							?>
							<div style="margin-top:14px !important;min-height:300px;">
								<div class="regblocktoggle">
									<div class="margin0100">
										<div class="col-md-12 col-sm-12 col-xs-12 bulk_reg_blocks_mobile">

											<?php
											$totalAmount 		= 0;
											$coupons_counts 	= 0;
											$choose_type 		= array();
											$extra_amount 		= 0;
											$daycare_amount  	= 0;
											$TASALLI 			= 0;
											$Ananda 			= 0;
											$sgst 				= 0;
											$cgst 				= 0;
											$gst 				= 0;
											$actual_amount 		= 0;
											$actual_gst 		= 0;
											$cgstpercentage 	= "9%";
											$sgstpercentage 	= "9%";
											$gstpercentage 		= "18%";
											$raceAmountsvalues 	= "";
											$raceAmountgst 		= 0;
											$ahm_net_amt 		= 0;
											$dscount_amt 		= 0;
											$get_registered_2020= 0;

											$userID = isset($_SESSION['users']['id']) ? $_SESSION['users']['id'] : "";
											if (isset($bulk_regid) and $bulk_regid > 0) {
												if ($event_id == 9607 || $event_id == 9434 || $event_id == 9602 || $event_id == 9682) {
													$get_regs = $db->getRow("select id from  ifinish_registration where bulk_id=" . $bulk_regid . " limit 1 ", MYSQLI_ASSOC);
													$userState = $db->getRow("select country,state from  ifinish_registration_data where reg_id=" . $get_regs['id'], MYSQLI_ASSOC);
												} else {
													$userState = $db->getRow("select country from  ifinish_registration_bulk where id=" . $bulk_regid, MYSQLI_ASSOC);
												}
											} else {
												$userState = $db->getRow("select country,state from  ifinish_registration_data where reg_id=" . $regId, MYSQLI_ASSOC);
											}
											if ($event_id == 9607 || $event_id == 9434 || $event_id == 9602) {
												$type =  $db->getValue("select currency_code from ifinish_counties 
								 				where country='" . $userState['country'] . "'");
						
											} else {
												$type = "INR";
												
											}
											
											if ($event_id == 9447 || $event_id == 9602 || $event_id == 9682) {
												if (strtolower($userState['country']) == 'india') {
													$type = "INR";
												} else {
													$type = "USD";
												}
											}
											
											if ($event_id == 9612) {
												$raceIdsInter = array('7371','7372','7373');
												if (isset($_POST['runner_race']) && !empty($_POST['runner_race'])) {
													if (in_array($_POST['runner_race'][0], $raceIdsInter)) {
														$type = "USD";
													}
												}
											}
											

											$hasDonation = 0;
											$ic= 1;foreach ($races as $keys => $data) {
												if($event_id == 9690){
													
													if($is_bulk == 1){
														$grmbaseAmt 	+=	$data['race_amount'];
														$totalBulk 	=	count($_POST['runner_race']);
														if($totalBulk >=10 && $totalBulk < 15){
															if ($data['gender'] == 'female') {
																$discountByUserssi 	=	($data['race_amount'] * 15) / 100;
																$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
															}else{
																$discountByUserssi 	=	($data['race_amount'] * 10) / 100;
																$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
															}
														}
														elseif($totalBulk >=15 && $totalBulk < 20){
																$discountByUserssi 	=	($data['race_amount']*15)/100;
																$discountAmtBulk = $data['race_amount']-$discountByUserssi;
														}
														elseif($totalBulk >=20){
																$discountByUserssi 	=	($data['race_amount']*20)/100;
																$discountAmtBulk = $data['race_amount']-$discountByUserssi;
														}else{
															if ($data['gender'] == 'female') {
																$discountByUserssi 	=	($data['race_amount'] * 15) / 100;
																$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
															}else{
																$discountAmtBulk = $data['race_amount'];
															}
														}
													}else{
														if($data['gender']=='female'){
															$discountByUserssi 	=	($data['race_amount'] * 15) / 100;
															$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
														}else{
															$discountAmtBulk = $data['race_amount'];
														}
													}
												}

												if($event_id == 9825){
													
													if($is_bulk == 1){
														$grmbaseAmt 	+=	$data['race_amount'];
														$totalBulk 	=	count($_POST['runner_race']);
														if($totalBulk >=10){
															$discountByUserssi 	=	($data['race_amount']*10)/100;
															$discountAmtBulk = $data['race_amount']-$discountByUserssi;
														}
														elseif ($data['gender'] == 'female') {
															$discountByUserssi 	=	($data['race_amount'] * 10) / 100;
															$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
														}	
														else{
															$discountAmtBulk = $data['race_amount'];
														}
													}else{
														if($data['gender']=='female'){
															$discountByUserssi 	=	($data['race_amount'] * 10) / 100;
															$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
														}else{
															$discountAmtBulk = $data['race_amount'];
														}
													}
												}
												if ($event_id == 9722) {
													if ($is_bulk == 1) {
														$grmbaseAmt 	+=	$data['race_amount'];
														$totalBulk 	=	count($_POST['runner_race']);
														if ($totalBulk >= 10 && $totalBulk < 50) {
																$discountByUserssi 	=	($data['race_amount'] * 10) / 100;
																$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
														} elseif ($totalBulk >=50 && $totalBulk <100) {
															$discountByUserssi 	=	($data['race_amount'] * 15) / 100;
															$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
														} elseif ($totalBulk >=100) {
															$discountByUserssi 	=	($data['race_amount'] * 20) / 100;
															$discountAmtBulk = $data['race_amount'] - $discountByUserssi;
														} 
													} 
												}
												
												$rent_cycle =  0;
												$breakfast = 0;
												$user_extraFiels = $db->getRows("SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id = " . $data['reg_id'] . "  and column_id in (581,625,626,622,624,509,547,628,633,634,653,712,713,714,721,788,816,819,820,821,827,828,886,887,914,915,918,919) order by id asc");
												
												if ($event_id == 347  && $data['race_id'] != 5314) {
													if (isset($QS_Values['choose_type'])) {
														$_POST['choose_type'] = array_explode_with_keys($QS_Values['choose_type']);
													}
													$choose_type[$keys] = $_POST['choose_type'][$keys];
													$data['race_amount'] = isset($_POST['choose_type']) ? $_POST['choose_type'][$keys] : $QS_Values['choose_type'][$keys];
												}

												if ($event_id == 9607 || $event_id == 9434 || $event_id == 9602) {
													$countryAmt = graceRunAmount($event_id, $data['race_id'], $data['country']);
													$data['race_amount'] = ($countryAmt != '') ? $countryAmt : 3;
												}
												

												if (sizeof($user_extraFiels) > 0) {
													foreach ($user_extraFiels as $extra => $ex_val) {
														if ($ex_val['column_id'] == 136) {
															$TASALLI = $Ananda + $ex_val['value'];
														}

														if ($ex_val['column_id'] == 443) {

															$data['race_amount'] = $data['race_amount'] + ($ex_val['value'] * 50);
														}

														if ($ex_val['column_id'] == 477) {
															if ($ex_val['value'] == 'Yes') {
																$rent_cycle  = 300;
																$totalAmount += $rent_cycle;
															}
														}

														if ($ex_val['column_id'] == 721) {
															if ($ex_val['value'] == 'Yes') {
																$valAmount = 200;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 816) {
															if ($ex_val['value'] == 1) {
																$valAmount = 250;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}

														if ($ex_val['column_id'] == 581) {
															if ($ex_val['value'] == 'Yes') {
																$rent_cycle  = 1000;
																$totalAmount += $rent_cycle;
															}
														}
														if ($ex_val['column_id'] == 886) {
															if ($ex_val['value'] == 1) {
																$cycleRent  = 200;
																$totalAmount += $cycleRent;
															}
														}
														if ($ex_val['column_id'] == 887) {
															if ($ex_val['value'] == 1) {
																$trail_part  = 100;
																$totalAmount += $trail_part;
															}
														}
														if ($ex_val['column_id'] == 547) {
															if ($ex_val['value'] == 'Yes') {
																$breakfast  = 150;
																$totalAmount += $breakfast;
															}
														}
														if ($ex_val['column_id'] == 624) {
															if ($ex_val['value'] == 'YES') {
																$valAmount = 150;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 625) {
															if ($ex_val['value'] == 'YES') {
																$valAmount = 300;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 914) {
															if ($ex_val['value'] == 'YES') {
																$valAmount = 349;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 915) {
															if ($ex_val['value'] == 'YES') {
																$valAmount = 199;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 918) {
															if ($ex_val['value'] == 'YES') {
																$valAmount = 299;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 919) {
															if ($ex_val['value'] == 'YES') {
																$valAmount = 399;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}
														if ($ex_val['column_id'] == 626 || $ex_val['column_id'] == 619 || $ex_val['column_id'] == 622) {

															if ($ex_val['value'] == 'YES') {
																$valAmount = 200;
															} else {
																$valAmount = 0;
															}
															$data['race_amount'] = $data['race_amount'] + $valAmount;
														}

														if ($data['country'] == "India") {
															if ($ex_val['column_id'] == 628) {
																if ($ex_val['value'] == 'YES') {
																	$valAmount = 400;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 653) {
																if ($ex_val['value'] == 'YES') {
																	$valAmount = 200;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}

															if ($ex_val['column_id'] == 712) {
																if ($ex_val['value'] == 'Yes') {
																	$valAmount = 150;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 713) {
																if ($ex_val['value'] == 'Yes') {
																	$valAmount = 350;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 819) {
																if ($ex_val['value'] == 1) {
																	$valAmount = 20;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 820) {
																if ($ex_val['value'] == 1) {
																	$valAmount = 20;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 821) {
																if ($ex_val['value'] == 1) {
																	$valAmount = 30;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 788) {
																
																if ($ex_val['value'] == 'Yes') {
																	$valAmount = 500;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
															if ($ex_val['column_id'] == 827) {
																if ($ex_val['value'] == 'Yes') {
																	$valAmount = 150;
																} else {
																	$valAmount = 0;
															}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}

															if ($ex_val['column_id'] == 828) {
																if ($ex_val['value'] == 'Yes') {
																	$valAmount = 150;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
														}

														//Donation amount for grace virtual marathon
														//checking donation is yes?
														if ($ex_val['column_id'] == 633 && $ex_val['value'] == "YES") {
															$hasDonation = 1;
														}

														if ($ex_val['column_id'] == 634) {
															if ($ex_val['value'] > 0) {
																$donationAmt = $ex_val['value'];
															} else {
																$donationAmt = 0;
															}
															if ($hasDonation == 0) {
																$donationAmt = 0;
															}

															$data['race_amount'] = $data['race_amount'] + $donationAmt;
														}
														
													}
												}
												
												$earlys = $db->getRow("select * from ifinish_early_registration_fee where NOW() BETWEEN valid_from AND valid_to and race_id=" . $data['race_id'], MYSQLI_ASSOC);
												
												if ($data['amount_international'] == 0) {
													$type = 'INR';
													if (!empty($earlys)) {
														$data['race_amount'] =  $earlys['amount'];
													} else {
														$data['race_amount'] = $data['race_amount'];
													}
												} else {
													$type = (strtolower($userState['country']) == 'india') ? 'INR' : 'USD';
													if (!empty($earlys)) {
														$data['race_amount'] = $earlys['amount'];
													} else {
														$data['race_amount'] = (strtolower($userState['country']) == 'india') ? $data['race_amount'] : $data['amount_international'];
													}
												}
												$raceAmounts = $db->getRow("select * from  ifinish_event_race where id=" . $data['race_id'], MYSQLI_ASSOC);
												if ($raceAmounts['iscoupon_reg_start'] == 1) {
													$coupons_counts += $raceAmounts['iscoupon_reg_start'];
												}
												
												
														
												########################### Race amount calculation ##################################
												// if($event_id == '9690' && $is_bulk == 1 && $totalBulk >=10){
												if(($event_id == '9690' || $event_id == '9722' || $event_id == '9825') && isset($discountAmtBulk) && $discountAmtBulk >0){
													if($raceAmounts['tax_type'] == 1){
														
														$actual_gst 		= $discountAmtBulk * $raceAmounts['gst'] / 100;
														// echo $actual_gst;
														$raceAmountgst 		+= $actual_gst;
														// echo $raceAmountgst ;
														$raceAmountWGST 	= $discountAmtBulk+$actual_gst;
														$data['race_amount']= $data['race_amount'];
													}
													else{
														
														$data['race_amount'] = $data['race_amount'];
														$raceAmountWGST 	 =  $discountAmtBulk;	
													}
													//echo $raceAmountgst;die;
												}
												else{
													if($raceAmounts['tax_type'] == 1){
														$actual_gst 		= $data['race_amount'] * $raceAmounts['gst'] / 100;
														$raceAmountgst 		+= $actual_gst;
														$raceAmountWGST 	= $data['race_amount']+$actual_gst;
														$data['race_amount']= $data['race_amount'];
													}
													else{
														$data['race_amount'] = $data['race_amount'];
														$raceAmountWGST 	 =  $data['race_amount'];	
													}
												}


												########################### Race amount calculation ##################################

												if (sizeof($user_extraFiels) > 0) {
													foreach ($user_extraFiels as $extra => $ex_val) {
														if ($data['country'] != "India") {
															if ($ex_val['column_id'] == 653) {
																if ($ex_val['value'] == 'YES') {
																	$valAmount = 30;
																} else {
																	$valAmount = 0;
																}
																$data['race_amount'] = $data['race_amount'] + $valAmount;
															}
														}
													}
												}
												//echo $data['race_amount'];
												$baseAmount  += $data['race_amount'];
												//echo $baseAmount;
												if($event_id == 9690){
													if($is_bulk == 1){
														$totalBulk 	=	count($_POST['runner_race']);
														if($totalBulk >=10 && $totalBulk < 15){
															if ($data['gender'] == 'female') {
																$discount = 'YES';
																$bulkDisPer	=	'10';
																$discountByUsers 	+=	($data['race_amount'] * 15) / 100;
															}else{
																$discount = 'YES';
																$bulkDisPer	=	'10';
																$discountByUsers 	+=	($data['race_amount'] * 10) / 100;
															}
														}
														elseif($totalBulk >=15 && $totalBulk < 20){
																$discount = 'YES';
																$bulkDisPer	=	'15';
																$discountByUsers 	+=	($data['race_amount']*15)/100;
														}
														elseif($totalBulk >=20){
																$discount = 'YES';
																$bulkDisPer	=	'20';
																$discountByUsers 	+=	($data['race_amount']*20)/100;
																//echo $bulkDisPer.'------';$discountByUsers.'<br>';
														}else{
															if ($data['gender'] == 'female') {
																$discount = 'YES';
																$discountByUsers 	+=	($data['race_amount'] * 15) / 100;
															}
														}
													}else{
														if ($data['gender'] == 'female') {
															$discount = 'YES';
															$discountByUsers 	+=	($data['race_amount'] * 15) / 100;
														} 
													}
												}

												if($event_id == 9825){
													if($is_bulk == 1){
														$totalBulk 	=	count($_POST['runner_race']);
														if($totalBulk >=10){
																$discount = 'YES';
																$bulkDisPer	=	'10';
																$discountByUsers 	+=	($data['race_amount']*10)/100;
														}
														elseif ($data['gender'] == 'female') {
															$discount = 'YES';
															$discountByUsers 	+=	($data['race_amount'] * 10) / 100;
															$singleDiscPu 		=	($data['race_amount'] * 10) / 100;
														}
														else{
															$singleDiscPu 		= 0;
														}
													}else{
														if ($data['gender'] == 'female') {
															$discount = 'YES';
															$discountByUsers 	+=	($data['race_amount'] * 10) / 100;
															$singleDiscPu 		=	($data['race_amount'] * 10) / 100;
														} 
														else{
															$singleDiscPu 		= 0;
														}
													}
												}
												if ($event_id == 9722) {
													if ($is_bulk == 1) {
														$totalBulk 	=	count($_POST['runner_race']);
														if ($totalBulk >= 10 && $totalBulk < 50) {
																$discount = 'YES';
																$bulkDisPer	=	'10';
																$discountByUsers 	+=	($data['race_amount'] * 10) / 100;

														} elseif ($totalBulk >= 50 && $totalBulk < 100) {
															$discount = 'YES';
															$bulkDisPer	=	'15';
															$discountByUsers 	+=	($data['race_amount'] * 15) / 100;
														} elseif ($totalBulk > 100) {
															$discount = 'YES';
															$bulkDisPer	=	'20';
															$discountByUsers 	+=	($data['race_amount'] * 20) / 100;
														}
													}
												}
												
												
												$totalAmount += $raceAmountWGST;
											    if($event_id == 9826){
													$photographExtra = $db->getRow("SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id = " . $data['reg_id'] . "  and column_id in (953) order by id asc");
													if($photographExtra['value'] == 1){
														$totalAmount += 699;
													}
												}
												if($event_id == 9813){
													$photographExtra = $db->getRow("SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id = " . $data['reg_id'] . "  and column_id in (945) order by id asc");
													if($photographExtra['value'] == 'Yes'){
														$totalAmount += 500;
													}
												}
												?>
												
												<div class="col-md-6 col-sm-6 col-xs-12 data wordbreak   font-weight500 lineheight30">
													<div class="bulk_reg_blocks">
														<div class="uppercase bold"><?= ucwords(strtolower($data['first_name'] . " " . $data['middle_name'] . " " . $data['last_name'])); ?></div>
														<div class="paid"><?= $data['email']; ?></div>
														<h3 style="color:#e0a806;font-size:18px;margin:0px;"> <?= $data['race_name'].' '. $data['gender']; ?></h3>
														<?php
															if (isset($ahm_bulk_user_id)) {
																if (($ahm_user_discount == $ahm_bulk_user_id) && $get_registered_2020 > 0) {
																	$ahm_racename = $db->getValue("SELECT `name` FROM `ifinish_event_race` where event_id = .$event_id. and id='" . $get_registered['race_id'] . "'");


																	echo '<sub style="color:#f97d6a !important">NOTE:  We already have the Registration for AHM 2020 (' . $ahm_racename . ')</sub>';
																}
															}

															?>
													</div>
												</div>
															
												<div class="col-md-6 col-sm-6 col-xs-12 wordbreak" <?php if(($is_bulk == 1 && $event_id == '9690' && $totalBulk >=10) || ($is_bulk == 1 && $event_id == '9722' && $totalBulk >10) || ($is_bulk == 1 && $event_id == '9825' && $totalBulk >=10) || ($event_id == '9690' && isset($discountAmtBulk) && $discountAmtBulk > 0 && $data['gender'] == 'female')){?>style="display:none"><?php }?>
												
												
													<div class="bulk_reg_blocks">
														<div>
															<span class="paid">Base Fee : </span>
															<span class="amountstyle"><?= getformatCurrency($data['race_amount'], $type); ?></span>
														</div>
														
														
														<div class="coupon" id="couponApplyDiv_<?= $data['id']; ?>">
															<input type="hidden" id="iscoupon_<?= $data['id']; ?>" class="btn btn-primary btn-xs full_coupon" value="<?= $raceAmounts['iscoupon_reg_start']; ?>">
															<input type="hidden" id="amount_couponApplyDiv_<?= $data['id']; ?>" value="<?= $data['race_amount']; ?>" />
															<input type="hidden" id="race_couponApplyDiv_<?= $data['id']; ?>" value="<?= $data['race_id']; ?>" />
															<input type="hidden" id="tax_type_<?= $data['id']; ?>" value="<?=$raceAmounts['tax_type']; ?>" />
															<input type="hidden" id="discountByUsers_<?= $data['id']; ?>" value="<?=$singleDiscPu; ?>" />
															<input type="hidden" id="tax_type_gst_<?= $data['id']; ?>" value="<?=$raceAmounts['gst']; ?>" />
															<input type="hidden" id="currency_type>" value="<?=$type; ?>" />
															<?php if($event_id == '9690'&& isset($discountAmtBulk) && $discountAmtBulk > 0 && $data['gender']=='female'){  ?>
																<div style="margin-top:28px; display:none;" id="couponInnerDiv_<?= $data['id']; ?>" >
																	<input type="text" id="coupon_couponApplyDiv_<?= $data['id']; ?>" class="form-controlv cout" name="bulk_coupon_couponApplyDiv_<?= $data['id']; ?>" <?php if($event_id == 9690){?>data-toggle="tooltip" title="Discounts are applicable for only group registrations.
																			Above 10 participants 10% discount on registration
																			Above 15 participants 15% discount on registration 
																			Above 20 participants  20% discount."<?php }?> placeholder="Enter Coupon">
																	<input type="button" class="btn btn-green btn-xs apply_coupon" value="Apply">
																</div>
															<?php } else {?>
															<div style="margin-top:28px;" id="couponInnerDiv_<?= $data['id']; ?>" >
																<input type="text" id="coupon_couponApplyDiv_<?= $data['id']; ?>" class="form-controlv cout" name="bulk_coupon_couponApplyDiv_<?= $data['id']; ?>" <?php if($event_id == 9690){?>data-toggle="tooltip" title="Discounts are applicable for only group registrations.
																		Above 10 participants 10% discount on registration
																		Above 15 participants 15% discount on registration 
																		Above 20 participants  20% discount."<?php }?> placeholder="Enter Coupon">
																<input type="button" class="btn btn-green btn-xs apply_coupon" value="Apply">
															</div>
															<?php } ?>
														</div>
														<div style="margin-top:16px;">
															<span class="paid">To be paid <small><?= (isset($raceAmounts['tax_type']) && $raceAmounts['tax_type'] == 1) ? "" : ""; ?> </small> :</span> <span id="pamount_<?= $data['id']; ?>" class="amountstyle"><?= getformatCurrency($data['race_amount'], $type); ?></span>
														</div>
													</div>
												</div>
											<?php
											$ic++;
											
											}
											// exit;

											if (isset($userState['state']) && $userState['state'] == 'Telangana') {
												$sgst = $sgst;
												$cgst = $cgst;
												$gst = 0;
												$totalAmount = $totalAmount + $sgst + $cgst;
											} else {
												$sgst = 0;
												$cgst = 0;
												$gst = $gst;
												$totalAmount = $totalAmount + $gst;
											}

											$total_race_amount += $data['race_amount'];
											$totalAmount = $totalAmount + $daycare_amount + $TASALLI + $Ananda;
											if (isset($dscount_amt) && $dscount_amt > 0) {
												$totalAmount = $totalAmount - $dscount_amt;
											}
											

											if (isset($userState['country']) && ($userState['country'] != '' && $userState['country'] !=  'India')) {
												if ($e['processing_fee'] > 0) {
													if ($totalAmount == 0) {
														$processing_fee  = '10%';
														$serviceCharge = 0;
													} else {
														$processing_fee  = '10%';
														$serviceCharge = (($totalAmount * 10) / 100);
													}
												} else {
													$processing_fee = $e['processing_fee'] . '%';
													$serviceCharge = $totalAmount * $e['processing_fee'] / 100;
												}
											} else {
												$processing_fee = $e['processing_fee'] . '%';
												$serviceCharge = $totalAmount * $e['processing_fee'] / 100;
											}

											if (isset($e['swachh_bharat_cess']) && $e['swachh_bharat_cess'] > 0) {
												$swachh_bharat = $serviceCharge * $e['swachh_bharat_cess'] / 100;
											} else {
												$swachh_bharat  = 0;
											}

											$serviceTax = ($serviceCharge * $e['tax'] / 100);
											$totalAmount += $serviceCharge + $serviceTax + $swachh_bharat;
											
											

											?>
											<input type="hidden" id="discountByusers" value="<?=$bulkDisPer;?>">
											<span id="hideamount" style="display:none"><?= $totalAmount; ?></span>
											<?php


											if ($event_id == 590 and $disAmount > 0) { ?>
												<tr style="border:none" align="right">
													<td colspan="6" style="border:none;padding-right: 40px; color:green; font-weight:bold">Discount Amount For (<?= $patCount; ?>) Participants <span style="font-weight: bold; padding: 0 8px;">:</span></td>
													<td align="left" style="border:none;" id="service_tax">
														<?php
															echo getformatCurrency($disAmount, $type);
															?>
													</td>
												</tr>
											<?php }
											if (isset($e['swachh_bharat_cess']) && $e['swachh_bharat_cess'] > 0) { ?>
												<tr style="border:none" align="right">
													<td colspan="5" style="border:none;padding-right: 40px;">Swachh Bharat Cess(<?= $e['swachh_bharat_cess']; ?>%) <span style="font-weight: bold; padding: 0 8px;">:</span></td>
													<td align="left" style="border:none">
														<?php
															echo formatCurrency($swachh_bharat, $type);
															?>
													</td>
												</tr>
											<?php } ?>


											<?php
											if ($QS_Values['event'] == 438) {
												?>
												<tr style="font-weight:bold;border:none" align="right">
													<td colspan="12" style="border:none">
														<div id="dcDIV">

															<div class="priceLabel">
																<span id="dc_bulk" style="margin: 2px 0 2px 20px;display:inline-block;">
																	<input type="text" id="coupon_in" name="coupon_in" placeholder="Discount Coupon" value=""> <input type="button" value="Apply" id="DC" class="btn btn-success">
																</span>
															</div>
														</div>
													</td>
												</tr>
											<?php
											}
											?>
										</div>
									</div>



									<?php

									if ($event_id == 910) {
										echo '<h4 class="reg_header" style="border-top:1px solid #eee;margin-bottom:12px;">Charity</h4>
											<div class="row" style="margin:0 0 12px 0 !important;">
												<div class="col-md-12 col-sm-12 col-xs-12  bulk_reg_blocks_mobile_2">';
										$donation_min_amt = 1000;
										?>

										<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">
											<div class="bulk_reg_blocks_2">
												<div class="col-md-12 amount-select divider">
													<div class="option-one">
														<div class="donation-box active">
															<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
															<label><span><?= getformatCurrency($donation_min_amt, $type); ?></span></label>
															<input type="hidden" class="charityamount" id="charity_hiddenamt" value="<?= $donation_min_amt; ?>">
														</div>
														<div class="txt">Minimum Donation Amount.</div>
													</div>
													<div class="option-last">
														<div class="donation-box enter-amount">
															<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
															<label><i class="fa fa-rupee"></i><input type="number" pattern="[0-9]*" min="<?= $data['charity_amount']; ?>" class="charityamount" id="charityamount" placeholder="Enter Amount" name="charity_amunt"></label>
														</div>
														<div class="txt">Donate any amount of your Choice</div>
													</div>
												</div>
											</div>
										</div>

										</div>

									</div>
						<?php
						}
						if ($event_id == 9602) {
						echo '<h4 class="reg_header" style="border-top:1px solid #eee;margin-bottom:12px;">Charity</h4>
						<div class="row" style="margin:0 0 12px 0 !important;">
							<div class="col-md-12 col-sm-12 col-xs-12  bulk_reg_blocks_mobile_2">';
								$donation_min_amt = 0;
								?>

								<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">
									<div class="bulk_reg_blocks_2">
										<div class="col-md-12 amount-select divider">
											<!-- <div class="option-one"> -->
												<!-- <div class="donation-box "> -->
													<!-- <div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i> -->
													<!-- </div> -->
													<!-- <label><span><?= getformatCurrency($donation_min_amt, $type); ?></span></label> -->
													<input type="hidden" class="charityamount" id="charity_hiddenamt"
														value="<?= $donation_min_amt; ?>">
												<!-- </div>
												<div class="txt">Minimum Donation Amount.</div> -->
											<!-- </div> -->
											<div class="option-last">
												<div class="donation-box enter-amount active">
													<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i>
													</div>
													<label><?php 
													if($type=='USD'){
													echo	'&#36;&nbsp;';
													}else{
														echo '&#8377;&nbsp;';
													}
													 ?><input type="number" pattern="[0-9]*"
															min="<?= $data['charity_amount']; ?>" class="charityamount" id="charityamount"
															placeholder="Enter Amount" name="charity_amunt"></label>
												</div>
												<div class="txt">Donate any amount of your Choice</div>
											</div>
										</div>
									</div>
								</div>

							</div>

						</div>
						<?php
						}


						if (isset($ngo_id, $data['purchase_bib_amt']) && $ngo_id > 0) {
							echo '<h4 class="reg_header" style="border-top:1px solid #eee;margin-bottom:12px;">Charity</h4>
                          		 <div class="row" style="margin:0 0 12px 0 !important;">
						      <div class="col-md-12 col-sm-12 col-xs-12  bulk_reg_blocks_mobile_2">';
							$ngoData = $db->getRow("SELECT ngodescription,id,logo FROM `ifinish_ngo_list` where id='" . $ngo_id . "'", MYSQLI_ASSOC);
							//$totalAmount = $totalAmount+$data['purchase_bib_amt'];
							?>
							<div class="col-md-6 col-sm-12 col-xs-12 padding0">
								<div class="col-md-12 col-sm-12 col-xs-12 bulk_reg_blocks_2">
									<div class="col-md-3 padding2015"><img class="img-responsive" src="<?= WEB_ROOT . "../" . $ngoData['logo']; ?>" /></div>
									<div class="col-md-9 padding2015 borderlefteee">
										<div style="min-height: 100px;" class="font-size14 font-weight500"><?= substr(strip_tags($ngoData['ngodescription']), 0, 200) . '....'; ?></div><a target="_blank" class="btn" style="float:right;color:#ec482f;font-weight:600;text-transform: uppercase" href="<?= WEB_ROOT . 'charityinfo/' . $ngo_id . '&e=' . $event_id; ?>">Read More</a>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12">
								<div class="bulk_reg_blocks_2">
									<div class="col-md-12 amount-select divider">
										<div class="option-one">
											<div class="donation-box active">
												<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
												<label><span><?= getformatCurrency($data['purchase_bib_amt'], $type); ?></span></label>
												<input type="hidden" class="charityamount" id="charity_hiddenamt" value="<?= $data['purchase_bib_amt']; ?>">
											</div>
											<div class="txt">Minimum Donation Amount.</div>
										</div>
										<div class="option-last">
											<div class="donation-box enter-amount">
												<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
												<label><i class="fa fa-rupee"></i><input type="number" pattern="[0-9]*" min="<?= $data['purchase_bib_amt']; ?>" class="charityamount" id="charityamount" placeholder="Enter Amount" name="charity_amunt"></label>
											</div>
											<div class="txt">Donate any amount of your Choice</div>
										</div>
									</div>
								</div>
							</div>

						</div>

					</div>
					<?php
						}
					else if (isset($purchase_charity_bib, $data['purchase_bib_amt']) && $purchase_charity_bib == 1 && $event_id == '9678') {
						$charities = $db->getRows("SELECT N.id, N.ngoname,N.ngocategory,  N.supporters,  N.logo,  N.photos FROM `ifinish_ngo_list` N,ifinish_approved_ngos  A where A.ngo_id=N.id and A.event_id=" . $QS_Values['event'], MYSQLI_ASSOC);
						// printr($charities);
						// exit;
						
						echo '<h4 class="reg_header" style="border-top:1px solid #eee;margin-bottom:12px;">Charity</h4>
							   <div class="row" style="margin:0 0 12px 0 !important;">
						  <div class="col-md-12 col-sm-12 col-xs-12  bulk_reg_blocks_mobile_2">';
						$ngoData = $db->getRow("SELECT ngodescription,id,logo FROM `ifinish_ngo_list` where id='" . $ngo_id . "'", MYSQLI_ASSOC);
						//$totalAmount = $totalAmount+$data['purchase_bib_amt'];
						?>
						<div class="col-md-12">
						<div class="txt" style="text-align: left;font-size: 16px;color: red;padding: 10px 10px 10px 8px;font-weight: bold;">Minimum Donation Amount should be : <span><?= $data['purchase_bib_amt']; ?></span></div>
							<select title="Select a charity" class="charitypicker" name="charitypicker" id="charitypicker">
								<option value="" id="emptyselect">-- Select Charity --</option>
								<?php
										// $cats = $db->getRows("SELECT * FROM ngo_category");
										// foreach ($cats as $cat) {

											$charities = $db->getRows("SELECT N.id, N.ngoname,N.ngocategory,  N.supporters,  N.logo,  N.photos FROM `ifinish_ngo_list` N,ifinish_approved_ngos  A where A.ngo_id=N.id  and A.event_id=" . $QS_Values['event']." GROUP BY N.ngoname ", MYSQLI_ASSOC);
											if (!empty($charities)) {
												//echo '<optgroup label="' . $cat['category'] . '">';
												foreach ($charities as $cahrityData) {
													echo ' <option value="' . $cahrityData['id'] . '">' . $cahrityData['ngoname'] . '</option>';
												}
												echo '</optgroup>';
											}
										// }

										?>
							</select>
						</div>
						
						<div class="col-md-6 col-sm-12 col-xs-12" style="display:none;">
							<div class="bulk_reg_blocks_2">
								<div class="col-md-12 amount-select divider">
									<div class="option-one">
										<div class="donation-box active">
											<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
											<label><span><?= getformatCurrency($data['purchase_bib_amt'], $type); ?></span></label>
											<input type="hidden" class="charityamount" id="charity_hiddenamt" value="<?= $data['purchase_bib_amt']; ?>">
										</div>
										<div class="txt">Minimum Donation Amount.</div>
									</div>
									<div class="option-last">
										<div class="donation-box enter-amount">
											<div class="input-circle"><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
											<label><i class="fa fa-rupee"></i><input type="number" pattern="[0-9]*" min="<?= $data['purchase_bib_amt']; ?>" class="charityamount" id="charityamount" placeholder="Enter Amount" name="charity_amunt"></label>
										</div>
										<div class="txt">Donate any amount of your Choice</div>
									</div>
								</div>
							</div>
						</div>

					</div>

				</div>
				<?php
				}
					 else  if (isset($ngo_id, $data['charity_amount']) && $ngo_id == 0 && $bulk_regid == 0) {

						$charities = $db->getRows("SELECT N.id, N.ngoname,N.ngocategory,  N.supporters,  N.logo,  N.photos FROM `ifinish_ngo_list` N,ifinish_approved_ngos  A where A.ngo_id=N.id and A.event_id=" . $QS_Values['event'], MYSQLI_ASSOC);
						// printr($charities);
						// exit;
						if (isset($charities) && !empty($charities)) {
							?>
						<h4 class="reg_header" style="border-top:1px solid #eee;margin-bottom:12px;">Charity</h4>
						<div class="row" style="margin:0 0 12px 0 !important;">
							<div class="col-md-12 col-sm-12 col-xs-12  bulk_reg_blocks_mobile_2">
								<div class="col-md-12">
									<select title="Select a charity" class="charitypicker" name="charitypicker" id="charitypicker">
										<option value="" id="emptyselect">-- Select Charity --</option>
										<?php
												// $cats = $db->getRows("SELECT * FROM ngo_category");
												// foreach ($cats as $cat) {

													$charities = $db->getRows("SELECT N.id, N.ngoname,N.ngocategory,  N.supporters,  N.logo,  N.photos FROM `ifinish_ngo_list` N,ifinish_approved_ngos  A where A.ngo_id=N.id  and A.event_id=" . $QS_Values['event']." GROUP BY N.ngoname ", MYSQLI_ASSOC);
													if (!empty($charities)) {
														//echo '<optgroup label="' . $cat['category'] . '">';
														foreach ($charities as $cahrityData) {
															echo ' <option value="' . $cahrityData['id'] . '">' . $cahrityData['ngoname'] . '</option>';
														}
														echo '</optgroup>';
													}
												// }

												?>
									</select>
								</div>
							</div>

						</div>
				<?php }
				}  ?>
				<div id="wrapper"></div>
				<div class="clearfix"></div>

				<h4 class="reg_header" style="border-top:1px solid #eee;">Payment Summary</h4>
				<div class="row" style="margin:0 !important;border-bottom:1px solid #eee;">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">

						<div class="flex" id="noflex">
							<div class="col-md-6 col-sm-12 col-xs-12 padding2015 padding0 borderrighteee">
								<div class="col-md-12  col-sm-12 col-xs-12">
									<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Base Fee :</div>
																							
									<?php if(($event_id == '9690' || $event_id == '9825') && $is_bulk ==1){?>
									<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="subtotal"><?= getformatCurrency($baseAmount, $type); ?></div>
									<?php }else{?>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="subtotal"><?= getformatCurrency($baseAmount, $type); ?></div>
									<?php }?>
								</div>
								<?php if ($discount == 'YES' && ($event_id=='9690' || $event_id == '9825')) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12 femaledis">
									<?php if(($event_id == '9690' || $event_id == '9825') && $is_bulk ==1 && $totalBulk>=10){?>
											<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">GRM Discount(<?=$bulkDisPer;?>%)</div>
										<?php }else{?>
											<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">GRM Female Discount(10%)</div>
										<?php }?>
										<!-- <div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">GRM Discount(<?=$bulkDisPer;?>%)</div> -->
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="grmdiscount">
											<?=getformatCurrency($discountByUsers, $type); ?></div>
									</div>
								<?php }?>
								<?php if ($discount == 'YES' && $event_id == '9722') { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">RUN BHOPAL RUN Discount(<?=$bulkDisPer;?>%)</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="grmdiscount">
											<?=getformatCurrency($discountByUsers, $type); ?></div>
									</div>
								<?php }?>
								<?php
								
									if($event_id == 9826){
										if ($is_bulk == 1) {
											$getBulkRegId  = $db->getRows('SELECT id FROM ifinish_registration WHERE bulk_id="'.$bulk_regid.'" ');
											foreach($getBulkRegId as $bulkIds){
												$currentBulk[] = $bulkIds['id'];
											}
											//echo "<pre>";print_r($currentBulk);die;
											$implodeId = implode(',',$currentBulk);
											//echo $implodeId;die;
											//echo "SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id  IN(" . $implodeId . ")  and column_id in (879) order by id asc";
											$photographExtraBulk = $db->getRows("SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id  IN(" . $implodeId . ")  and column_id in (953) GROUP BY registration_id order by id asc");
											$additionalCount = count($photographExtraBulk);
											$additionalAmount = $additionalCount*699;
											if($additionalAmount > 0){	
											?>
											<input type="hidden" name="prepic" id=prepic value="<?php echo $additionalAmount;?>">
											<div class="col-md-12  col-sm-12 col-xs-12">
												<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Photos-Pre Order Charge :</div>
																														
												<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="subtotalp"><?= getformatCurrency($additionalAmount, $type); ?></div>
											</div>
											<?php
											}	
											
										}
										else{
											if($photographExtra['value'] == 1){?>
												<div class="col-md-12  col-sm-12 col-xs-12">
												<input type="hidden" name="prepic" id=prepic value="<?php echo $additionalAmount;?>">
													<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Photos-Pre Order Charge :</div>
																															
													<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="subtotalp"><?= getformatCurrency(699, $type); ?></div>
												</div>
									<?php }}}?>
									<?php
								
								if($event_id == 9813){
									if ($is_bulk == 1) {
										$getBulkRegId  = $db->getRows('SELECT id FROM ifinish_registration WHERE bulk_id="'.$bulk_regid.'" ');
										foreach($getBulkRegId as $bulkIds){
											$currentBulk[] = $bulkIds['id'];
										}
										//echo "<pre>";print_r($currentBulk);die;
										$implodeId = implode(',',$currentBulk);
										//echo $implodeId;die;
										//echo "SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id  IN(" . $implodeId . ")  and column_id in (879) order by id asc";
										$photographExtraBulk = $db->getRows("SELECT * FROM `ifinish_registration_extra_values` WHERE registration_id  IN(" . $implodeId . ")  and column_id in (945) GROUP BY registration_id order by id asc");
										$additionalCount = count($photographExtraBulk);
										$additionalAmount = $additionalCount*500;
										if($additionalAmount > 0){	
										?>
										<div class="col-md-12  col-sm-12 col-xs-12">
											<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">T-Shirt Charge :</div>
																													
											<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="subtotal"><?= getformatCurrency($additionalAmount, $type); ?></div>
										</div>
										<?php
										}	
										
									}
									else{
										if($photographExtra['value'] == 'Yes'){?>
											<div class="col-md-12  col-sm-12 col-xs-12">
												<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">T-Shirt Charge :</div>
																														
												<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="subtotal"><?= getformatCurrency(500, $type); ?></div>
											</div>
								<?php }}}?>
								
								<?php
								
								if ($raceAmounts['tax_type'] == 1 and $raceAmountgst > 0) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">GST on Race Amount</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="gstraceamt">
											<?= getformatCurrency($raceAmountgst, $type); ?></div>
									</div>
								<?php }
								if (isset($processing_fee) && $processing_fee > 0) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Processing Fee (<?= $processing_fee; ?>):</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="service_charge"><?php echo getformatCurrency($serviceCharge, $type); ?>
										</div>
									</div>
								<?php }

								if (isset($e['tax']) && $e['tax'] > 0) { ?>
								<div class="col-md-12  col-sm-12 col-xs-12">
									<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">GST On Processing Fee(<?= $e['tax']; ?>%)
									</div>
									<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="gstProcessFee">
										<?= getformatCurrency($serviceTax, $type); ?></div>
								</div>

								<?php }
								
								if (isset($e['tax']) && $e['tax'] > 0) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Total GST</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16" id="service_tax"><?= getformatCurrency($serviceTax+$raceAmountgst, $type); ?></div>
									</div>

								<?php }
								
								
								//if (isset($ngo_id) && $ngo_id > 0) { 
								if ($rent_cycle > 0) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Donation:</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16"><i class="fa fa-rupee"></i> <span id="donated_amt"><?= $rent_cycle ?></span></div>
										<input type="hidden" id="charity_amt" name="charity_amt" value="" />
									</div>
								<?php } else {
									if($type=='USD'){
									$cur = '&#36;&nbsp;';
									}else{
									$cur = '&#8377;&nbsp;';
									}
									echo '<div class="col-md-12  col-sm-12 col-xs-12 donation">
											  <div  class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Donation:</div>
											  <div  class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16">
											  '.$cur.'
											   <span  id="donated_amt"></span></div>
										   </div>';
								}
								?>
								<?php if ($cycleRent > 0) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Rent Cycle:</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16"><i class="fa fa-rupee"></i> <span id="donated_amt"><?= $cycleRent ?></span></div>
										<input type="hidden" id="charity_amt" name="charity_amt" value="" />
									</div>
								<?php } 
								if ($trail_part > 0) { ?>
									<div class="col-md-12  col-sm-12 col-xs-12">
										<div class="col-md-9 col-sm-6 col-xs-6 lineheight30 font-size14 font-weight500">Trail Race:</div>
										<div class="col-md-3 col-sm-6 col-xs-6 font-weight800 lineheight30 font-size16"><i class="fa fa-rupee"></i> <span id="donated_amt"><?= $trail_part ?></span></div>
										<input type="hidden" id="charity_amt" name="charity_amt" value="" />
									</div>
								<?php }?>
							</div>
							<div class="col-md-5 col-md-offset-1 col-sm-12 col-xs-12 padding2015 padding0">
								<div class="col-sm-12 col-xs-12 centerindesktop">
									<div class="font-size16 font-weight800 col-md-12 col-sm-6 col-xs-6">Grand Total</div>
									<div class="font-size32 font-weight800 ec482fcolor col-md-12 col-sm-12 col-xs-12" id="grand_total">

										<?php
										$db->query('UPDATE `ifinish_registration_bulk` SET amount = ' . $totalAmount . ' WHERE id = ' . $bulk_regid);
										if ($e['tax_type'] == 1) {
											echo getformatCurrency($totalAmount, $type);
										} else {
											echo getformatCurrency($totalAmount, $type);
										}
										
										?>


									</div>
									
									<input type="hidden" id="grandTotal" name="grandTotal" value="<?= $totalAmount; ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php $formID = md5(time()); // Unique Form ID 
				?>
				<?php
				if (isset($bulk_regid) && $bulk_regid > 0) {
					$backURL = WEB_ROOT . 'race_register/' . encryptParams(array('step' => '0', 'bulk' => $bulk_regid, 'event' => $event_id, 'choose_type' => $choose_type, "popup" => 1));
				} else {
					$backURL = WEB_ROOT . 'race_register/' . encryptParams(array('step' => '0', 'reg_id' => $regId, 'event' => $event_id, 'choose_type' => $choose_type, 'ngo_id' => $ngo_id, 'is_student' => $is_student, "popup" => 1));
				}
				//  echo $e['iscoupon_reg_start'].$coupons_counts;
				if ($e['iscoupon_reg_start'] == 1 && $coupons_counts > 0) {
					if ($ngo_id == 0) {
						echo '<div class="pull-left" >
						<div class="paragraph" style="padding: 10px 0px;font-size:14px;color:red"><p>Registrations closed. Corporate users holding full value coupons can continue to register for the event.</p></div>
					</div>';
					}
				}
				?>
				<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

				<form id="form" name="payforms" action="" method="post">

					<input type="hidden" name="event" id="eventID" value="<?= $event_id; ?>">
					<input type="hidden" name="bulkId" id="bulkId" value="<?= $bulk_regid; ?>">
					<input type="hidden" name="coupon" id="coupon" value="">
					<input type="hidden" name="coupon_code" id="coupon_code" value="">
					<input type="hidden" name="reg_ids" id="reg_ids" value="">
					<input type="hidden" name="reg_id" id="reg_id" value="<?= $reg_id; ?>">
					<input type="hidden" name="ngo_id" id="ngo_id" value="<?= $ngo_id; ?>">
					<input type="hidden" name="spoky_id" id="spoky_id" value="<?= $spoky_id; ?>">
					<input type="hidden" name="is_student" id="is_student" value="<?= $is_student; ?>">
					<input type="hidden" name="coupon_id" id="coupon_id" value="">
					<input type="hidden" name="total_gst" id="total_gst" value="<?php echo $raceAmountgst;?>">
					<input type="hidden" name="bulkCharge" id="bulkCharge" value="<?php echo $additionalAmount;?>">
					<input type="hidden" name="discountByUsers" id="discountByUsers" value="<?php echo $discountByUsers;?>">
					<input type="hidden" name="singleCharge" id="singleCharge" value="<?php echo $photographExtra['value'] == 1?"800":"0";?>">
					<input type="hidden" name="currency_type" id="currency_type" value="<?php echo $type?>">
					<input type="hidden" name="amount_international" id="amount_international" value="<?php echo $e['amount_international'];?>">
					<?php if ($e['tax_type'] == 1) {
						$totl_val_amt = number_format($totalAmount, 2);
						?>
						<input type="hidden" name="amount" id="amount" value="<?= number_format($totalAmount, 2); ?>">
					<?php } else {
						$totl_val_amt = number_format($totalAmount, 2); ?>
						<input type="hidden" name="amount" id="amount" value="<?= number_format($totalAmount, 2); ?>">
					<?php }
					?>
					<input type="hidden" name="encamt" value="<?= encryptParams(array('encamt' => $totl_val_amt)); ?>" />
					<?php if (isset($ahm_discount['percentage']) && !empty($ahm_discount['percentage'])) { ?>
						<input type="hidden" name="ahm_discount" id="ahm_discount" value="<?= $ahm_discount['percentage']; ?>">
					<?php }
					?>
					<input type="hidden" name="raceamount" id="raceamount" value="<?= $baseAmount; ?>">
					<input type="hidden" name="choose_type" id="choose_type" value="<?= array_implode_with_keys($choose_type); ?>">
					<?php
					if ($event_id == 9407 || $event_id == 9502) { ?>
						<div class="row" style="margin:0 !important">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<div class="checkbox">
										<label> <input class="" type="checkbox" name="ahmterms" value="1" required>I have verified my AHM history and updated the same. I understand that any discount I might be eligible for, on account of my running history with AHM, will not be available if my history is updated or approved after I complete my registration. <i class="fa fa-star" style="color:red;font-size:5px;" title="Required"></i></label>
									</div>


								</div>
							</div>
						</div>
					<?php } ?>
					<div class="row" style="margin:0 !important">
						<div class="col-md-6">
							<div class="form-group" style="margin:50px 30px 0px">
								<label for="gstno">GST No. (optional)</label>
								<input type="text" style="width: 70%" value="<?= (isset($_SESSION['users']['gst'])) ? $_SESSION['users']['gst'] : ''; ?>" name="gstno" placeholder="Enter GST Number" class="form-control" />
							</div>
						</div>
						<div class="col-md-6 pull-right padding2015 text-right">
							<div class="form-group">
								<a href="<?= $backURL; ?>">
									<button type="button" value="Back" class="btn btn-back">BACK</button>
								</a>
								<?php
								$disabled = "";
								if ($coupons_counts > 0) {
									if ($ngo_id == 0) {

										$disabled = "disabled";
									}
								}
								if ($totalAmount == 0) {
									echo '<button type="submit" value="save" class="btn btn-continue" analytics-on="click" analytics-event="Continue to Pay" analytics-category="Register payments" analytics-label="Continue to Pay button">PROCEED</button>';
								} else {
									if ($spoky_id > 0) {

										echo '<button type="submit" value="save" class="btn btn-continue" analytics-on="click" analytics-event="Continue to Pay" analytics-category="Register payments" analytics-label="Continue to Pay button">PROCEED</button>';
									} else {
										echo '<button ' . $disabled . ' style="width:150px;" type="submit" id="saveform" value="save" class="btn btn-continue" analytics-on="click" analytics-event="CONTINUE TO PAY" analytics-category="Register payments" analytics-label="Continue to Pay button">CONTINUE TO PAY</button>';
									}
								} ?>
							</div>
						</div>
					</div>
				</form>
				</div>
				<div id="regForm"></div>
				<center>
					<div id="spinnerLoad" style="position:absolute;left:0;right:0;top:50%;display:none"><img src="<?= WEB_ROOT; ?>images/processing-please-wait.gif" />
						<br /> <b style="font-size:18px;">Please Scroll down to continue the payment</b></div>
				</center>

			</div>

			<?php
			$raceValues = rtrim($raceAmountsvalues, ',');
			?>
			<form method="post" id="coupon_form" class="ng-pristine ng-valid">
				<div class="form-group">
					<input type="hidden" name="bulk_event" id="bulk_event" value="<?= $event_id; ?>">
					<input type="hidden" name="bulk_race" id="bulk_race">
					<input type="hidden" name="bulk_coupon" class="form-input bulk_coupon" id="bulk_coupon" placeholder="Your Coupon Code">
					<input id="actamount" type="hidden" name="actamount" class="form-input">
					<input id="raceAmountsvalues" type="hidden" name="raceAmountsvalues" value="<?= $raceValues; ?>" class="form-input">
					<input type="hidden" name="userID" id="userID" value="<?= isset($_SESSION['users']['id']) ? $_SESSION['users']['id'] : ''; ?>">
					<input type="hidden" name="currency_type" id="currency_type" value="<?php echo $type?>">
					<input type="hidden" name="amount_international" id="amount_international" value="<?php echo $e['amount_international'];?>">
					<i class="fa fa-spinner fa-spin" id="spinner_login"></i>
				</div>
			</form>

			<script type="text/javascript">
				$type = '<?php echo $type?>';
				if($type == 'INR'){
					$currency = '<i class="fa fa-inr"></i>';
				}
				else{
					$currency = '<i class="fa fa-usd"></i>';
				}
				$('.popoverInfo').popover({
					trigger: 'click'
				});

				if ($('.donation-box.active .charityamount').val() > 0) {
					charityCal($('#grandTotal').val(), $('.donation-box.active .charityamount').val());
				}
				$(document).on('click', '.skip_charity', function() {
					$('.charityhide').hide();
					$("#emptyselect").attr('selected', 'selected');
					$("#ngo_id").val('0');
					$('.donation-box.active .charityamount').val('0');
					charityCal($('#grandTotal').val(), 0);
				});

				$(document).on('click', '.donation-box', function() {

					$('.donation-box').removeClass('active');
					$(this).addClass('active');
					$(".active .charityamount").focus();
					var charityamount = $(".active .charityamount").val();
					if (charityamount > 0) {
						charityCal($('#grandTotal').val(), charityamount);
					}
				});

				$(window).load(function() {
					var $contents = $('#frame').contents();
					$contents.scrollTop($contents.height());
				});

				var coupnId = '';
				var couponCode = '';
				var regids = '';

				$("#form").submit(function(event) {
					event.preventDefault();
					debugger;
					//$("#amount").val($("#totalamt").text().replace(/\,/g,''));
					var charityAmt = $('.donation-box.active .charityamount').val();
					//var tlamt = <?= $totl_val_amt; ?>;
					$.post("<?= WEB_ROOT; ?>race_reg_order", $("#form").serialize() + '&charityAmt=' + charityAmt)
						.fail(function() {
							$.alert({
								title: 'Alert!',
								content: 'Something went wrong, Please try again!',
							});
						})
						.done(function(data) {
							if (data == "amount_unmatched") {
								$("#amtmismatch_err").show();
								$("#amtmismatch_err").html("Something went wrong, Please try again!");
							} else {
								$("#spinnerLoad").css("display", "block");
								$(".regblocktoggle").css("display", "none");
								$("#regForm").html(data);
								// $("#logo").child().css("width", "auto");
							}
						});
				});
				$('#charitypicker').change(function(event) {
					var ngo_val = event.target.value;
					if (ngo_val != '') {
						var event_id = $("#eventID").val();
						var purchase_charity_bib = '<?php echo $purchase_charity_bib?>';
						if(event_id == '9678' && purchase_charity_bib==1){
						var charity_amount = '<?= isset($data['purchase_bib_amt']) ? $data['purchase_bib_amt'] : 0; ?>';
						}
						else{
							var charity_amount = '<?= isset($data['charity_amount']) ? $data['charity_amount'] : 0; ?>';
						}
						$.ajax({
							type: "POST",
							url: '<?= WEB_ROOT; ?>api/ngo_api.php',
							data: {
								ngo_id: ngo_val,
								charity_amount: charity_amount,
								event_id: event_id
							}
						}).done(function(response) {
							// console.log(response);
							$('.charityhide').empty();
							$('#wrapper').empty().append(response);
							charityCal($('#grandTotal').val(), $('.donation-box.active .charityamount').val());
							$('#ngo_id').val(ngo_val);
						}).fail(function(response) {
							$('#spinny').hide();
							alert('Failed to submit the form!');
						});
					} else {
						$.alert({
							title: 'Alert!',
							content: 'Please select a Charity to continue',
						});
					}
				});
				$(document).on('change', 'input[name=charity_amunt]', function() {
					var amt = $(this).val();
					var min_amnt = $('#charity_hiddenamt').val();
					if (parseInt(amt) < parseInt(min_amnt)) {
						$.alert({
							title: 'Alert!',
							content: 'Minimum amount should be <b>' + min_amnt + '</b>',
						});
						$(this).val(min_amnt);
						charityCal($('#grandTotal').val(), min_amnt);

					} else {
						charityCal($('#grandTotal').val(), $('.donation-box.active .charityamount').val());
					}
				});

				function charityCal(total, charityamount) {
					debugger;
					var grand_amt = (parseFloat(total) + parseFloat(charityamount)).toFixed(2);
					
					if(<?php echo "'".$type."'"; ?> == 'INR'){
						$('#donated_amt').html('<i class="fa fa-inr"></i> ' + parseFloat(charityamount).toFixed(2));
						$('#grand_total').html('<i class="fa fa-inr"></i> ' + grand_amt);
					}else{
						$('#donated_amt').html('<i class="fa fa-usd"></i> ' + parseFloat(charityamount).toFixed(2));
						$('#grand_total').html('<i class="fa fa-usd"></i> ' + grand_amt);
					}
					
					$('#donated_amt').html(charityamount);
					//$('#charity_amt').val(charityamount);
					$("#amount").val(grand_amt);
					$('.donation').show();
				}
				var event_id = $("#eventID").val();
				/* if(event_id == 753) {
					$(".apply_coupon").attr("disabled", "disabled");
					$(".cout").attr("disabled", "disabled");
				} */
				$(document).on('click', '.apply_coupon', function() {
					//$(".apply_coupon").click(function() {
					// debugger;
					//var a = $(this).parent().parent().attr('id');
					var a = $(this).parent().parent().attr('id');
					var rmv = $(this).parent().attr('id');

					var b = "#amount_" + a;
					var raceAmount = $(b).val();
					$("#actamount").val(raceAmount);
					console.log($("#actamount").val(raceAmount));
					var d = "#race_" + a;
					var bulk_race_id = $(d).val();
					$("#bulk_race").val(bulk_race_id);

					var bcoupon = "#coupon_" + a;
					var bulk_c = $(bcoupon).val();
					$("#bulk_coupon").val(bulk_c);

					if ($(bcoupon).val() == '') {
						$.alert({
							title: 'Alert!',
							content: 'Please Enter Valid Coupon Code',
						});
						return false;
					}
					$("#coupon_code").val($("#coupon_in").val());
					bulkApplyCoupon(a, rmv);

				});
				var isValid;

				function removeCoupon(regID, disAmt) {
					// debugger;
					var processing_fee 		= <?= (isset($e['processing_fee']) and $e['processing_fee'] != "") ? $e['processing_fee'] : 0 ?>;
					var tax 				= <?= (isset($e['tax']) and $e['tax'] != "") ? $e['tax'] : 0 ?>;
					var taxType 			= $('#tax_type_'+regID).val();
					var ttotal_amount 		= $("#raceamount").val();
					var currRaceAmt 		= $("#amount_couponApplyDiv_"+regID).val();
					var aaamount 			= parseFloat(ttotal_amount) + parseFloat(disAmt);
					var totalGST 			= $('#total_gst').val();
					var currentGST 			= $('#tax_type_gst_'+regID).val();
					var gstOnRaceAmt 		= 0;
					var aaamount 			= aaamount.toFixed(2)

					if($("#eventID").val() == '9825'){
						$('.femaledis').show();
						var getDisAmt = $('#discountByUsers_'+regID).val();
						if(getDisAmt > 0){
							var nDisAmt = parseFloat(getDisAmt)+parseFloat($('#discountByUsers').val());
							// alert(getDisAmt);
							// alert($('#discountByUsers').val());
							// alert(nDisAmt);
							var ugetDisAmt = $('#discountByUsers').val(nDisAmt);
							var aaamount =  parseFloat(aaamount)-parseFloat(nDisAmt);
							// alert(aaamount);
							$('#grmdiscount').html($currency+' '+nDisAmt); 
						}
					}

					if($("#eventID").val() == '9690'){
						var discountByusers= $('#discountByusers').val();
						if(discountByusers > 0){
							var discountGRM = (aaamount*discountByusers)/100;
							var aaamount = aaamount-discountGRM;
							$('#grmdiscount').html($currency+' '+discountGRM);
						}

					}
					if($("#eventID").val() == '9722'){
						var discountByusers= $('#discountByusers').val();
						if(discountByusers > 0){
							var discountGRM = (aaamount*discountByusers)/100;
							var aaamount = aaamount-discountGRM;
							$('#grmdiscount').html($currency+' '+discountGRM);
						}

					}
					//alert(taxType);

					if(taxType == 1){
						var actual_gst 		= aaamount * 18 / 100;
						var gstOnRaceAmt 	= parseFloat(actual_gst);
						$("#gstraceamt").html($currency+' '+gstOnRaceAmt.toFixed(2));
						$('#total_gst').val(gstOnRaceAmt.toFixed(2));
					}
					var gstOnRaceAmt 		=  $('#total_gst').val();
					var serviceCharge = ((parseFloat(aaamount)+parseFloat(gstOnRaceAmt)) * processing_fee) / 100;
					var serviceCharge = serviceCharge.toFixed(2);
					$('#service_charge').html($currency+' '+serviceCharge);

					var gstProcessFee = (parseFloat(serviceCharge) * tax) / 100;
					$('#gstProcessFee').html($currency+' '+gstProcessFee.toFixed(2));
					var serviceTax = (parseFloat(gstOnRaceAmt)+parseFloat(gstProcessFee));
					var serviceTax = serviceTax.toFixed(2);
					$('#service_tax').html($currency+' '+serviceTax);
					var ttamount = parseFloat(aaamount) +parseFloat(serviceCharge) + parseFloat(serviceTax);
					
					// debugger;
					var amt = parseFloat($('#amount_couponApplyDiv_' + regID).val()).toFixed(2);
					var in_PUT = '<input type="text" id="coupon_couponApplyDiv_' + regID + '" class="form-controlv cout" name="bulk_coupon_couponApplyDiv_' + regID + '" placeholder="Enter Coupon"><input type="button" class="btn btn-green btn-xs apply_coupon" value="Apply">';
					var in_div = '#couponInnerDiv_' + regID;
					$(in_div + ' ' + '.remove_coupon').hide();
					$(in_div).html(in_PUT);
					$("#pamount_" + regID).html($currency+' '+amt);
					if(event_id == '9678'){
						var bulkId        = $('#bulkId').val();
						if(bulkId > 0){
							var singleCharge        = $('#bulkCharge').val();
						}
						else{
						var singleCharge        = $('#singleCharge').val();
						}
						//alert(bulkId);
						var rceAmt 				= $("#raceamount").val();
						var ttamount 			=  parseFloat(ttamount)+parseFloat(singleCharge);
					}

					if($("#eventID").val() == '9825'){
						$('.femaledis').show();
						var getDisAmt = $('#discountByUsers').val();
						
						if(getDisAmt > 0){
							var aaamount =  parseFloat(aaamount)+parseFloat(getDisAmt);
							var aaamount = aaamount.toFixed(2);
						}
						
					}
					if($("#eventID").val() == '9826'){
						var ExtraAmt 	=	 <?php echo $photographExtra['value']?>;
						var ExtraAmt1 	=	 $('#prepic').val();
						if(ExtraAmt == 1){
							var ttamount = parseFloat(ttamount)+parseFloat(ExtraAmt1);
						}
					}
					$("#grand_total").html($currency+' '+numberWithCommas(ttamount));
					$("#subtotal").html($currency+' '+numberWithCommas(aaamount));
					$("#amount").val(ttamount.toFixed(2));
					$("#raceamount").val(aaamount);
					if($("#eventID").val() == '9678'){
						$("#saveform").attr("disabled", "TRUE");
					}


				};

				function fullcoupon() {
					$(".full_coupon").each(function() {
						var element = $(this);
						if (element.val() == "") {
							isValid = false;
						}
					});
					return isValid;
				}

				function bulkApplyCoupon(a, rmv) {
					var processing_fee = <?= (isset($e['processing_fee']) and $e['processing_fee'] != "") ? $e['processing_fee'] : 0 ?>;
					var tax = <?= (isset($e['tax']) and $e['tax'] != "") ? $e['tax'] : 0 ?>;
					var regid = a.replace("couponApplyDiv_", "");
					var taxType = $('#tax_type_'+regid).val();
					var totalGST = $('#total_gst').val();

					$.post("<?= WEB_ROOT; ?>bulk_apply_coupon", $("#coupon_form").serialize()).fail(function() {
							$.alert({
								title: 'Alert!',
								content: 'Please Enter Valid Coupon Code',
							});
						})
						.done(function(data) {
							var obj = jQuery.parseJSON(data);
							if (obj.status == 'success') {
								// debugger;
								
								var regid = a.replace("couponApplyDiv_", "");
								//console.log(obj);
								
								$("#amountDisplay").html(obj.amount);
								var coupon_validity = $("#iscoupon_" + regid).val();
								console.log(coupon_validity);
								if (obj.discountAmount == 0) {
									obj.discount = obj.discountPercentage;
									var per_symbol = '% OFF';
								} else if (obj.discountPercentage == 0) {
									obj.discount = obj.discountAmount;
									var per_symbol = ' OFF';

								}
								var amount = obj.amount;
								var amount = amount;
								
								//fullcoupon(); HR2021
								if ((obj.coupon.indexOf("AIRHM5") >= 0 || obj.coupon.indexOf("AIRHMT") >= 0 || obj.coupon.indexOf("AIRT41") >=
								0 || obj.coupon.indexOf("AIRTH4") >= 0 || obj.coupon.indexOf("AIRTF4") >= 0
								|| obj.coupon.indexOf("HR2021") >= 0 || obj.coupon.indexOf("HRMD21") >= 0 || obj.coupon.indexOf("TRAINF") >= 0
								|| obj.coupon.indexOf("SHIVT1") >= 0 || obj.coupon.indexOf("SHIVH1") >= 0 || obj.coupon.indexOf("SHIVF1") >= 0)
								&& coupon_validity == 1) {
									$("#" + a).html('<span><i class="fa fa-check-circle" style="color:green"></i> <b>' + obj.coupon + '</b> - Applied Successfully (<font color="green">' + obj.discount + per_symbol + '</font></span><span class="amountstyle"><font color="green">-'+$currency+' '+amount + '</font></span>)');
									var isValid = true;
									$(".full_coupon").each(function() {
										var element = $(this);
										var reg_ID = element.attr('id').replace("iscoupon_", "");
										if (element.val() == 1 && $("#couponApplyDiv_" + reg_ID + ":contains('100%')").length == 0) {
											isValid = false;
										}
									});
									if (isValid) {
										$("#saveform").prop('disabled', false);
									} else {
										$("#saveform").prop('disabled', true);
									}
								} else if (obj.discount == "100" && coupon_validity == 1) {
									console.log('here');
									$("#" + a).html('Discount <font color="green">' + obj.discount + per_symbol + '</font>, Applied with <font color="green">' + obj.coupon + '</font>');
									var isValid = true;
									$(".full_coupon").each(function() {
										var element = $(this);
										var reg_ID = element.attr('id').replace("iscoupon_", "");
										if (element.val() == 1 && $("#couponApplyDiv_" + reg_ID + ":contains('100%')").length == 0) {
											isValid = false;
										}
									});
									if (isValid) {
										console.log('here1');
										$("#saveform").prop('disabled', false);
									} else {
										console.log('here2');
										$("#saveform").prop('disabled', true);
									}
								} else if (obj.discount != "100" && coupon_validity == 1) {
									console.log('here3');
									var isValid = true;
									$(".full_coupon").each(function() {
										var element = $(this);
										if (element.val() == 1 && $("#couponApplyDiv_" + regid + ":contains('100%')").length == 0) {
											isValid = false;
										}
									});

									if (!isValid) {
										console.log('here4');
										$("#saveform").prop('disabled', true);
									} else {
										console.log('here5');
										$("#saveform").prop('disabled', false);
									}

									$("#coupon_couponApplyDiv_" + regid).val('');
									
									$.alert({
										title: 'Alert!',
										content: 'Registrations closed. Corporate users holding full value coupons can continue to register for the event.',
									});
									return false;
								} else {
									// debugger;
									$("#" + rmv).html('<span><i class="fa fa-check-circle" style="color:green"></i> <b>' + obj.coupon + '</b> - Applied Successfully (<font color="green">' + obj.discount + per_symbol + '</font></span><span class="amountstyle"><font color="green">-'+$currency+' '+amount + '</font></span>)<div class="remove_coupon" id="rm_' + amount + '" onclick="removeCoupon(' + regid + ',' + amount + ')"><b>Remove coupon</b></div>');
									if($("#eventID").val() == '9678'){
										$("#saveform").removeAttr("disabled");
									}

								}
								//debugger;
								var ttotal_amount 	= $("#raceamount").val();
								//alert(ttotal_amount);
								//alert(amount);
								var aaamount 		= (ttotal_amount - amount).toFixed(2);
								//alert(aaamount);
								var gstOnRaceAmt 	=0;
								$("#raceamount").val(aaamount);
								if($("#eventID").val() == '9690'){
									var discountByusers= $('#discountByusers').val();
									if(discountByusers > 0){
										var discountGRM = (aaamount*discountByusers)/100;
										var aaamount = aaamount-discountGRM;
										$('#grmdiscount').html($currency+' '+discountGRM);
									}
								}

								if($("#eventID").val() == '9825'){
									var discountByusers= $('#discountByUsers_'+regid).val();
									// alert(aaamount);
									if(discountByusers > 0){
										var totalDiss = $('#discountByUsers').val();
										// alert(totalDiss);
										// alert(discountByusers);
										var newDis = parseFloat(totalDiss)-parseFloat(discountByusers);
										$('#grmdiscount').html($currency+' '+newDis);
										$('#discountByUsers').val(newDis);
										var aaamount = aaamount-newDis;
										var totalDiss = $('#discountByUsers').val();
										if(totalDiss == 0){
											$('.femaledis').hide();
										} 
									}
								}

								if($("#eventID").val() == '9722'){
									var discountByusers= $('#discountByusers').val();
									if(discountByusers > 0){
										var discountGRM = (aaamount*discountByusers)/100;
										var aaamount = aaamount-discountGRM;
										$('#grmdiscount').html($currency+' '+discountGRM);
									}
								}
								$("#subtotal").html($currency+' '+numberWithCommas(aaamount));
								if(taxType == 1){
									var actual_gst 		= amount * 18 / 100;
									var gstOnRaceAmt 	= totalGST - actual_gst;
									
									$('#total_gst').val(gstOnRaceAmt.toFixed(2));
									$('#tax_type_gst_'+regid).val(gstOnRaceAmt);
									$("#gstraceamt").html($currency+' '+gstOnRaceAmt.toFixed(2));
									
								}
								gstOnRaceAmt 			= $('#total_gst').val();
								var aaamount 			= parseFloat(aaamount)+parseFloat(gstOnRaceAmt);
								var serviceCharge 		= (parseFloat(aaamount) * processing_fee) / 100;
								var serviceTax 			= (parseFloat(serviceCharge) * tax) / 100;
								var totalTax 			= parseFloat(gstOnRaceAmt)+parseFloat(serviceTax);
								$('#service_charge').html($currency+' '+serviceCharge.toFixed(2));
								$('#gstProcessFee').html($currency+' '+serviceTax.toFixed(2));
								$('#service_tax').html($currency+' '+totalTax.toFixed(2));

								var ttamount 			= aaamount + serviceCharge + serviceTax;
								ttamount 				= ttamount.toFixed(2);
								if($("#eventID").val() == '9678'){
										var bulkId        = $('#bulkId').val();
									if(bulkId > 0){
										var singleCharge        = $('#bulkCharge').val();
									}
									else{
									var singleCharge        = $('#singleCharge').val();
									}
									//alert(bulkId);
									var ttamount 			=  parseFloat(ttamount)+parseFloat(singleCharge);
								}

								if($("#eventID").val() == '9826'){
									var ExtraAmt 	=	 <?php echo $photographExtra['value']?>;
									var ExtraAmt1 	=	 $('#prepic').val();
									if(ExtraAmt == 1){
										var ttamount = parseFloat(ttamount)+parseFloat(ExtraAmt1);
									}
								}
								$("#grandTotal").val(ttamount);
								if (parseInt($('#donated_amt').html()) > 0) {
									ttamount = parseInt(ttamount) + parseInt($('#donated_amt').html());
								}
								$("#amount").val(ttamount);
								$("#grand_total").html($currency+' '+numberWithCommas(ttamount));
								// debugger;

								$("#pamount_" + regid).html(obj.pamount);

								regids += regid + ',';
								var pregids = regids.substring(0, regids.length - 1);
								$("#reg_ids").val(pregids);

								$("#key").val(obj.key);
								coupnId += obj.coupon_id + ',';
								var coupnIds = coupnId.substring(0, coupnId.length - 1);
								$("#coupon_id").val(coupnIds);
								couponCode += obj.coupon + ',';
								var couponCodes = couponCode.substring(0, couponCode.length - 1);
								$("#coupon").val(couponCodes);

								if($("#eventID").val() == '9825'){
									var discountByusers= $('#discountByUsers').val();
									// alert(aaamount);
									if(discountByusers > 0){
										var aaamount = (parseFloat(aaamount)+parseFloat(discountByusers)).toFixed(2);
										$("#subtotal").html($currency+' '+numberWithCommas(aaamount)); 
									}
									else{
										$("#subtotal").html($currency+' '+numberWithCommas(aaamount));
									}
								}
								else{
									$("#subtotal").html($currency+' '+numberWithCommas(aaamount));
								}
								
								//Showing Remove Coupon text
							} else if (obj.status == 'expired') {
								$.alert({
									title: 'Alert!',
									content: 'Coupon is Invalid / Expired',
								});
							} else {
								$.alert({
									title: 'Coupon failed',
									content: 'Coupon does not exist',
								});
							}

						});
				}

				$("#DC").click(function(event) {

					if ($("#coupon_in").val() == '') {
						$.alert({
							title: 'Alert!',
							content: 'Please Enter Coupon',
						});
						return false;
					}
					$("#coupon_code").val($("#coupon_in").val());
					debugger;
					if ($("#eventID").val() != 438 && parseInt($('input[name="reg-inner_count"]').val()) < 10) {
						$.alert({
							title: 'Alert!',
							content: 'Not eligible to use coupon!',
						});
						return false;
					}

					applyCoupon();
				});

				function numberWithCommas(number) {
				    var parts = number.toString().split(".");
				    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				    return parts.join(".");
				}


				function applyCoupon() {
					//debugger;
					if ($("#eventID").val() != 438) {
						var coupon = "12345";
						var discount = 15;
						var total_amount = $('#hideamount').html();
						if (coupon != $("#coupon_in").val()) {
							$.alert({
								title: 'Alert!',
								content: 'Coupon is Invalid / Expired',
							});
						} else {
							var amount = (total_amount - (discount * total_amount / 100)).toFixed(2);
							$("#totalamt").html($("#totalamt").html().replace(/\d+./g, '') + amount);
							$("#dc_bulk").html('Discount <font color="green">' + discount + '%</font>, Applied with <font color="green">' + coupon + '</font>');

						}
					} else {
						var processing_fee = <?= (isset($e['processing_fee']) and $e['processing_fee'] != "") ? $e['processing_fee'] : 0 ?>;
						var tax = <?= (isset($e['tax']) and $e['tax'] != "") ? $e['tax'] : 0 ?>;
						$.post("<?= WEB_ROOT; ?>apply_coupon", $("#form").serialize()).fail(function() {
								$.alert({
									title: 'Alert!',
									content: 'Please Enter Valid Coupon Code',
								});
							})
							.done(function(data) {
								var obj = jQuery.parseJSON(data);
								if (obj.status == 'success') {
									$("#amountDisplay").html(obj.amount);
									$("#dcDIV").html('Discount <font color="green">' + obj.discount + '%</font>, Applied with <font color="green">' + obj.coupon + '</font>');
									var amount = obj.amount;
									var daycare_amount = 0;
									var TASALLI = 0;
									var Ananda = 0;
									<?php
									if (isset($TASALLI) and $TASALLI != 0) {
										?>
										TASALLI = <?= $TASALLI; ?>;
									<?php
									}
									if (isset($Ananda) and $Ananda != 0) {
										?>
										Ananda = <?= $Ananda; ?>;
									<?php
									}
									if ($daycare_amount != 0) {
										?>
										daycare_amount = <?= $daycare_amount; ?>;
									<?php
									}
									?>

									var amount = amount + daycare_amount + Ananda + TASALLI;
									var serviceCharge = (parseFloat(amount) * processing_fee) / 100;
									var serviceTax = (parseFloat(serviceCharge) * tax) / 100;
									var ttamount = amount + serviceCharge + serviceTax;
									ttamount = ttamount.toFixed(2);
									
									$("#amount").val(ttamount);
									$("#grand_total").html('&#8377; ' + ttamount);
									$("#key").val(obj.key);
									$("#coupon_id").val(obj.coupon_id);
									$("#coupon").val(obj.coupon);
								} else {
									$.alert({
										title: 'Alert!',
										content: 'Coupon is Invalid / Expired',
									});
								}
							});
					}
				}
			</script>
			<?php

			?>
		</div>
	</div>
	</div>
	</div>
	</div>
	</div>
</section>

<script>
if($("#eventID").val() == '9678'){
	$(document).ready(function(){
		$("#saveform").attr("disabled", "TRUE");
	});
}
</script>
<?php if($purchase_charity_bib == 1){?>
<script>
	if($("#eventID").val() == '9678'){
		$(document).ready(function(){
			$("#saveform").attr("disabled", "TRUE");
			$('.charitypicker').css('border','1px solid red');
		});

		$('.charitypicker').on('change',function(){
			var charity = $('.charitypicker').val();
			//alert(charity.length);
			if(charity.length == 0){
				$("#saveform").attr("disabled", "TRUE");
				$('.charitypicker').css('border','1px solid red');
			}
			else{
				$("#saveform").removeAttr("disabled");
				$('.charitypicker').css('border','1px solid #ddd');
			}
		});
	}
</script>
<?php }?>

<?php
require_once 'templates/footer.php';
?>
<!-- <script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});
</script> -->