<?php /*Template Name: Subscription Refresher **Backend** */ ?>
<?php
update_user_meta(1, 'last_cron', date('m/d/y G:i:s T'));

//make sure stats directory exists
if(!file_exists('stats')) mkdir('stats');

//user count
$users = get_users();
$file_uc = 'stats/users.txt';
if(file_exists($file_uc)) {
	$lines = file($file_uc);
	$line = $lines[count($lines) - 1];
	if(strpos($line, date('m/d/y')) === false || !$line) file_put_contents($file_uc, date('m/d/y') . "," . count($users) . "\r\n", FILE_APPEND);
}else file_put_contents($file_uc, date('m/d/y') . "," . count($users) . "\r\n");

//subscriber count
$file_sc = 'stats/subscribers.txt';
if(file_exists($file_sc)) {
	$lines = file($file_sc);
	$line = $lines[count($lines) - 1];
	if(strpos($line, date('m/d/y')) === false || !$line) file_put_contents($file_sc, date('m/d/y') . "," . get_subscriber_count() . "," . get_real_subscriber_count() . "," . get_active_subscriber_count() . "\r\n", FILE_APPEND);
}else file_put_contents($file_sc, date('m/d/y') . "," . get_subscriber_count() . "," . get_real_subscriber_count() . "," . get_active_subscriber_count() . "\r\n", FILE_APPEND);

$time = time();
foreach($users as $user) {
	//user has subscription
	if(get_user_subscription($user->id) !== 'basic') {
		switch(stored_payment_method($user->id)) {
			//Card payments (Payeezy)
			case 'card':
			//has the subscription expired
			if(has_subscription_expired($user->id, $time)) {
				//set new period
				update_user_meta($user->id, 'three_day_email_sent', 'false');

				$referral_credit = get_referral_credit($user->id);
				$monthly_amount = get_next_amount($user->id);
				//is subscription active
				if(is_subscription_active($user->id) || $referral_credit >= $monthly_amount) {

					//check if completely covered by referral credit
					if($referral_credit >= $monthly_amount) {

						//remove amount
						add_referral_credit($user->id, -1 * $monthly_amount);
						//add month
						update_user_meta($user->id, 'subscription_end_time', strtotime('+1 month'));
						//add transaction
						add_transaction($user->id, 'Paid fully by referral credit ($' . $referral_credit . ' to $' . number_format($referral_credit - $monthly_amount, 2) . '), timestamp ' . time());
						//email
						send_email(get_user_email($user->id), 'Thank you! Your subscription was renewed automatically.', '<p>Your subscription with us has been automatically renewed using referral credit.<br><br>Your previous referral credit: <strong>$' . $referral_credit . '</strong><br>Your new referral credit: <strong>$' . number_format($referral_credit - $monthly_amount, 2) . '</strong></p><br>' . 
	                    EMAIL_LINKS_UNSUBSCRIBE_HTML);
					}else {
						//attempt to charge
						$new_end_time = charge_subscription($user->id);
						if($new_end_time) {
							//Successful charge
							update_user_meta($user->id, 'subscription_end_time', $new_end_time);
						}else {
							//failed charge
							revoke_subscription($user->id);
						}
					}
				}else {
					//subscription was canceled previously
					revoke_subscription($user->id);
					send_email(get_user_email($user->id), 'Notice of Subscription End', '<p>We noticed that you don\'t have automatic billing enabled. We\'re sorry to have you leaving us! This email is a reminder that your subscription period has ended. <a href="' . site_url('pricing') .'">Renew your subscription?</a></p><br>' . EMAIL_LINKS_HTML);
				}
			}else if(three_day_email($user->id, $time)) {
				$response = false;
				if(is_subscription_active($user->id)) {
					//Notify that they will be charged
					$response = send_email(get_user_email($user->id), '3 Day Reminder of Subscription Renewal', '<p>Thank you for your subscription to our service! This email is a reminder that in 3 days, you will be charged automatically to continue your subscription (' . nice_stored_payment_method($user->id) . ').</p><br>' . EMAIL_LINKS_UNSUBSCRIBE_HTML);
				}else {
					//Notify that subscription will end
					$response = send_email(get_user_email($user->id), '3 Day Reminder of Subscription End', '<p>We noticed that you canceled your subscription with us. This email is a reminder that your subscription period will end in <strong>3 days</strong>. View your subscription status <a href="' . site_url('account') . '">here</a>.</p><br>' . EMAIL_LINKS_HTML);
				}

				if($response) {
					//mark sent
					update_user_meta($user->id, 'three_day_email_sent', 'true');
				}
			}
			break;

			//PayPal
			case 'paypal':
				if(three_day_email($user->id, $time)) {
					$response = false;
					if(is_subscription_active($user->id)) {
						//Notify that they will be charged
						$response = send_email(get_user_email($user->id), '3 Day Reminder of Subscription Renewal', '<p>Thank you for your subscription to our service! This email is a reminder that in 3 days, you will be charged automatically to continue your subscription (' . nice_stored_payment_method($user->id) . ').</p><br>' . EMAIL_LINKS_UNSUBSCRIBE_HTML);
					}else {
						//Notify that subscription will end
						$response = send_email(get_user_email($user->id), '3 Day Reminder of Subscription End', '<p>We noticed that you have canceled your subscription with us (and hope you might rejoin us soon). This email is a reminder that your subscription period will end in <strong>3 days</strong>. View your subscription status <a href="' . site_url('account') . '">here</a>.</p><br>' . EMAIL_LINKS_HTML);
					}

					if($response) {
						//mark sent
						update_user_meta($user->id, 'three_day_email_sent', 'true');
					}
				}
				if(has_subscription_expired($user->id, $time)) send_email('ovachoinvestments@gmail.com', 'PayPal User (' . $user->id . ')Subscription End', 'User ' . $user->id . 'subscription end.'); //temporary
			break;
		} //CLOSING BRACKET OF SWITCH STATEMENT
	}//CLOSING BRACKET OF if subscription isn't basic
}

?>