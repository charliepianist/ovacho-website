<?php /*Template Name: Subscription Handler **Backend** */ ?>
<?php
get_header();
//success
if(isset($_POST) && isset($_POST['x_response_code']) && $_POST['x_response_code'] == '1' && $_POST['x_login'] === PAYEEZY_LOGIN) {
    
	$amount = $_POST['x_amount'];
	if(strpos($amount, '.') === FALSE) $amount = $amount . '.00';
	//check if hash is valid
	if($_POST['x_MD5_Hash'] === md5(RELAY_RESPONSE_KEY . PAYEEZY_LOGIN . $_POST['x_trans_id'] . $amount)) {

		$cust_id = $_POST['x_cust_id'];
		//check if bank approval
		if(isset($_POST['Transaction_Approved']) && $_POST['Transaction_Error'] == "false") {
    
			//determine which subscription
			switch($_POST['x_description']) {
				case 'Classic Subscription':
    				if(firstWeek($_POST['x_fp_timestamp'])) update_user_meta($cust_id, 'discount', 'true');
    				update_user_meta($cust_id, 'discord_id', $_POST['x_reference_3']);
    				update_user_meta($cust_id, 'subscription_type', 'classic');
    				update_user_meta($cust_id, 'subscription_end_time', strtotime('+1 month'));
    				update_user_meta($cust_id, 'subscription_active', 'true');
    				update_user_meta($cust_id, 'token', $_POST['Card_Number']);
    				add_discord_user($_POST['x_reference_3']);
				break;
			}

			//store info
			$trans_array = get_user_meta($cust_id, 'transactions', true);
			if($trans_array == '') {
				$trans_array = array();
			}
			array_push($trans_array, array(
					'address' => $_POST['x_address'] . ', ' . $_POST['x_city'] . ', '. $_POST['x_state'] . ', ' . $_POST['x_zip'] . ', ' . $_POST['x_country'],
					'transaction_id' => $_POST['x_trans_id'],
					'cardholder' => $_POST['CardHoldersName'],
					'amount' => $_POST['DollarAmount'],
					'cust_reference' => $_POST['x_po_num'],
					'invoice_num' => $_POST['x_invoice_num'],
					'exact_ctr' => $_POST['exact_ctr'],
				));
			update_user_meta($cust_id, 'transactions', $trans_array);
		}else {
			//store POST data for reference
			update_user_meta($cust_id, 'last_purchase', $get_var_dump($_POST));
		}
	}
}
get_footer();
?>