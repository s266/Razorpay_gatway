<?php
require('../lib/config.php');
require('config.php');

//session_start();

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";
//echo "<pre>"; print_r($_POST); //die;
if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_POST['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}


// print_r($_SESSION);die();

// die;
global $mxDb;
if ($success === true)
{
    	
  //   	$insert_array = array(
		// 		'invoice_no'			=>	$_SESSION['invoice_no'],
		// 		'user_id'				=>	$_SESSION['userpanel_user_id'], 
		// 		'order_id'			    =>	$attributes['razorpay_order_id'],
		// 		'razorpay_payment_id'	=>	$attributes['razorpay_payment_id'], 
		// 		'razorpay_signature'	=>	$attributes['razorpay_signature'],
		// 		'total_amount'			=>	$_SESSION['final_amount'],
		// 		'status'			    =>	'1'
		// );


            mysqli_query($GLOBALS["___mysqli_ston"], "update razorpay set razorpay_payment_id='".$attributes['razorpay_payment_id']."', razorpay_signature='".$attributes['razorpay_signature']."', status='1' where  order_id='".$attributes['razorpay_order_id']."'");


//if($mxDb->insert_record('razorpay', $insert_array)){
		    //===================================================
		    $val = $_SESSION['paymentmethod'];
		    $pucself = $_SESSION['puc_self'];
		    $puc = $_SESSION['puc_self'];
		//if($val=='bank'){
        $date=date("Y-m-d");
        //if($_SESSION['paymentmethod1']!=''){
            // if($_SESSION['paymentmethod1']=='self_pickup_online'){
            //     $mode11='Self Pickup';
            //     $total_amount = $_SESSION['dp_amount']-100;
            //     $delivery_charge='';
            // }else{
            //     $delivery_charge=100;
            //     $total_amount = $_SESSION['dp_amount'];
            //     $mode11='Home Delivery';
            // }
             
            $delivery_charge=$_SESSION['delivery_charge'];
            $total_amount = $_SESSION['final_amount'];
            $mode11='Home Delivery';


           // $payment_type = $_GET['value'];
            $username = $_SESSION['SD_User_Name'];
            $id = $_SESSION['userpanel_user_id'];
            $f['user_id']=$id;
        
          
            $ewallet_table1= 'Online Payment';
          
          //  $condition1 = " where (username='".$username."' || user_id='".$id."')";
          //  $args_sponsor1 = $mxDb->get_information('user_registration', 'user_id', $condition1, true, 'assoc');
          //  $paid_id1 = $args_sponsor1['user_id'];
               $invoice_no = $_SESSION['invoice_no'];
           $neproasas=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from amount_detail where invoice_no='$invoice_no'"));
           if($neproasas==0){

               $bv_total = $_SESSION['rp'];

                $detail = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from user_registration where user_id='$id'"));
                $match_wallet1 = $total_amount;
             
                $final_discount = 0;


                foreach ($_SESSION["cart_eshop_products"] as $cart_itm){
                            $product_qty = $cart_itm["quantity"];
                            $product_name = $cart_itm["product_name"];
                            $product_id = $cart_itm["product_id"];
                            
                      $nepro=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from eshop_products where product_id='$product_id'"));       
                                $product_price = $cart_itm["product_price"];
                                $subtotal = ($product_price * $product_qty); //calculate Price x Qty
                                $gst_percent=$nepro["product_gst"];
                                $gst=$subtotal*$gst_percent/100;
                              //  $product_color = $cart_itm["product_color"];
                              $product_size = $cart_itm['product_size'];
                                $price = $cart_itm["price"];
                                $product_bv = 0;
                                $product_rp = $nepro["rp"];
                                $total_bv = ($product_bv * $product_qty); //calculate PP x Qty
                                $total_rp = ($product_rp * $product_qty); 
                                $final_pp = ($final_pp + $total_rp);
                            //    $gp = $nepro["gp"];
                             //   $total_gp = ($gp * $product_qty); //calculate GP x Qty
                             //   $final_gp = ($final_gp + $total_gp);
                                $basic_dp = $nepro["dp"];
                                $total_dp = ($basic_dp * $product_qty); //calculate Price x Qty
                                $total_dp1=$total_dp1+$total_dp;
                                $discount = 0;
                                // if(isset($f['user_id']) && $f['user_rank_name'] == 'Affiliate' && $cart_itm['discount'] > 0){
                                //     $discount = ($subtotal * $cart_itm['discount'] / 100);
                                // }
                                $final_discount = $final_discount + $discount;
                                $insert_array = array('invoice_no'=>$invoice_no,'product_name'=>$product_name,'p_color'=>$cart_itm['product_color'],'p_size'=>$cart_itm['product_size'],'p_image'=>$nepro['actual_image'],'user_id'=>$f['user_id'],'p_id'=>$product_id,'quantity'=>$product_qty,'net_price'=>$subtotal,'price'=>$product_price,'pay_mode'=>$ewallet_table1, 'pay_type'=>$ewallet_table1,'purchase_date'=>$date,'discount'=> $discount,'shipping'=>$delivery_charge,'pv'=>$total_rp,'bv'=>$total_bv,'rp'=>$total_rp, 'gp'=>$total_gp, 'dp'=>$product_price,'basic_dp'=>$basic_dp,'product_type'=>$cart_itm["product_type"],'gst'=>$gst, 'gst_percent'=>$gst_percent,'ttype'=>$_SESSION['purchase_typess'],'buy_type'=>$_SESSION['purchase_typess']);
                                $mxDb->insert_record('eshop_purchase_detail', $insert_array);
                                $product_quantity = $nepro['qty'];
                                $now_qty=$product_quantity-$product_qty;
                                $qur_product = mysqli_query($GLOBALS["___mysqli_ston"], "update eshop_products set qty='$now_qty' where  product_id='$product_id'");
                        }
                        
                
                        $insert_array1= array('invoice_no'=>$invoice_no,'user_id'=>$f['user_id'],'net_amount'=>$total_amount,'payment_mode'=>$ewallet_table1, 'purchase_type'=>'1','total_amount'=>$total_amount, 'payment_date'=>$date, 'status'=>'0', 'purchase_date'=>$date, 'date'=>$date,'dp'=>$total_dp1,'mrp'=>$_SESSION['mrp_amount'],'pp'=>$bv_total,'discount'=>$final_discount,'pin_no'=>$pincodes,'puc'=>$puc,'delivery_charge'=>$_SESSION['delivery_charge'],'delivery_type'=>$_SESSION['shiptype1'],'ttype'=>$_SESSION['purchase_typess'],'buy_type'=>$_SESSION['purchase_typess'],'voucher'=>$_SESSION['holiday_voucher1']); 
                        $mxDb->insert_record('amount_detail', $insert_array1);
                        
                    // update buying status
                
    $income_id1=$f['user_id'];
    $position1='right';
    $user_level1='0';
    
    $udata=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select first_buy_status,repurchase_status from user_registration where user_id='$income_id1'"));
    $first_buy_status  = $udata['first_buy_status'];
    $rep_buy_status  = $udata['repurchase_status'];
    
   // mysqli_query($GLOBALS["___mysqli_ston"], "insert into manage_bv_history_repurchase values(NULL,'$income_id1','".$f['user_id']."','$user_level1','$bv_total','$position1','Repurchase Purchase Amount','$date',CURRENT_TIMESTAMP,'0','$bv_total','0')");
    
    $upliners=mysqli_query($GLOBALS["___mysqli_ston"], "select * from level_income_binary where down_id='".$f['user_id']."'");
    while($upline=mysqli_fetch_array($upliners))
    {
        $income_id=$upline['income_id'];
        $position=$upline['leg'];
        $user_level=$upline['level'];
        
        mysqli_query($GLOBALS["___mysqli_ston"], "insert into manage_bv_history_repurchase values(NULL,'$income_id','".$f['user_id']."','$user_level','$bv_total','$position','Repurchase Amount','$date',CURRENT_TIMESTAMP,'0','$bv_total','0')");
    } 
        
  
   // if($first_buy_status==1){
   // $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_start=1,self_repurchase_bv=(self_repurchase_bv+$bv_total) where user_id='".$f['user_id']."'");
   // }
   
    $user_rank = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select rank from user_registration where user_id='".$f['user_id']."'"));
    $rank=$user_rank['rank'];
   
    $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_start=1,self_repurchase_bv=(self_repurchase_bv+$bv_total) where user_id='".$f['user_id']."'");
     
     mysqli_query($GLOBALS["___mysqli_ston"], "insert into self_bv_history values(NULL,'".$f['user_id']."','$bv_total','Package Repurchase Amount','$date',CURRENT_TIMESTAMP,'0','$bv_total','1')");
    
   // if($rep_buy_status==0 && $rank<4 &&){
    
        //$user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_status=1 where user_id='".$f['user_id']."'");
    // }
     
     
     $currentMonthStartDate = date('Y-m-01');
     $currentMonthEndDate = date('Y-m-t');
     $current_month_bv=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(pair) as totalbv from self_bv_history where user_id='".$f['user_id']."' and date>='$currentMonthStartDate' and date<='$currentMonthEndDate'"));
     $current_month_bv1=$current_month_bv['totalbv'];
     
     
     if($rank==4){
      $target = 200;
    }elseif($rank==5){
      $target = 400;
    }elseif($rank==6){
      $target = 400;
    }elseif($rank==7){
      $target = 800;
    }elseif($rank==8){
      $target = 800;
    }elseif($rank==9){
      $target = 1500;
    }elseif($rank==10){
      $target = 1500;
    }elseif($rank==11){
      $target = 2500;
    }elseif($rank==12){
      $target = 2500;
    }elseif($rank==13){
      $target = 3000;
    }elseif($rank==14){
      $target = 3000;
    }elseif($rank==15){
      $target = 3500;
    }elseif($rank==16){
      $target = 3500;
    }else{
      $target=0; 
    }
    
    
     /*if($rank>=4){
         
         if(!empty($current_month_bv1) && ($current_month_bv1>=$target)){
            $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_status=1,autoship_status=1 where user_id='".$f['user_id']."'");
         }else{
            $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_status=0,autoship_status=0 where user_id='".$f['user_id']."'");  
         }
    
     }*/
     
     
     /*if($rank<4){
    
       if(!empty($current_month_bv1) && ($current_month_bv1>=200)){
            $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_status=1 where user_id='".$f['user_id']."'");
         }else{
            $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set repurchase_status=0 where user_id='".$f['user_id']."'"); 
         }
    }*/
     
    
    $user_id=$f['user_id'];
   
    mysqli_query($GLOBALS["___mysqli_ston"], "insert into credit_debit values(NULL,'$invoice_no','$user_id','0','$total_amount','0','0','$user_id','Admin','$date','Repurchase Product Purchase','Repurchase Product Purchase','Repurchase Product Purchase','Repurchase Product Purchase','$invoice_no','Repurchase Product Purchase','0','$ewallet_table1',CURRENT_TIMESTAMP,'$urls')");
 

            $amount=$total_amount;
            $uname = $_SESSION['name'];
            $invoiceno = $_SESSION['invoice_no'];
            $recipient=$_SESSION['mobile'];
                     unset($_SESSION['product_tty']);

                        unset($_SESSION['cart_eshop_products']);

                         unset($_SESSION['dp_amount']);

                          unset($_SESSION['mrp_amount']);

                           unset($_SESSION['rp']);

                           unset($_SESSION['final_amount']);
                           
                            unset($_SESSION['purchase_typess']);

                           unset($_SESSION['delivery_charge']);  
                            unset($_SESSION['shiptypes']);  
                             unset($_SESSION['holiday_active']);
                           unset($_SESSION['holiday_voucher1']);

                header('location:../dashboard/eshop-invoice-details.php?inv='.$invoice_no);

  }        
       
         
	//	}  razorpay insert if
}
else
{
  //   	$insert_array = array(
		// 		'invoice_no'			=>	$_SESSION['invoice_no'],
		// 		'user_id'				=>	$_SESSION['userpanel_user_id'], 
		// 		'order_id'			    =>	$attributes['razorpay_order_id'],
		// 		'razorpay_payment_id'	=>	$attributes['razorpay_payment_id'], 
		// 		'razorpay_signature'	=>	$attributes['razorpay_signature'],
		// 		'total_amount'			=>	$_SESSION['final_amount'],
		// 		'status'			    =>	'2'
		// );

		// if($mxDb->insert_record('razorpay', $insert_array)){
  //   	    header('location:../checkout.php?msg=Your payment failed');
           
		// }


        mysqli_query($GLOBALS["___mysqli_ston"], "update razorpay set razorpay_payment_id='".$attributes['razorpay_payment_id']."', razorpay_signature='".$attributes['razorpay_signature']."', status='2' where  order_id='".$attributes['razorpay_order_id']."'");

         header('location:../checkout1.php?msg=Your payment failed');
}

echo $html;
