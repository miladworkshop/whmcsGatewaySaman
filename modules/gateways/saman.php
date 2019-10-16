<?php
/*
	author 	: Milad Maldar
	URL		: https://miladworkshop.ir
*/

function saman_config(){
    $configarray = array(
		"FriendlyName" 			=> array("Type" => "System", "Value"=>"ماژول درگاه بانک سامان"),
		"webgate_id" 			=> array("FriendlyName" => "کد پذيرنده", "Type" => "text", "Size" => "50", ),
		"webgate_pw" 			=> array("FriendlyName" => "رمز پذيرنده", "Type" => "text", "Size" => "50", ),
		"Currencies" 			=> array("FriendlyName" => "واحد پول سیستم", "Type" => "dropdown", "Options" => "rial,toman", "Description" => "لطفا واحد پول سیستم خود را انتخاب کنید.",),
		"send_telegram_ok" 		=> array("FriendlyName" => "اطلاع از تراکنش های موفق", "Type" => "dropdown", "Options" => "No,Yes", "Description" => "ارسال گزارش تراکنش های مالی موفق این درگاه از طریق تلگرام",),
		"send_telegram_error" 	=> array("FriendlyName" => "ارسال هشدار تراکنش های ناموفق و خطاها", "Type" => "dropdown", "Options" => "No,Yes", "Description" => "ارسال گزارش تراکنش های ناموفق و خطاهای این درگاه از طریق تلگرام",),
		"telegram_chatid" 		=> array("FriendlyName" => "Chat ID تلگرام", "Type" => "text", "Description" => "چت آی دی تلگرام خود را وارد کنید", ),
		"author" 				=> array("FriendlyName" => "برنامه نویس", "Type" => "", "Description" => "طراحی و برنامه نویسی شده توسط <a href='https://miladworkshop.ir' target='_blank' style='color:#FF0000'>میلاد مالدار</a>", ),
    );
	return $configarray;
}

function saman_link($params)
{
    $currencies = $params['Currencies'];
    $invoiceid 	= $params['invoiceid'];
    $amount 	= $params['amount'];
    $email 		= $params['clientdetails']['email'];

	$amount 	= $params['amount']-'.00';

	if($params['Currencies'] == 'toman')
	{
		$amount = round($amount*10);
	}

	$code = '<form method="post" action="modules/gateways/saman/pay.php">
	<input type="hidden" name="invoiceid" value="'. $invoiceid .'" />
	<input type="hidden" name="amount" value="'. $amount .'" />
	<input type="hidden" name="email" value="'. $email .'" />
	<input type="submit" name="pay" value=" پرداخت " /></form>';

	return $code;
}
?>