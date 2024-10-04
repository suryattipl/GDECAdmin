<?php
include('includes/master.inc.php');
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
$event_id           =   $_POST['event_id'];
$reg_id             =   $_POST['reg_id'];
$getRegDetails      =     $db->getRow("SELECT r.id,r.event_id,r.user_id,r.bulk_id,r.payment_id,r.amount,
r.coupon_id,r.paid_amount,r.paid_in,r.registration_at,r.registration_no,
r.charity_amt,r.ngo_id,rd.first_name,rd.last_name,rd.gender,rd.email,rd.address,
rd.state,e.name,e.organizer_id,e.company_name,e.address1,e.address2,e.email as contact_email,
e.pan_number,e.gst_number,c.discountPercentage,c.discountAmount,ra.name as race_name,ra.id as race_id,
ra.amount as race_amount,ra.tax_type,u.address as user_address,u.gst_no
FROM ifinish_ifinish.ifinish_registration as r LEFT JOIN  ifinish_ifinish.ifinish_registration_data as rd
ON r.id=rd.reg_id LEFT JOIN ifinish_events  as e ON r.event_id = e.id LEFT JOIN ifinish_discount_coupons as c
ON r.coupon_id = c.id LEFT JOIN ifinish_event_race as ra ON ra.id=r.race_id LEFT JOIN ifinish_user_details as u
ON u.user_id = r.user_id
where r.event_id=".$event_id." and r.id=".$reg_id." and r.payment_id > 0");

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
if($getRegDetails['processing_fee'] == 0 && $getRegDetails['tax'] == 0 && $getRegDetails['organizer_id'] == 191){
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
if($getRegDetails['organizer_id'] == 191){
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
$type                                        =   $getRegDetails['paid_in'];


$html = '';
$html .='<table  class="table table-bordered">
                  <tr>
                     <td>Race Amount</td>
                     <td><b>'.formatCurrency($getRegDetails['race_amount'],$type).'</b></td>
                  </tr>';
                  if(!empty($getRegDetails["discount"] > 0)){
                    $html .='<tr>
                     <td>Discount ('.$getRegDetails["discountPercentage"]?$getRegDetails["discountPercentage"].'%':$getRegDetails["discountAmount"].')</td>
                     <td><b>'.formatCurrency($getRegDetails["discount"],$type).'</b></td>
                  </tr>';
                   }
                  if($getRegDetails["gst_race"] > 0):
                    $html .='<tr>
                     <td>GST on Race Amount</td>
                     <td><b>'.formatCurrency($getRegDetails["gst_race"],$type).'</b></td>
                  </tr>';
                  endif;
                  if($getRegDetails["race_processing_fee"] > 0):
                    $html .='<tr>
                     <td>Processing Fee</td>
                     <td><b>'.formatCurrency($getRegDetails["race_processing_fee"],$type).'</b></td>
                  </tr>';
                  endif;
                  if($getRegDetails["gstOnProcess"] > 0):
                    $html .='<tr>
                     <td>GST on Processing Fee</td>
                     <td><b>'.formatCurrency($getRegDetails["gstOnProcess"],$type).'</b></td>
                  </tr>';
                  endif;
                  $html .='<tr>
                     <td>CGST(9%)</td>
                     <td><b>'.formatCurrency($getRegDetails["cgst"],$type).'</b></td>
                  </tr>';
                  $html .=' <tr>
                     <td>SGST(9%)</td>
                     <td><b>'.formatCurrency($getRegDetails["sgst"],$type).'</b></td>
                  </tr>';
                  $html .='<tr>
                     <td>IGST(18%)</td>
                     <td><b>'.formatCurrency($getRegDetails["igst"],$type).'</b></td>
                  </tr>';
                 
                     if($type='inr') {
                      
                        $html .='<tr class="yellowborder">
                     <td class="yellowborder"><b>Total(in Figure)</b></td>
                     <td class="yellowborder"><b>'.formatCurrency($getRegDetails["total_amount"],$type).'</b></td>
                  </tr>';
                 
                     }
                     
                     $html .='<tr  class="yellowborder">
                     <td class="yellowborder"><b>Total(In Words)</b></td>
                     <td class="yellowborder"><b>'.ucwords(strtolower(getIndianCurrency($getRegDetails["total_amount"]))).'</b></td>
                  </tr>
               </table>';
               
echo $html;
