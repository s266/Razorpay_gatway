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
		// 		'total_amount'			=>	$_SESSION['final_amount1'],
		// 		'status'			    =>	'1'
		// );


         mysqli_query($GLOBALS["___mysqli_ston"], "update razorpay set razorpay_payment_id='".$attributes['razorpay_payment_id']."', razorpay_signature='".$attributes['razorpay_signature']."', status='1' where order_id='".$attributes['razorpay_order_id']."'");

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
            //     $total_amount = $_SESSION['final_amount1']-100;
            //     $delivery_charge='';
            // }else{
            //     $delivery_charge=100;
            //     $total_amount = $_SESSION['final_amount1'];
            //     $mode11='Home Delivery';
            // }

            $delivery_charge=$_SESSION['delivery_charge1'];
            $total_amount = $_SESSION['final_amount1'];
            $mode11='Home Delivery';
             
            $payment_type = $_GET['value'];
            $username = $_SESSION['SD_User_Name'];
            $id = $_SESSION['userpanel_user_id'];
            $f['user_id']=$id;
        
            $ewallet_table1= 'Online Payment';
          
            $bv_total = $_SESSION['bv_total1'];

   /*if($bv_total >= 30){   */
   
            $date=date("Y-m-d");

            $condition1 = " where (username='".$username."' || user_id='".$id."')";
            $args_sponsor1 = $mxDb->get_information('user_registration', 'user_id', $condition1, true, 'assoc');
            $paid_id1 = $args_sponsor1['user_id'];
            $invoice_no = $_SESSION['invoice_no'];
           $neproasas=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from amount_detail where invoice_no='$invoice_no'"));
           if($neproasas==0){

              // $bv_total = $_SESSION['bv_total1'];

                $detail = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from user_registration where user_id='$id'"));
                $match_wallet1 = $total_amount;
             
                $final_discount = 0;

                foreach ($_SESSION["cart_products"] as $cart_itm){
                        $product_qty = $cart_itm["quantity"];
                            $product_name = $cart_itm["product_name"];
                            $product_id = $cart_itm["product_id"];
                          $nepro=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from eshop_products where product_id='$product_id'"));       
                                //$product_price = $cart_itm["product_price"];
                                $product_price = $cart_itm["dp"];
                                $subtotal = ($product_price * $product_qty); //calculate Price x Qty
                                $gst_percent=$nepro["product_gst"];
                             $gst=$subtotal*$gst_percent/100;
                              //  $product_color = $cart_itm["product_color"];
                               $product_size = $cart_itm['product_size'];
                                $price = $cart_itm["price"];
                                  $product_bv = $nepro["bv"];
                               $product_rp = $nepro["rp"];
                                $total_bv = ($product_bv * $product_qty); //calculate PP x Qty
                                //$total_rp = ($product_rp * $product_qty); 
                                $total_rp = 0; 
                                $final_pp = ($final_pp + $total_bv);
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
                                $insert_array = array('invoice_no'=>$invoice_no,'product_name'=>$product_name,'p_color'=>$cart_itm['product_color'],'p_size'=>$cart_itm['product_size'],'p_image'=>$nepro['actual_image'],'user_id'=>$f['user_id'],'p_id'=>$product_id,'quantity'=>$product_qty,'net_price'=>$subtotal,'price'=>$product_price,'pay_mode'=>$ewallet_table1, 'pay_type'=>$ewallet_table1,'purchase_date'=>$date,'discount'=> $discount,'shipping'=>$delivery_charge,'pv'=>$total_bv,'bv'=>$total_bv,'rp'=>$total_rp,'gp'=>$total_gp, 'dp'=>$product_price,'basic_dp'=>$basic_dp,'product_type'=>$cart_itm["product_type"],'gst'=>$gst,'gst_percent'=>$gst_percent,'ttype'=>$_SESSION['purchase_typess'],'buy_type'=>$_SESSION['purchase_typess']);
                                $mxDb->insert_record('eshop_purchase_detail', $insert_array);
                                $product_quantity = $nepro['qty'];
                                $now_qty=$product_quantity-$product_qty;
                                $qur_product = mysqli_query($GLOBALS["___mysqli_ston"], "update eshop_products set qty='$now_qty' where  product_id='$product_id'");
                        }
                        
                        
                        $insert_array1= array('invoice_no'=>$invoice_no,'user_id'=>$f['user_id'],'net_amount'=>$total_amount,'payment_mode'=>$ewallet_table1, 'purchase_type'=>'1','total_amount'=>$total_amount, 'payment_date'=>$date, 'status'=>'0', 'purchase_date'=>$date, 'date'=>$date,'dp'=>$total_dp1,'mrp'=>$_SESSION['mrp_amount1'],'pp'=>$final_pp,'discount'=>$final_discount,'pin_no'=>$pincodes,'puc'=>$pucself,'delivery_charge'=>$_SESSION['delivery_charge1'],'delivery_type'=>$_SESSION['shiptypes'],'ttype'=>$_SESSION['purchase_typess'],'buy_type'=>$_SESSION['purchase_typess'],'voucher'=>$_SESSION['holiday_voucher']); 
                        $mxDb->insert_record('amount_detail', $insert_array1);
                        
                        $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set self_purchase=(self_purchase+$bv_total) where  user_id='".$f['user_id']."'");
                        
                        mysqli_query($GLOBALS["___mysqli_ston"], "insert into self_pv_history values(NULL,'".$f['user_id']."','$bv_total','Package Purchase Amount','$date',CURRENT_TIMESTAMP,'0','$bv_total','0')");
                       
                        $monthlyts=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(pp) as total5 from amount_detail where user_id='".$_SESSION['userpanel_user_id']."'"));
                        $oldpurchase =$monthlyts['total5'];
                        $totalpurchase=$oldpurchase;

                        $details = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select first_buy_status from user_registration where user_id='".$f['user_id']."'"));
                        $first_buy_status=$details['first_buy_status'];
                        $fully_active=$details['fully_active'];
                 
                        if($totalpurchase>=100 && $first_buy_status!='1'){
                            $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set first_buy_status=1,activation_date='$date',user_rank_name='Paid Member',designation='Paid Member',user_plan='1' where  user_id='".$f['user_id']."'");
                        }
                 
                       if($totalpurchase>=200 && $fully_active!='1'){
                           $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set fully_active='1',fully_active_date='$date' where user_id='".$f['user_id']."'");
                       // $user_status = mysqli_query($GLOBALS["___mysqli_ston"], "update user_registration set first_buy_status=1,repurchase_start=1,capping='200000' where user_id='".$f['user_id']."'");
                       }
                
                
                    $upliners=mysqli_query($GLOBALS["___mysqli_ston"], "select * from level_income_binary where down_id='".$f['user_id']."'");
                    while($upline=mysqli_fetch_array($upliners))
                    {
                        $income_id=$upline['income_id'];
                    
                        $position=$upline['leg'];
                    
                        $user_level=$upline['level'];
                    
                        mysqli_query($GLOBALS["___mysqli_ston"], "insert into manage_bv_history values(NULL,'$income_id','".$f['user_id']."','$user_level','$bv_total','$position','Package Purchase Amount','$date',CURRENT_TIMESTAMP,'0','$bv_total','0')");
                    
                    }  
                

                        $total_amount = $_SESSION['final_amount1'];

                        $user_id=$f["user_id"];
   
                        mysqli_query($GLOBALS["___mysqli_ston"], "insert into credit_debit values(NULL,'$invoice_no','$user_id','0','$total_amount','0','0','$user_id','Admin','$date','Product Purchase','Product Purchase','Product Purchase','Product Purchase','$invoice_no','Product Purchase','0','$ewallet_table1',CURRENT_TIMESTAMP,'$urls')");

                         
                         
                         
               
                         $amount=$total_amount;
                         
                         
      commission_of_level($user_id,$total_amount,$invoice_no);
     self_of_level($user_id,$total_amount,$invoice_no);   
                         

                         $uname = $_SESSION['name'];

                         $invoiceno = $_SESSION['invoice_no'];

                         $recipient=$_SESSION['mobile'];


                       unset($_SESSION['product_tty']);

                        unset($_SESSION['cart_products']);

                         unset($_SESSION['dp_amount1']);

                          unset($_SESSION['mrp_amount1']);

                           unset($_SESSION['bv_total1']);

                            unset($_SESSION['final_amount1']);

                           unset($_SESSION['delivery_charge1']);
                           unset($_SESSION['gst1']);
                            unset($_SESSION['shiptypes']);
                            unset($_SESSION['holiday_active']);
                            unset($_SESSION['holiday_voucher']);
                           
                           header('location:../dashboard/eshop-invoice-details.php?inv='.$invoice_no);

  }              


   /*}else{



       $url = 'location:checkout1.php?msg=Minimum 30PV to proceed checkout';

        if(isset($_POST['last_url']) && $_POST['last_url'] != ''){

            $url = 'location:'.$_POST['last_url'].'?msg=All fields are mandatory';

        }

      header($url);



     }*/

     
         
	//	}
}
else
{
  //   	$insert_array = array(
		// 		'invoice_no'			=>	$_SESSION['invoice_no'],
		// 		'user_id'				=>	$_SESSION['userpanel_user_id'], 
		// 		'order_id'			    =>	$attributes['razorpay_order_id'],
		// 		'razorpay_payment_id'	=>	$attributes['razorpay_payment_id'], 
		// 		'razorpay_signature'	=>	$attributes['razorpay_signature'],
		// 		'total_amount'			=>	$_SESSION['final_amount1'],
		// 		'status'			    =>	'2'
		// );

		// if($mxDb->insert_record('razorpay', $insert_array)){
  //   	    header('location:../checkout.php');
          
		// }

     mysqli_query($GLOBALS["___mysqli_ston"], "update razorpay set razorpay_payment_id='".$attributes['razorpay_payment_id']."', razorpay_signature='".$attributes['razorpay_signature']."', status='2' where  order_id='".$attributes['razorpay_order_id']."'");

         header('location:../checkout.php?msg=Your payment failed');
}



function commission_of_level($user_id,$amount,$invoice_no)
{
	  $date=date('Y-m-d');
	  $user=$user_id;

      $select_upl=mysqli_query($GLOBALS["___mysqli_ston"], "select * from matrix_downline where down_id='$user_id' and level<=7");
      while($select_upl1=mysqli_fetch_array($select_upl))
      {
         $upid=$select_upl1['income_id'];
         $level=$select_upl1['level'];
         
         
         $comm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from user_registration where user_id='$upid'"));
         $self_purchase_dp=$comm['self_purchase_dp'];
         
         $levcom=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select level_comm from set_level where level='$level'"));
            
        if($level==1 && $self_purchase_dp>10000){      
         $spc=$levcom['level_comm'];
        }else if($level==2 && $self_purchase_dp>20000){      
         $spc=$levcom['level_comm'];
        }else if($level==3 && $self_purchase_dp>30000){      
         $spc=$levcom['level_comm'];
        }else{
            $spc=0;
        }
            $withdrawal_commission=$amount*$spc/100;
            
            //$admin=$withdrawal_commission*5/100;
            //$tds = $withdrawal_commission*5/100;
            //$deduct=$admin+$tds;
            
            $admin=0;
            $tds = 0;
            $deduct=$admin+$tds;
            
            $amount1 = $withdrawal_commission-$deduct;
            $rwallet=$amount1;
            //$invoice_no=$upid.rand(10000,99999);
        
                if($rwallet!='' && $rwallet>0 && $spc!='' && $spc>0 && $comm['user_rank_name']!='Free User')
                {
                    mysqli_query($GLOBALS["___mysqli_ston"], "update final_e_wallet set amount=(amount+$rwallet) where user_id='".$upid."'");
                    $urls="https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                    mysqli_query($GLOBALS["___mysqli_ston"], "insert into credit_debit values(NULL,'$invoice_no','$upid','$rwallet','0','$admin','$tds','$upid','$user','$date','Level Income','Earn Level Income from $user for $amount Package','$amount','$level','$invoice_no','$spc','1','Income Wallet',CURRENT_TIMESTAMP,'$urls')");    
                }
                
    }

} 

function self_of_level($user_id,$amount,$invoice_no)
{
	  $date=date('Y-m-d');
	  $upid=$user_id;

$levcom=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select delivery_charge from amount_detail where invoice_no='$invoice_no'"));
$delivery_charge=$levcom['delivery_charge'];
$spc=0;
$upliners=mysqli_query($GLOBALS["___mysqli_ston"], "select net_price,self_com from eshop_purchase_detail where invoice_no='$invoice_no'");
	while($upline=mysqli_fetch_array($upliners))
	{
		$net_price=$upline['net_price']+$delivery_charge;
		$self_com=$upline['self_com'];
		$total=$net_price*$self_com/100;
		
        $spc=$spc+$total;
	}
            $withdrawal_commission=$spc;
            
            //$admin=$withdrawal_commission*5/100;
            //$tds = $withdrawal_commission*5/100;
            //$deduct=$admin+$tds;
            
            $admin=0;
            $tds = 0;
            $deduct=$admin+$tds;
            
            
            $amount1 = $withdrawal_commission-$deduct;
            $rwallet=$amount1;
            //$invoice_no=$upid.rand(10000,99999);
        
                if($rwallet!='' && $rwallet>0 && $comm['user_rank_name']!='Free User')
                {
                    mysqli_query($GLOBALS["___mysqli_ston"], "update final_e_wallet set amount=(amount+$rwallet) where user_id='".$upid."'");
                    $urls="https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                    mysqli_query($GLOBALS["___mysqli_ston"], "insert into credit_debit values(NULL,'$invoice_no','$upid','$rwallet','0','$admin','$tds','$upid','123456','$date','Self Income','$rank','$amount','$level','$invoice_no','$spc','1','Income Wallet',CURRENT_TIMESTAMP,'$urls')");    
                }
                

} 


echo $html;
