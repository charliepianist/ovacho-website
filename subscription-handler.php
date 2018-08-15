<?php /*Template Name: Subscription Handler **Backend** */ ?>
<?php

if(isset($_POST['x_cust_id'])) {
	//store info
	add_transaction($_POST['x_cust_id'], get_var_dump($_POST));
}

//success
if(isset($_POST) && isset($_POST['x_response_code']) && $_POST['x_response_code'] == '1' && $_POST['x_login'] === PAYEEZY_LOGIN) {
    
	$amount = number_format($_POST['x_amount'], 2);
	//check if hash is valid
	if($_POST['x_MD5_Hash'] === md5(RELAY_RESPONSE_KEY . PAYEEZY_LOGIN . $_POST['x_trans_id'] . $_POST['x_amount'])) {

		$cust_id = $_POST['x_cust_id'];
		//check if bank approval
		if(isset($_POST['Transaction_Approved'])) {
    		//TRANSACTION SUCCESSFUL
			//determine which subscription
			switch($_POST['x_description']) {
				case 'Classic Subscription':
					$normal_amount = DEFAULT_SUBSCRIPTION_AMOUNT;
					if(firstTwoMonths() && get_user_meta($cust_id, 'discount', true) == 'true') {
						$normal_amount = '15.00';
					}
					//referred
    				if(referred_first_time($cust_id)) {
    					$normal_amount -= 5;
						new_referred_user_subscription($cust_id);
    				}

    				//referrer
    				if($amount < $normal_amount) {
    					$difference = $normal_amount - $amount;
    					add_referral_credit($cust_id, -1 * $difference);
    				}

    				//customer info
    				update_user_meta($cust_id, 'email', $_POST['Client_Email']);
    				update_user_meta($cust_id, 'card_type', $_POST['TransactionCardType']); // should be card
    				
    				//subscription info
    				subscribe_user($cust_id, $_POST['x_reference_3']); //defined in functions.php

    				if(isset($_POST['Card_Number'])) {
    					//if user used card, then store token data for future charges
    					update_user_meta($cust_id, 'token', $_POST['Card_Number']);
    					update_user_meta($cust_id, 'cardholder_name', $_POST['CardHoldersName']);
    					//update_user_meta($cust_id, 'monthly_amount', $amount); DETERMINED IN CLASSIC-FORM.PHP
    					update_user_meta($cust_id, 'expiry_date', $_POST['Expiry_Date']);
    					if($_POST['TransactionCardType'] == 'PayPal') update_user_meta($cust_id, 'subscription_active', 'false');
    				}
				break;
			}
		}else {
			//store POST data for reference
			update_user_meta($cust_id, 'last_purchase', $get_var_dump($_POST));
		}
	}
}
?>