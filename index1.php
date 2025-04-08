<?php
require('../lib/config.php');
require('config.php');
require('razorpay-php/Razorpay.php');
//include('../lib/config.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

// $keyId ='rzp_live_glnAONgnMTuzjN';

// $keySecret ='1JXWxlNqpAcUEZsT5lraoVIr';

$keyId ='rzp_test_QwdCLJaiFkuAwU';

$keySecret ='GpuNbpKzt3p9XdZXENWmMDAg';

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//

$invoice=$_SESSION['invoice_no'];
$total_amount=$_SESSION['final_amount1'];
$username=$_SESSION['SD_User_Name'];
$email=$_SESSION['email'];
$mobile=$_SESSION['mobile'];

$_SESSION['invoice_no']=$invoice;




$orderData = [
    'receipt'         => $invoice,
    'amount'          => $total_amount, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];


$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

//$checkout = 'automatic';
$checkout = 'manual1';

/*if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
{
    $checkout = $_GET['checkout'];
}*/

$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => $username,
    "description"       => "Smart Shop.",
    "image"             => "https://www.scssindia.com/frontAssets/images/logo.png",
    "prefill"           => [
    "name"              => $username,
    "email"             => $email,
    "contact"           => $mobile,
    ],
    "notes"             => [
    "address"           => "",
    "merchant_order_id" => $razorpayOrderId,
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

// insert pre data into database

$insert_array = array(
                'invoice_no'            =>  $_SESSION['invoice_no'],
                'user_id'               =>  $_SESSION['userpanel_user_id'], 
                'order_id'              =>  $razorpayOrderId,
                'razorpay_payment_id'   =>  '', 
                'razorpay_signature'    =>  '',
                'total_amount'          =>  $_SESSION['final_amount1'],
                'status'                =>  '0'
        );

$mxDb->insert_record('razorpay', $insert_array);

// insert details into temp table for just tracking

 $val = $_SESSION['paymentmethod'];
     $pucself = $_SESSION['puc_self'];
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

            $delivery_charge= $_SESSION['delivery_charge1'];
            $total_amount = $_SESSION['final_amount1'];
            $mode11=$_SESSION['shiptypes'];
             
            $payment_type = $_GET['value'];
            $username = $_SESSION['SD_User_Name'];
            $id = $_SESSION['userpanel_user_id'];
            $f['user_id']=$id;
        
            $ewallet_table1= 'Online Payment';

            $bv_total = $_SESSION['bv_total1'];

        //if($bv_total >= 30){   
            $invoice_no = $_SESSION['invoice_no'];
            $neproasas=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from amount_detail_temp where invoice_no='$invoice_no'"));
            if($neproasas==0){
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
                          
                                $basic_dp = $nepro["dp"];
                                $total_dp = ($basic_dp * $product_qty); //calculate Price x Qty
                                $total_dp1=$total_dp1+$total_dp;
                                $discount = 0;
                                

                            $final_discount = $final_discount + $discount;
                                $insert_array = array('invoice_no'=>$invoice_no,'product_name'=>$product_name,'p_color'=>$cart_itm['product_color'],'p_size'=>$cart_itm['product_size'],'p_image'=>$nepro['actual_image'],'user_id'=>$f['user_id'],'p_id'=>$product_id,'quantity'=>$product_qty,'net_price'=>$subtotal,'price'=>$product_price,'pay_mode'=>$ewallet_table1, 'pay_type'=>$ewallet_table1,'purchase_date'=>$date,'discount'=> $discount,'shipping'=>$delivery_charge,'pv'=>$total_bv,'bv'=>$total_bv,'rp'=>$total_rp,'gp'=>$total_gp, 'dp'=>$product_price,'basic_dp'=>$basic_dp,'product_type'=>$cart_itm["product_type"],'gst'=>$gst,'gst_percent'=>$gst_percent,'ttype'=>$_SESSION['purchase_typess'],'buy_type'=>$_SESSION['purchase_typess']);
                                $mxDb->insert_record('eshop_purchase_detail_temp', $insert_array);
                               
                             
                        }
                
                        $insert_array1= array('invoice_no'=>$invoice_no,'user_id'=>$f['user_id'],'net_amount'=>$total_amount,'payment_mode'=>$ewallet_table1,'payment_mode2'=>$_SESSION['shiptypes'],'purchase_type'=>'1','total_amount'=>$total_amount, 'payment_date'=>$date, 'status'=>'0', 'purchase_date'=>$date, 'date'=>$date,'dp'=>$total_dp1,'mrp'=>$_SESSION['mrp_amount1'],'pp'=>$final_pp,'discount'=>$final_discount,'pin_no'=>$pincodes,'puc'=>$pucself,'delivery_charge'=>$_SESSION['delivery_charge1'],'delivery_type'=>$_SESSION['shiptypes'],'ttype'=>$_SESSION['purchase_typess'],'buy_type'=>$_SESSION['purchase_typess']); 
                        $mxDb->insert_record('amount_detail_temp', $insert_array1);
            }
        //}
//////

require("checkout/{$checkout}.php");

