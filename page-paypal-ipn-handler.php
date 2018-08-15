<?php /*Template Name: Subscription PayPal IPN Handler **Backend** */ ?>
<?php
//NOTE: ONLY PROCESSES PAYMENTS THAT ARE $20.00, OTHER ONES IGNORED

//
//===========Acknowledge Receipt of Notification===========
//

//200 OK
header("HTTP/1.1 200 OK");

//
//================Send Response to PayPal==================
//

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
        if ($keyval[0] === 'payment_date') {
            if (substr_count($keyval[1], '+') === 1) {
                $keyval[1] = str_replace('+', '%2B', $keyval[1]);
            }
        }
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}
// Build the body of the verification post request, adding the _notify-validate command.
$req = 'cmd=_notify-validate';
$get_magic_quotes_exists = false;
if (function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
    if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
    } else {
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}

// Step 2: POST IPN data back to PayPal to validate
$url = PAYPAL_IPN_URL;
if($_POST['test_ipn'] === '1') $url = PAYPAL_SANDBOX_IPN_URL;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSLVERSION, 6);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: PHP-IPN-VerificationScript'));
curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
$paypal_response = curl_exec($ch);
curl_close($ch);

//
//================Process Data as Appropriate================
//

if($paypal_response === 'VERIFIED' && $_POST['receiver_email'] === PAYPAL_PRIMARY_EMAIL && $_POST['test_ipn'] !== '1' && $_POST['item_name'] === 'Classic Subscription') {
	//store variables
	$txn_type = $_POST['txn_type'];
	$custom = $_POST['custom']; //"<userID>,<discordID>"
	$custom_arr = explode(',', $_POST['custom']);
	$cust_id = $custom_arr[0];
	$discord_id = $custom_arr[1];

	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$txn_id = $_POST['txn_id'];
	$payer_email = $_POST['payer_email'];
	$mc_gross = $_POST['mc_gross'];

	switch($txn_type) {
		case 'subscr_signup':
		//user begins subscription
		subscribe_user($cust_id, $discord_id, true);
		update_user_meta($cust_id, 'paypal_subscription_start', time());
		update_user_meta($cust_id, 'paypal_email', $payer_email);
		//if referred
		if(referred_first_time($cust_id)) new_referred_user_subscription($cust_id);
		break;

		case 'subscr_cancel':
		//user cancels subscription on PayPal website
		update_user_meta($cust_id, 'subscription_active', 'false');
		//email
		send_email(get_user_email($cust_id, true), 'Notice of Subscription Cancellation', '<p>We noticed you canceled your subscription; we\'re sorry to have you leaving us! This email is a confirmation that your subscription was canceled.</p><br>' . EMAIL_LINKS_HTML);
		break;

		case 'subscr_eot':
		//end of term (whether from cancel, payment failure, etc)
		revoke_subscription($cust_id);
		//email
		send_email(get_user_email($cust_id, true), 'Notice of Subscription End', '<p>We noticed that you canceled your subscription. We\'re sorry to have you leaving us! This email is a reminder that your subscription period has ended. <a href="' . site_url('pricing') .'">Renew your subscription?</a></p><br>' . EMAIL_LINKS_HTML);
		break;

		case 'subscr_payment':
		//user payment received (including initial payment)
		if($payment_status === 'Completed' &&get_paypal_transaction_status($cust_id, $txn_id) !== 'Completed' && $mc_gross === '20.00') {
			//store transaction in user meta
			$trans_array = array(
				'payment_date' => $_POST['payment_date'],
				'subscr_id' => $_POST['subscr_id'], //subscriber id
				'first_name' => $_POST['first_name'],
				'last_name' => $_POST['last_name'],
				'residence_country' => $_POST['residence_country'],
				'item_name' => $_POST['item_name'], //Classic Subscription
				'payment_type' => $_POST['payment_type'],
				'txn_id' => $_POST['txn_id'],
				'payer_id' => $_POST['payer_id'],
				'receiver_id' => $_POST['receiver_id'],
				'payment_status' => $_POST['payment_status'], //Completed, etc.
				'mc_fee' => $_POST['mc_fee'], //fee from paypal
				'ipn_track_id' => $_POST['ipn_track_id'],
			);
			add_transaction($cust_id, $trans_array);

			//store end time and reset for three day email
			update_user_meta($cust_id, 'subscription_end_time', strtotime('+1 month'));
			update_user_meta($cust_id, 'three_day_email_sent', 'false');

			//store that this transaction has been processed
			update_paypal_transaction_status($cust_id, $txn_id, $payment_status);
		}


		break;
	}
}

if(!file_exists('stats')) mkdir('stats');
$file = 'stats/paypal-log.txt';
file_put_contents($file, date('m/d/y') . ":\r\n" . get_var_dump($_POST) . "\r\n\r\n==========================\r\n\r\n", FILE_APPEND);

/*
ENABLING OF SUBSCRIPTION
{
  ["txn_type"]=>
  string(13) "subscr_signup"
  ["subscr_id"]=>
  string(14) "I-A5VRPFPXDTJK"
  ["last_name"]=>
  string(5) "buyer"
  ["residence_country"]=>
  string(2) "US"
  ["mc_currency"]=>
  string(3) "USD"
  ["item_name"]=>
  string(20) "Classic Subscription"
  ["business"]=>
  string(39) "ovachoinvestments-facilitator@gmail.com"
  ["amount3"]=>
  string(5) "20.00"
  ["recurring"]=>
  string(1) "1"
  ["verify_sign"]=>
  string(56) "AI3CO7S8cQRVoojPm3x8gMjc9tc4AtEdY1.OsWAcegV9kc0ekubFqpD6"
  ["payer_status"]=>
  string(8) "verified"
  ["test_ipn"]=>
  string(1) "1"
  ["payer_email"]=>
  string(33) "ovachoinvestments-buyer@gmail.com"
  ["first_name"]=>
  string(4) "test"
  ["receiver_email"]=>
  string(39) "ovachoinvestments-facilitator@gmail.com"
  ["payer_id"]=>
  string(13) "HU8BLWYM8KSAG"
  ["reattempt"]=>
  string(1) "1"
  ["item_number"]=>
  string(20) "sWhm55PWw6BrHv7QB2Qq"
  ["subscr_date"]=>
  string(25) "13:13:46 Aug 11, 2018 PDT"
  ["btn_id"]=>
  string(7) "3888959"
  ["custom"]=>
  string(1) "2"
  ["charset"]=>
  string(12) "windows-1252"
  ["notify_version"]=>
  string(3) "3.9"
  ["period3"]=>
  string(3) "1 M"
  ["mc_amount3"]=>
  string(5) "20.00"
  ["ipn_track_id"]=>
  string(13) "7061872e29991"
}




CANCEL SUBSCRIPTION
{
    [0]=>
    string(2635) "array(25) {
  ["txn_type"]=>
  string(13) "subscr_cancel"
  ["subscr_id"]=>
  string(14) "I-A5VRPFPXDTJK"
  ["last_name"]=>
  string(5) "buyer"
  ["residence_country"]=>
  string(2) "US"
  ["mc_currency"]=>
  string(3) "USD"
  ["item_name"]=>
  string(20) "Classic Subscription"
  ["business"]=>
  string(39) "ovachoinvestments-facilitator@gmail.com"
  ["amount3"]=>
  string(5) "20.00"
  ["recurring"]=>
  string(1) "1"
  ["verify_sign"]=>
  string(56) "A6w61B8gjO63nr-T5mOATWyRGIWgADKgtqp.nFK2ruE6xeQWHz6gKk2O"
  ["payer_status"]=>
  string(8) "verified"
  ["test_ipn"]=>
  string(1) "1"
  ["payer_email"]=>
  string(33) "ovachoinvestments-buyer@gmail.com"
  ["first_name"]=>
  string(4) "test"
  ["receiver_email"]=>
  string(39) "ovachoinvestments-facilitator@gmail.com"
  ["payer_id"]=>
  string(13) "HU8BLWYM8KSAG"
  ["reattempt"]=>
  string(1) "1"
  ["item_number"]=>
  string(20) "sWhm55PWw6BrHv7QB2Qq"
  ["subscr_date"]=>
  string(25) "13:20:27 Aug 11, 2018 PDT"
  ["custom"]=>
  string(1) "2"
  ["charset"]=>
  string(12) "windows-1252"
  ["notify_version"]=>
  string(3) "3.9"
  ["period3"]=>
  string(3) "1 M"
  ["mc_amount3"]=>
  string(5) "20.00"
  ["ipn_track_id"]=>
  string(13) "4508b1bcba2d6"
}

PAYMENT FOR SUBSCRIPTION
{
  ["transaction_subject"]=>
  string(20) "Classic Subscription"
  ["payment_date"]=>
  string(25) "15:29:01 Aug 12, 2018 PDT"
  ["txn_type"]=>
  string(14) "subscr_payment"
  ["subscr_id"]=>
  string(14) "I-K8PVFVN272N6"
  ["last_name"]=>
  string(5) "buyer"
  ["residence_country"]=>
  string(2) "US"
  ["item_name"]=>
  string(20) "Classic Subscription"
  ["payment_gross"]=>
  string(5) "20.00"
  ["mc_currency"]=>
  string(3) "USD"
  ["business"]=>
  string(39) "ovachoinvestments-facilitator@gmail.com"
  ["payment_type"]=>
  string(7) "instant"
  ["protection_eligibility"]=>
  string(8) "Eligible"
  ["verify_sign"]=>
  string(56) "Au9qn.cvxroAtpaktellmBY5HX9HATo61Fv4cEAo1KnqPaBQCJ1YjRrZ"
  ["payer_status"]=>
  string(8) "verified"
  ["test_ipn"]=>
  string(1) "1"
  ["payer_email"]=>
  string(33) "ovachoinvestments-buyer@gmail.com"
  ["txn_id"]=>
  string(17) "24D00651RU9210919"
  ["receiver_email"]=>
  string(39) "ovachoinvestments-facilitator@gmail.com"
  ["first_name"]=>
  string(4) "test"
  ["payer_id"]=>
  string(13) "HU8BLWYM8KSAG"
  ["receiver_id"]=>
  string(13) "B63LKCVYZT3V2"
  ["item_number"]=>
  string(20) "sWhm55PWw6BrHv7QB2Qq"
  ["payment_status"]=>
  string(9) "Completed"
  ["payment_fee"]=>
  string(4) "0.88"
  ["mc_fee"]=>
  string(4) "0.88"
  ["btn_id"]=>
  string(7) "3888959"
  ["mc_gross"]=>
  string(5) "20.00"
  ["custom"]=>
  string(3) "1,0"
  ["charset"]=>
  string(12) "windows-1252"
  ["notify_version"]=>
  string(3) "3.9"
  ["ipn_track_id"]=>
  string(12) "3b3d7ca539ff"
}
*/

?>