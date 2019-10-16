<?php
/*
	author	: Milad Maldar
	URL		: https://miladworkshop.ir
*/

if(file_exists('../../../init.php')){require( '../../../init.php' );}else{require("../../../dbconnect.php");}
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule 		= 'saman';
$GATEWAY 			= getGatewayVariables($gatewaymodule);

if (!$GATEWAY['type']) die('Module Not Activated');

$whmcs_url			= $CONFIG['SystemURL'];
$invoiceid 			= $_GET['invoiceid'];
$order_id 			= $_POST['ResNum'];
$tran_id 			= $_POST['ResNum'];
$refcode			= $_POST['RefNum'];

if(!empty($invoiceid)){
	if(!empty($order_id) && !empty($tran_id) && !empty($refcode)){
		$invoiceid 	= checkCbInvoiceID($invoiceid, $GATEWAY['name']);
		
		$results = select_query( "tblinvoices", "", array( "id" => $invoiceid ) );
		$data = mysql_fetch_array($results);
		$db_amount = strtok($data['total'],'.');
		
		$soapclient = new soapclient('https://verify.sep.ir/Payments/ReferencePayment.asmx?WSDL');
		$result 	= $soapclient->VerifyTransaction($_POST['RefNum'], $GATEWAY['webgate_id']);
		
		$amount 	= $result;
		
		if($GATEWAY['Currencies'] == 'toman'){
			$amount = $result/10;
		}
		
		$cartNumber = $_POST['SecurePan'];

		if ($result > 0){
			
			if ($amount == $db_amount) {
				addInvoicePayment($invoiceid, $refcode, $amount, 0, $gatewaymodule);
				logTransaction($GATEWAY["name"], array(
					'invoiceid' 	=> $invoiceid,
					'order_id' 		=> $order_id,
					'amount' 		=> $amount ." ". $GATEWAY['Currencies'],
					'tran_id' 		=> $tran_id,
					'refcode' 		=> $refcode,
					'CardNumber'	=> $cartNumber,
					'status' 		=> "OK"
				), "موفق");
				
				if ($GATEWAY['send_telegram_ok'] == "Yes") {
					
					$pm = "یک تراکنش موفق در سیستم ثبت شد ( درگاه پرداخت سامان )
					----------------------------------------------------------------------------------------------\n
					
					Gateway : Saman
					
					Price : $amount $GATEWAY[Currencies]
					Ref Code : $refcode
					Order ID : $order_id
					Invoice ID : $invoiceid
					Customer Cart Number : $cartNumber";
					
					$chat_id 		= $GATEWAY['telegram_chatid'];
					$botToken 		= "291958747:AAF65_lFLaap35HS5zYxSbO1ycNb8Pl2vTk";
					$data = array('chat_id' => $chat_id, 'text' => $pm . "\n\n----------------------------------------------------------------------------------------------\n" . base64_decode("V0hNQ1MgVGVsZWdyYW0gTm90aWZpY2F0aW9uIEJ5IE1pbGFkLmlu"));
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot$botToken/sendMessage");
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					curl_setopt($curl, CURLOPT_TIMEOUT, 10);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_exec($curl);
					curl_close($curl);
				}
			} else {
				logTransaction($GATEWAY["name"] ,  array('invoiceid'=>$invoiceid,'order_id'=>$order_id,'amount'=>$amount,'tran_id'=>$tran_id,'status'=>$result), "ناموفق") ; 
			
				if ($GATEWAY['send_telegram_error'] == "Yes") {
					
					$pm = "گزارش تراکنش ناموفق / خطا ( درگاه پرداخت سامان )
					----------------------------------------------------------------------------------------------\n
					
					Gateway : Saman
					
					Pay Price : $amount $GATEWAY[Currencies]
					Invoice Price : $db_amount $GATEWAY[Currencies]
					Order ID : $order_id
					Invoice ID : $invoiceid
					
					Error Code : مبلغ پرداخت شده با مبلغ فاکتور یکسان نیست";
					
					$chat_id 		= $GATEWAY['telegram_chatid'];
					$botToken 		= "291958747:AAF65_lFLaap35HS5zYxSbO1ycNb8Pl2vTk";
					$data = array('chat_id' => $chat_id, 'text' => $pm . "\n\n----------------------------------------------------------------------------------------------\n" . base64_decode("V0hNQ1MgVGVsZWdyYW0gTm90aWZpY2F0aW9uIEJ5IE1pbGFkLmlu"));
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot$botToken/sendMessage");
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					curl_setopt($curl, CURLOPT_TIMEOUT, 10);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_exec($curl);
					curl_close($curl);
				}
			}

		} else {
			logTransaction($GATEWAY["name"] ,  array('invoiceid'=>$invoiceid,'order_id'=>$order_id,'amount'=>$amount,'tran_id'=>$tran_id,'status'=>$result), "ناموفق") ; 
		
			if ($GATEWAY['send_telegram_error'] == "Yes") {
				
				$pm = "گزارش تراکنش ناموفق / خطا ( درگاه پرداخت سامان )
				----------------------------------------------------------------------------------------------\n
				
				Gateway : Saman
				
				Price : $amount $GATEWAY[Currencies]
				Order ID : $order_id
				Invoice ID : $invoiceid
				
				Error Code : $result";
				
				$chat_id 		= $GATEWAY['telegram_chatid'];
				$botToken 		= "291958747:AAF65_lFLaap35HS5zYxSbO1ycNb8Pl2vTk";
				$data = array('chat_id' => $chat_id, 'text' => $pm . "\n\n----------------------------------------------------------------------------------------------\n" . base64_decode("V0hNQ1MgVGVsZWdyYW0gTm90aWZpY2F0aW9uIEJ5IE1pbGFkLmlu"));
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot$botToken/sendMessage");
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_exec($curl);
				curl_close($curl);
			}
		
		}
	}
	$action = $whmcs_url ."/viewinvoice.php?id=". $invoiceid;
	header('Location: '. $action);
} else {
	echo "invoice id is blank";
}
?>