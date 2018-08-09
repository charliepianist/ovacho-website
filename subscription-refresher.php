<?php /*Template Name: Subscription Refresher **Backend** */ ?>
<?php
update_user_meta(1, 'last_cron', date('m/d/y G:i:s T'));

//user count
$users = get_users();
$file = 'user_count.txt';
if(file_exists($file)) {
	$lines = file($file);
	$line = $lines[count($lines) - 1];
	if(strpos($line, date('m/d/y')) === false || !$line) file_put_contents($file, date('m/d/y') . "     " . count($users) . "\r\n", FILE_APPEND);
}else file_put_contents($file, date('m/d/y') . "     " . count($users) . "\r\n");


update_user_meta(1, 'temp', $line);

$time = time();
foreach($users as $user) {
	//user has subscription
	if(get_user_subscription($user->id) !== 'basic') {

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
					$links_html = '<p style="color:#898989; text-align:center;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a> | <a style="color:#898989;" href="' . site_url('account') . '">UNSUBSCRIBE</a></p>';
					send_email(get_user_email($user->id), 'Thank you! Your subscription was renewed automatically.', '<p>Your subscription with us has been automatically renewed using referral credit.<br><br>Your previous referral credit: <strong>$' . $referral_credit . '</strong><br>Your new referral credit: <strong>$' . number_format($referral_credit - $monthly_amount, 2) . '</strong></p><br>' . 
                    $links_html);
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
				$links_html = '<p style="color:#898989;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a></p>';
				send_email(get_user_email($user->id), 'Notice of Subscription End', '<p>We noticed that you don\'t have automatic billing enabled (either you canceled your subscription or your payment method is Paypal, for which we will be releasing automatic billing soon). This email is a reminder that your subscription period has ended. <a href="' . site_url('pricing') .'">Renew your subscription?</a></p><br>' . $links_html);
			}
		}else if(three_day_email($user->id, $time)) {
			$links_html = '<p style="color:#898989;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a>';
			$response = false;

			if(is_subscription_active($user->id)) {
				//Notify that they will be charged
				$links_html .= ' | <a style="color:#898989;" href="' . site_url('account') . '">UNSUBSCRIBE</a></p>';
				$response = send_email(get_user_email($user->id), '3 Day Reminder of Subscription Renewal', '<p>Thank you for your subscription to our service! This email is a reminder that in 3 days, you will be charged automatically to continue your subscription (' . nice_stored_payment_method($user->id) . ').</p><br>' . $links_html);
			}else {
				//Notify that subscription will end
				$links .= '</p>';
				$response = send_email(get_user_email($user->id), '3 Day Reminder of Subscription End', '<p>We noticed that you canceled your subscription with us. This email is a reminder that your subscription period will end in <strong>3 days</strong>. View your subscription status <a href="' . site_url('account') . '">here</a>.</p><br>' . $links_html);
			}

			if($response) {
				//mark sent
				update_user_meta($user->id, 'three_day_email_sent', 'true');
			}
		}
	}
}

?>