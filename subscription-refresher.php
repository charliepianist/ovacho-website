<?php /*Template Name: Subscription Refresher **Backend** */ ?>
<?php
/*if(get_user_meta(1, 'cron', true)) update_user_meta(1, 'cron', get_user_meta(1, 'cron', true) + 1);
else update_user_meta(1, 'cron', '1');*/
update_user_meta(1, 'last_cron', date('m/d/y G:i:s T'));
$users = get_users();
$time = time();
foreach($users as $user) {
	//user has subscription
	if(get_user_subscription($user->id) !== 'basic') {

		//has the subscription expired
		if(has_subscription_expired($user->id, $time)) {
			//set new period
			update_user_meta($user->id, 'three_day_email_sent', 'false');

			//is subscription active
			if(is_subscription_active($user->id)) {

				//attempt to charge
				$new_end_time = charge_subscription($user->id);
				if($new_end_time) {
					//Successful charge
					update_user_meta($user->id, 'subscription_end_time', $new_end_time);
				}else {
					//failed charge
					revoke_subscription($user->id);
				}
			}else {
				//subscription was canceled previously
				revoke_subscription($user->id);
				$links_html = '<p style="color:#898989;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a></p>';
				send_email(get_user_email($id), 'Notice of Subscription End', '<p>We noticed that you don\'t have automatic billing enabled (either you canceled your subscription or your payment method is Paypal, for which we will be releasing automatic billing soon). This email is a reminder that your subscription period has ended. <a href="' . site_url('pricing') .'">Renew your subscription?</a></p><br>' . $links_html);
			}
		}else if(three_day_email($user->id, $time)) {
			$links_html = '<p style="color:#898989;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a>';
			$response = false;

			if(is_subscription_active($user->id)) {
				//Notify that they will be charged
				$links_html .= ' | <a style="color:#898989;" href="' . site_url('account') . '">UNSUBSCRIBE</a></p>';
				$response = send_email(get_user_email($id), '3 Day Reminder of Subscription Renewal', '<p>Thank you for your subscription to our service! This email is a reminder that in 3 days, you will be charged automatically to continue your subscription (' . nice_stored_payment_method($user->id) . ').</p><br>' . $links_html);
			}else {
				//Notify that subscription will end
				$links .= '</p>';
				$response = send_email(get_user_email($id), '3 Day Reminder of Subscription End', '<p>We noticed that you canceled your subscription with us. This email is a reminder that your subscription period will end in <strong>3 days</strong>. View your subscription status <a href="' . site_url('account') . '">here</a>.</p><br>' . $links_html);
			}

			if($response) {
				//mark sent
				update_user_meta($user->id, 'three_day_email_sent', 'true');
			}
		}
	}
}
?>