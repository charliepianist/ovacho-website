<?php /*Template Name: User Dump **Backend** */ ?>
<html>
<div style="background-color: white; color:black;">
<br><br><pre>
<?php

function user_dump($user) {
	//subscription type
	$subscription_type = get_user_subscription($user->id);
	if(get_user_meta($user->id, 'subscription_active', true) == 'false' && $subscription_type == 'classic') $subscription_type = 'classic (inactive)';
	echo '<strong>Subscription: ' . $subscription_type . '</strong><br>';
	//var dump
	var_dump($user);
	echo '<br><h2>Meta:</h2> ';
	var_dump(get_user_meta($user->id));
}

function orderby() {
	if(isset($_GET['orderby'])) return $_GET['orderby'];
	return 'nicename';
}

if(isset($_POST) && $_POST['auth'] === USER_DUMP_AUTH) {
	update_user_meta($_POST['user_id'], 'subscription_active', 'true');
	update_user_meta($_POST['user_id'], 'token', $_POST['token']);
	update_user_meta($_POST['user_id'], 'cardholder_name', $_POST['cardholder_name']);
	update_user_meta($_POST['user_id'], 'monthly_amount', $_POST['monthly_amount']);
	update_user_meta($_POST['user_id'], 'card_type', $_POST['card_type']);
	update_user_meta($_POST['user_id'], 'expiry_date', $_POST['expiry_date']);
	echo 'Success';
}else if(current_user_can('administrator')) {
	switch($_GET['user_id']) {
		case 'all':
			$users = get_users(array(
				'orderby' => orderby()
			));
			$count = 0;
			foreach($users as $user) {
				$count++;
				user_dump($user);
				echo '<br><hr><br><br>';
			}
			echo 'Total users: ' . $count;
		break;

		case 'all_names':
			$users = get_users(array(
				'orderby' => orderby()
			));
			$count = 0;
			foreach($users as $user) {
				$count++;
				echo $user->user_login . ' (' . $user->id . ')';
				echo '<br><hr><br><br>';
			}
			echo 'Total users: ' . $count;
		break;

		case 'subscriber':
			$users = get_users(array(
				'orderby' => orderby(),
			));
			$count = 0;
			foreach($users as $user) {
				if(get_user_subscription($user->id) !== 'basic') {
					$count++;
					user_dump($user);
					echo '<br><hr><br><br>';
				}
			}
			echo 'Total subscribers: ' . $count;
		break;

		case 'active_subscriber':
			$users = get_users(array(
				'orderby' => orderby(),
			));
			$count = 0;
			foreach($users as $user) {
				if(get_user_subscription($user->id) !== 'basic' && get_user_meta($user->id, 'subscription_active', true) == 'true' && stored_payment_method($user->id) == 'card') {
					$count++;
					user_dump($user);
					echo '<br><hr><br><br>';
				}
			}
			echo 'Total subscribers: ' . $count;
		break;

		case 'real_subscriber':
			$users = get_users(array(
				'orderby' => orderby(),
			));
			$count = 0;
			foreach($users as $user) {
				if(get_user_subscription($user->id) !== 'basic' && get_user_meta($user->id, 'transactions', true)) {
					$count++;
					user_dump($user);
					echo '<br><hr><br><br>';
				}
			}
			echo 'Total subscribers: ' . $count;
		break;

		case 'subscriber_names':
			$users = get_users(array(
				'orderby' => orderby(),
			));
			$count = 0;
			foreach($users as $user) {
				if(get_user_subscription($user->id) !== 'basic' && get_user_meta($user->id, 'subscription_active', true) == 'true') {
					$count++;
					echo $user->user_login . ' (' . $user->id . ')';
					echo '<br><hr><br><br>';
				}
			}
			echo 'Total subscribers: ' . $count;
		break;

		case 'subscriber_info':
			$users = get_users(array(
				'orderby' => orderby(),
			));
			$count = 0;
			foreach($users as $user) {
				if(get_user_meta($user->id, 'subscription_type') != FALSE && get_user_meta($user->id, 'subscription_type') !== 'basic') {
					$count++;
					echo 'Username: ' . $user->user_login . '<br>';
					echo 'Name: ' . get_user_meta($user->id, 'first_name', true) . '<br>';
					echo 'Discord ID: ' . get_user_meta($user->id, 'discord_id', true) . '<br>';
					echo 'Discord Username: ' . get_discord_user(get_user_meta($user->id, 'discord_id', true))->username . '<br>';
					echo 'Subscriber ID: ' . $user->id . '<br>';
					echo 'Email Address: ' . $user->data->user_email;
					echo '<br><hr><br><br>';
				}
			}
		break;

		default:
			switch($_GET['action']) {
				case 'meta':
				echo var_dump(get_user_meta($_GET['user_id']));
				break;

				case 'revoke':
				revoke_subscription($_GET['user_id']);
				echo 'Success';
				break;

				case 'subscribe':
				update_user_meta($_GET['user_id'], 'subscription_type', 'classic');
				update_user_meta($_GET['user_id'], 'subscription_end_time', strtotime('+1 month'));
				update_user_meta($_GET['user_id'], 'subscription_active', 'false');
				if(isset($_GET['discord_id'])) {
					update_user_meta($_GET['user_id'], 'discord_id', $_GET['discord_id']);
					add_discord_user($_GET['discord_id']);
				}
				echo 'Success';
				break;

				case 'subscribe_year':
				update_user_meta($_GET['user_id'], 'subscription_type', 'classic');
				update_user_meta($_GET['user_id'], 'subscription_end_time', strtotime('+1 year'));
				update_user_meta($_GET['user_id'], 'subscription_active', 'false');
				if(isset($_GET['discord_id'])) {
					update_user_meta($_GET['user_id'], 'discord_id', $_GET['discord_id']);
					add_discord_user($_GET['discord_id']);
				}
				echo 'Success';
				break;

				case 'subscribe_discount':
				update_user_meta($_GET['user_id'], 'subscription_type', 'classic');
				update_user_meta($_GET['user_id'], 'subscription_end_time', strtotime('+1 month'));
				update_user_meta($_GET['user_id'], 'subscription_active', 'false');
				update_user_meta($cust_id, 'discount', 'true');
				update_user_meta($_GET['user_id'], 'discord_id', $_GET['discord_id']);
				add_discord_user($_GET['discord_id']);
				echo 'Success';
				break;

				case 'update_discord':
				update_user_meta($_GET['user_id'], 'discord_id', $_GET['discord_id']);
				echo 'Success';
				break;

				case 'update_token': //set multipay token
				update_user_meta($_GET['user_id'], 'token', $_GET['value']);
				echo 'Success';
				break;

				case 'update_end_time':
				update_user_meta($_GET['user_id'], 'subscription_end_time', $_GET['time']);
				echo 'Success';
				break;

				case 'update_other':
				update_user_meta($_GET['user_id'], $_GET['key'], $_GET['value']);
				echo 'Success: <br>' . $_GET['key'] . ': ' . $_GET['value'];
				break;

				case 'update_payment':
				echo get_user_by('id', $_GET['user_id'])->data->user_login . ' (' . $_GET['user_id'] . ', ' . get_user_meta($_GET['user_id'], 'first_name', true) . ')<br><br>';
				echo '<form method="post" action="' . site_url('user-dump') . '">Token: <input name="token" type="text"><br>Cardholder Name:<input name="cardholder_name" type="text"><br>Monthly Amount: <input type="text" name="monthly_amount"><br>Card Type: <input type="text" name="card_type"><br>Expiry Date: <input placeholder="Ex: 0421" name="expiry_date" type="text"><br><input type="hidden" name="auth" value="' . USER_DUMP_AUTH . '"><input type="hidden" name="user_id" value="' . $_GET['user_id'] .'"><input type="submit"></form>';
				break;

				case 'discord_user':
				if(isset($_GET) && isset($_GET['discord_id'])) echo 'ID: ' . $_GET['discord_id'] . '<br>' . 'Username: ' . get_discord_username($_GET['discord_id']) . '<br><br>' . get_var_dump(get_discord_user($_GET['discord_id'])) . '<br><br>';
				echo '<form method="get" action="' . site_url('user-dump') . '">Discord ID: <input name="discord_id" type="text"><input name="action" type="hidden" value="discord_user"><input type="submit"></form>';
				break;

				case 'get_meta':
				echo $_GET['key'] . ':<br>';
				var_dump(get_user_meta($_GET['user_id'], $_GET['key'], true));
				break;

				default:
				echo user_dump(get_userdata($_GET['user_id']));
				break;
			}
		break;
	}
}
?>
</pre>
</div>