<!doctype html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>iFINISH</title>
      <link rel="icon" href="http://www.ifinish.in/images/favicon.png" type="image/png" sizes="16x16">
      <link rel="stylesheet" href="/css/bootstrap.css">
      <style>
         @import url('https://fonts.googleapis.com/css?family=Montserrat');
         body, h1, h2, h3, h4, h5, h6{
         font-family: 'Montserrat' !important;
         }
         .table-bordered>tbody>tr>td,.table>thead>tr>th,.table-bordered>tbody>tr>th{
         border:1px solid #8c99b3 !important;
         }
         .title_bg{
         background:#363b45;
         color:#fff;
         padding:8px;
         font-weight:800;
         }
         .fontsize20{
         font-size:20px;
         }
         .fontweight800{
         font-weight:800;
         margin:8px 0px;
         }
         .fontweight600{
         font-weight:600;
         }
         .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{
         border-bottom-width:0px !important;
         }
         .table-bordered>tbody>tr>.yellowborder{
         border:2px solid #ffc909 !important;
         }
      </style>
      <link href="/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="/css/font-awesome.min.css">
   </head>
   <?php
      require 'includes/master.inc.php';
      function getIndianCurrency($number)
      {
          $decimal = round($number - ($no = floor($number)), 2) * 100;
          $hundred = null;
          $digits_length = strlen($no);
          $i = 0;
          $str = array();
          $words = array(0 => '', 1 => 'one', 2 => 'two',
              3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
              7 => 'seven', 8 => 'eight', 9 => 'nine',
              10 => 'ten', 11 => 'eleven', 12 => 'twelve',
              13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
              16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
              19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
              40 => 'forty', 50 => 'fifty', 60 => 'sixty',
              70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
          $digits = array('', 'hundred','thousand','lakh', 'crore');
          while( $i < $digits_length ) {
              $divider = ($i == 2) ? 10 : 100;
              $number = floor($no % $divider);
              $no = floor($no / $divider);
              $i += $divider == 10 ? 1 : 2;
              if ($number) {
                  $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                  $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                  $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
              } else $str[] = null;
          }
          $Rupees = implode('', array_reverse($str));
          $paise = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
          return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise ;
      }
      // echo "<pre>";print_r($_GET);die;
      $event_id      =  isset($_GET['eid']) ? $_GET['eid'] : "";
      $reg_id        =  isset($_GET['rid']) ? $_GET['rid'] : "";
      
      if(!empty($event_id) && !empty($reg_id)){
      
         //  Fetching all required data for invoice
         $getRegDetails    =     $db->getRow("SELECT r.id,r.event_id,r.user_id,r.bulk_id,r.payment_id,r.amount,
         r.coupon_id,r.paid_amount,r.paid_in,r.registration_at,r.registration_no,
         r.charity_amt,r.ngo_id,rd.first_name,rd.last_name,rd.gender,rd.email,rd.address,
         rd.state,e.name,e.organizer_id,e.company_name,e.address1,e.address2,e.email as contact_email,
         e.pan_number,e.gst_number,e.processing_fee,e.tax,c.discountPercentage,c.discountAmount,ra.name as race_name,ra.id as race_id,
         ra.amount as race_amount,ra.tax_type,u.address as user_address,u.gst_no
         FROM ifinish_ifinish.ifinish_registration as r LEFT JOIN  ifinish_ifinish.ifinish_registration_data as rd
         ON r.id=rd.reg_id LEFT JOIN ifinish_events  as e ON r.event_id = e.id LEFT JOIN ifinish_discount_coupons as c
         ON r.coupon_id = c.id LEFT JOIN ifinish_event_race as ra ON ra.id=r.race_id LEFT JOIN ifinish_user_details as u
         ON u.user_id = r.user_id
         where r.event_id=".$event_id." and r.id=".$reg_id." and r.payment_id > 0");
         // echo "<pre>";print_r($getRegDetails);die;
         //  Fetching earlybird reg data
         $getEarlyReg   =  $db->getRow("SELECT * FROM ifinish_ifinish.ifinish_early_registration_fee where race_id=".$getRegDetails['race_id']." and 
         (SELECT registration_at FROM ifinish_registration WHERE event_id=".$event_id." and id=".$reg_id." and race_id=".$getRegDetails['race_id']." and payment_id > 0) BETWEEN valid_from AND valid_to");
      
         if(isset($getEarlyReg) && !empty($getEarlyReg)){
            $getRegDetails['is_early']                =  1;
            $getRegDetails['race_amount']             =  $getEarlyReg['amount'];
         }

         if($event_id == '9825'){
            if($getRegDetails['gender'] == 'female'){
               $getRegDetails['race_amount']             =  $getRegDetails['race_amount']-($getRegDetails['race_amount']*10/100);
            }
         }
         
         // Calculating coupon discount
         $discounted_race_amount                      =  $getRegDetails['race_amount'];
         $getRegDetails['discount']                   =  0;
         if($getRegDetails['coupon_id'] > 0){
            if($getRegDetails['discountPercentage']>0){
               $getRegDetails['discount']             =  $getRegDetails['race_amount']-($getRegDetails['race_amount']*$getRegDetails['discountPercentage']/100);
               $discounted_race_amount                =  $getRegDetails['discount'];
            }
            else if($getRegDetails['discountAmount']>0){
               $getRegDetails['discount']             =  $getRegDetails['race_amount']-$getRegDetails['discountAmount'];
               $discounted_race_amount                =  $getRegDetails['discount'];
            }
         }
         
         // calculating tax 
         if($getRegDetails['processing_fee'] == 0 && $getRegDetails['tax'] == 0 && ($getRegDetails['organizer_id'] == 191 || $event_id == 9825)){
            $getRegDetails['race_amount']             =  round($getRegDetails['amount']/1.235696,2); //Use 1.18 to get calculation without GST on race amount
            if($getRegDetails['coupon_id'] > 0){
               if($getRegDetails['discountPercentage']>0){
                  $getRegDetails['discount']          =     $getRegDetails['race_amount']-($getRegDetails['race_amount']*$getRegDetails['discountPercentage']/100);
               }
               else if($getRegDetails['discountAmount']>0){
                  $getRegDetails['discount']          =     $getRegDetails['race_amount']-$getRegDetails['discountAmount'];
               }
            }
            $getRegDetails['gst_race']                =  round(($getRegDetails['race_amount']*18)/100,2);
            $getRegDetails['race_gst']                =  $getRegDetails['race_amount']+$getRegDetails['gst_race'];
         }
         else{
            if($getRegDetails['tax_type'] == 1){
               $getRegDetails['gst_race']             =  round(($discounted_race_amount*18)/100,2);
               $getRegDetails['race_gst']             =  $discounted_race_amount+$getRegDetails['gst_race'];
            }
            else{
               $getRegDetails['gst_race']             =  round(($discounted_race_amount*0)/100,2);
               $getRegDetails['race_gst']             =  $discounted_race_amount+$getRegDetails['gst_race'];
            }
         }

         if($getRegDetails['organizer_id'] == 191 || $event_id == 9825){
            $getRegDetails['race_processing_fee']        =  round(($getRegDetails['race_gst']*4)/100,2);
            $getRegDetails['gstOnProcess']               =  round(($getRegDetails['race_processing_fee']*18)/100,2);
         }
         else{
            if($getRegDetails['processing_fee'] == 0 && $getRegDetails['tax'] == 0){
               $getRegDetails['race_processing_fee']        =  round(($getRegDetails['race_gst']*0)/100,2);
               $getRegDetails['gstOnProcess']               =  round(($getRegDetails['race_processing_fee']*0)/100,2);
            }
            else{
               $getRegDetails['race_processing_fee']        =  round(($getRegDetails['race_gst']*4)/100,2);
               $getRegDetails['gstOnProcess']               =  round(($getRegDetails['race_processing_fee']*18)/100,2);
            }
         }
         $getRegDetails['total_amount']               =  round($getRegDetails['race_gst']+$getRegDetails['race_processing_fee']+$getRegDetails['gstOnProcess'],2);
         $getRegDetails['total_gst']                  =  $getRegDetails['gst_race']+$getRegDetails['gstOnProcess'];
         $getRegDetails['cgst']                       =  ($getRegDetails['state'] == 'Telangana') ? 0 : $getRegDetails['total_gst']/2;                        
         $getRegDetails['sgst']                       =  ($getRegDetails['state'] == 'Telangana') ? 0 : $getRegDetails['total_gst']/2;                        
         $getRegDetails['igst']                       =  ($getRegDetails['state'] == 'Telangana') ? $getRegDetails['total_gst'] : 0;  
         $type                   =     $getRegDetails['paid_in'];
         // echo "<pre>";print_r($getRegDetails);die;
         
         if($event_id == '9678'){
            $columnsArray        =     array('Cost Towards','Accounting Code(Services)','Race','Qty','Rate/Price','Total');
            $columnValues        =     array($getRegDetails['name'],'999651',$getRegDetails['race_name'],'1',formatCurrency($getRegDetails['total_amount'],$type),formatCurrency($getRegDetails['total_amount'],$type));
         }
         else{
            $columnsArray        =     array('Event','Type','Race','Qty','Rate/Price','Total');
            $columnValues        =     array($getRegDetails['name'],'Event Registration',$getRegDetails['race_name'],'1',formatCurrency($getRegDetails['total_amount'],$type),$getRegDetails['total_amount']);
         }
      
      }
      else{
      
      }
      
      
      ?>
   <!-- End -->
   <body>
      <div class="container">
      <div class="row">
         <div class="col-xs-6">
            <img style="margin-bottom: 20px;" height="104" src="https://www.ifinish.in/uploads/EL_<?php echo md5($event_id);?>.jpg">
         </div>
         <?php
            ?>
         <div class="col-xs-6 text-right">
            <div class="fontweight800 fontsize20">INVOICE</div>
            <div class="fontweight800"><?=$getRegDetails["registration_no"];?></div>
            <?php $d=date_create($getRegDetails["registration_at"]);?>
            <div class="fontweight800"><small>Date of Invoice <?=date_format($d,"d/m/Y H:i:s");?></small></div>
            <!--div><small><a target="_blank" href="invoicepdf.php?id=<?php echo $_GET['id'];?>&uid=<?php echo $userid;?>">Download Invoice</a></small></div-->
         </div>
      </div>
      <?php //echo "<pre>";print_r($columnsArray);die;?>
      <div class="row">
         <style>
            td{
            font-size:12px !important;
            font-weight:bold;
            }
            th{
            font-size:14px !important;
            }
            .title_bg{
            font-size:13px !important;
            }
         </style>
         <div class="col-xs-12">
            <!-- / end client details section -->
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <?php $i=0;foreach($columnsArray as $columns){?>
                     <th><?=$columns?></th>
                     <?php }?>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <?php $i=0;foreach($columnValues as $values){?>
                     <th><?=$values?></th>
                     <?php }?>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="col-xs-12">
            <div class="col-xs-5" style="padding-left: 0px;">
               <div class="title_bg">
                  Details of the receiver
               </div>
               <table class="table table-bordered">
                  <tr>
                     <td class="col-xs-6 ">Name</td>
                     <td class="col-xs-6 "><b><?=$getRegDetails["first_name"]." ".$getRegDetails["last_name"];?></b></td>
                  </tr>
                  <?php if(!empty($getRegDetails["user_address"])){?>
                  <tr>
                     <td>Address</td>
                     <td><b><?=$getRegDetails["user_address"];?></b></td>
                  </tr>
                  <?php }?>
                  <?php if(!empty($getRegDetails["state"])){?>
                  <tr>
                     <td>State</td>
                     <td><b><?=$getRegDetails["state"];?></b></td>
                  </tr>
                  <?php }?>
                  <?php if(isset($getRegDetails["gst_no"]) && !empty($getRegDetails["gst_no"])):?>
                  <tr>
                     <td>GST No.</td>
                     <td><b><?=$getRegDetails["gst_no"];?></b></td>
                  </tr>
                  <?php endif;?>
               </table>
            </div>
            <div class="col-xs-7">
               <div class="title_bg">
                  Price Details
               </div>
               <table  class="table table-bordered">
                  <tr>
                     <td>Race Amount <?php if(isset($TotalBulk) && $TotalBulk > 0):echo "(".$TotalBulk.")";endif;?></td>
                     <td><b><?=formatCurrency($getRegDetails['race_amount'],$type);?></b></td>
                  </tr>
                  <?php if(!empty($getRegDetails["coupon_id"] > 0)){?>
                  <tr>
                     <td>Discount (<?=$getRegDetails["discountPercentage"]?$getRegDetails["discountPercentage"].'%':$getRegDetails["discountAmount"]?>)</td>
                     <td><b><?=formatCurrency($getRegDetails["discount"],$type);?></b></td>
                  </tr>
                  <?php }?>
                  <?php if($getRegDetails["gst_race"] > 0):?>
                  <tr>
                     <td>GST on Race Amount</td>
                     <td><b><?=formatCurrency($getRegDetails["gst_race"],$type);?></b></td>
                  </tr>
                  <?php endif;?>
                  <?php if($getRegDetails["race_processing_fee"] > 0):?>
                  <tr>
                     <td>Processing Fee</td>
                     <td><b><?=formatCurrency($getRegDetails["race_processing_fee"],$type);?></b></td>
                  </tr>
                  <?php endif;?>
                  <?php if($getRegDetails["gstOnProcess"] > 0):?>
                  <tr>
                     <td>GST on Processing Fee</td>
                     <td><b><?=formatCurrency($getRegDetails["gstOnProcess"],$type);?></b></td>
                  </tr>
                  <?php endif;?>
                  <tr>
                     <td>CGST(9%)</td>
                     <td><b><?=formatCurrency($getRegDetails["cgst"],$type);?></b></td>
                  </tr>
                  <tr>
                     <td>SGST(9%)</td>
                     <td><b><?=formatCurrency($getRegDetails["sgst"],$type);?></b></td>
                  </tr>
                  <tr>
                     <td>IGST(18%)</td>
                     <td><b><?=formatCurrency($getRegDetails["igst"],$type);?></b></td>
                  </tr>
                  <?php
                     if($type='inr') {
                      ?>
                  <tr class="yellowborder">
                     <td class="yellowborder"><b>Total(in Figure)</b></td>
                     <td class="yellowborder"><b><?=formatCurrency($getRegDetails["total_amount"],$type);?></b></td>
                  </tr>
                  <?php
                     }
                     ?>
                  <tr  class="yellowborder">
                     <td class="yellowborder"><b>Total(In Words)</b></td>
                     <td class="yellowborder"><b><?=ucwords(strtolower(getIndianCurrency($getRegDetails["total_amount"])));?></b></td>
                  </tr>
               </table>
            </div>
         </div>
         <?php if($getRegDetails['company_name'] || $getRegDetails['address1'] || $getRegDetails['address2'] || $getRegDetails['pan_number'] || $getRegDetails['gst_number']){?>
         <div class="col-xs-5">
            <div class="title_bg">
               Information
            </div>
            <table class="table table-bordered fontweight600">
               <tbody>
                  <?php if(!empty($getRegDetails["company_name"])){?>
                  <tr>
                     <td><?php echo $getRegDetails['company_name']?></td>
                  </tr>
                  <?php }?>
                  <?php if(!empty($getRegDetails["pan_number"])){?>
                  <tr>
                     <td>PAN No. <?php echo $getRegDetails['pan_number']?></td>
                  </tr>
                  <?php }?>
                  <?php if(!empty($getRegDetails["gst_number"])){?>
                  <tr>
                     <td>GSTIN : <?php echo $getRegDetails['gst_number']?></td>
                  </tr>
                  <?php }?>
               </tbody>
            </table>
         </div>
         <?php }?>
         <div class="col-xs-7">
            <div class="span7">
               <div>
                  <div class="title_bg">
                     Contact Details
                  </div>
                  <table class="table table-bordered fontweight600">
                     <tbody>
                        <?php if(!empty($getRegDetails["address1"])){?>
                        <tr>
                           <td><?php echo $getRegDetails['address1']?></td>
                        </tr>
                        <?php }?>
                        <?php if(!empty($getRegDetails["address2"])){?>
                        <tr>
                           <td><?php echo $getRegDetails['address2']?></td>
                        </tr>
                        <?php }?>
                        <?php if(!empty($getRegDetails["contact_email"])){?>
                        <tr>
                           <td>email : <?php echo $getRegDetails['event_id'] == '9678'?" info@hyderabadrunners.com":$getRegDetails['contact_email']?></td>
                        </tr>
                        <?php }?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12"> 
               <b>Note:</b>
               If the registration is not approved, this invoice will stand null and void. 
               <?php if(isset($user_row["gst_no"]) && !empty($user_row["gst_no"])):?>
               GST can be claimed only if above mentioned GST# of receiver is correct.
               <?php endif; ?>
               <br>
               <br>
               <?php if($getRegDetails['event_id'] == '9678'){?> 
               <b>This is a digital invoice hence does not require signature.</b>
               <?php }?>
            </div>
         </div>
         <br>
         <?php if($getRegDetails['event_id'] == '9648' ){?> 
         <div class="row" >
            <div class="col-md-12" style="font-weight:800;text-align:right;">
               <div style="font-size:18px;"><br/>For Hyderabad Runners</div>
               <div>Authorised Signatory</div>
            </div>
         </div>
         <?php }?>
      </div>
   </body>
</html>