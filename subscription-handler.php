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
						$ref_id = get_referrer_id($cust_id);
						//give referral credit
						$referral_credit = get_referral_credit($ref_id);
						add_referral_credit($ref_id, 5);
						$referral_credit += 5;

						//email
						$links_html = '<p style="color:#898989; text-align:center;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a>';
						send_email(get_user_email($id), 'Thanks for referring a new user!.', '<p>Thank you for referring ' . get_username($cust_id) . ' to us! You received $5.00 in referral credit (applied automatically to your next purchase or subscription renewal). You now have <strong>$' . number_format($referral_credit, 2) . '</strong> in referral credit.</p>' . $links_html);
						add_referred_paid_user($ref_id, $cust_id);
    				}

    				//referrer
    				if($amount < $normal_amount) {
    					$difference = $normal_amount - $amount;
    					add_referral_credit($cust_id, -1 * $difference);
    				}

    				//customer info
    				update_user_meta($cust_id, 'discord_id', $_POST['x_reference_3']);
    				update_user_meta($cust_id, 'subscription_type', 'classic');
    				update_user_meta($cust_id, 'subscription_end_time', strtotime('+1 month'));
    				update_user_meta($cust_id, 'email', $_POST['Client_Email']);
    				update_user_meta($cust_id, 'card_type', $_POST['TransactionCardType']);
    				update_user_meta($cust_id, 'has_subscribed', 'true');

    				if(isset($_POST['Card_Number'])) {
    					//if user used card, then store token data for future charges
    					update_user_meta($cust_id, 'token', $_POST['Card_Number']);
    					update_user_meta($cust_id, 'cardholder_name', $_POST['CardHoldersName']);
    					//update_user_meta($cust_id, 'monthly_amount', $amount); DETERMINED IN CLASSIC-FORM.PHP
    					update_user_meta($cust_id, 'expiry_date', $_POST['Expiry_Date']);
    					if($_POST['TransactionCardType'] != 'PayPal') update_user_meta($cust_id, 'subscription_active', 'true');
    				}

    				//add discord role "Tier 1"
    				add_discord_user($_POST['x_reference_3']);
				break;
			}
		}else {
			//store POST data for reference
			update_user_meta($cust_id, 'last_purchase', $get_var_dump($_POST));
		}
	}
}
?>