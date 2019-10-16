<?php
/*
	author	: Milad Maldar
	URL		: https://miladworkshop.ir
*/

if(file_exists('../../../init.php')){require( '../../../init.php' );}else{require("../../../dbconnect.php");}
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule 	= 'saman';
$GATEWAY 		= getGatewayVariables($gatewaymodule);

if (!$GATEWAY['type']) die('Module Not Activated');

$amount 			= intval($_POST['amount']);
$invoiceid 			= $_POST['invoiceid']; 
$email 				= $_POST['email'];
$CallbackURL 		= $CONFIG['SystemURL'] .'/modules/gateways/saman/callback.php?invoiceid='. $invoiceid;
$payMerchantCode 	= $GATEWAY['webgate_id'];

echo "<form id='samanpeyment' action='https://sep.shaparak.ir/payment.aspx' method='post'>
<input type='hidden' name='Amount' value='$amount' />
<input type='hidden' name='ResNum' value='$invoiceid'>
<input type='hidden' name='RedirectURL' value='$CallbackURL'/>
<input type='hidden' name='MID' value='$payMerchantCode'/>
</form><script>document.forms['samanpeyment'].submit()</script>";
?>