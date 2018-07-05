<?php /*Template Name: User Dump **Backend** */ ?>
<html>
<div style="background-color: white; color:black;">
<br><br><pre>
<?php

function user_dump($user) {
	$subscription_type = get_user_meta($user->id, 'subscription_type', true);
	if($subscription_type == '') $subscription_type = 'basic';
	echo '<strong>Subscription: ' . $subscription_type . '</strong><br>';
	var_dump($user);
	echo '<br><h2>Meta:</h2> ';
	var_dump(get_user_meta($user->id));
}

function orderby() {
	if(isset($_GET['orderby'])) return $_GET['orderby'];
	return 'nicename';
}

if(current_user_can('administrator')) {
	switch($_GET['user_id']) {
		case 'all':
			$users = get_users(array(
				'orderby' => orderby()
			));
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
				if(get_user_meta($user->id, 'subscription_type') != FALSE && get_user_meta($user->id, 'subscription_type') !== 'basic') {
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
				if(get_user_meta($user->id, 'subscription_type') != FALSE && get_user_meta($user->id, 'subscription_type') !== 'basic') {
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
				update_user_meta($_GET['user_id'], 'subscription_type', 'basic');
				remove_discord_user(get_user_meta($_GET['user_id'], 'discord_id', true));
				echo 'Success';
				break;

				case 'subscribe':
				update_user_meta($_GET['user_id'], 'subscription_type', 'classic');
				update_user_meta($_GET['user_id'], 'subscription_end_time', strtotime('+1 month'));
				if(isset($_GET['discord_id'])) {
					update_user_meta($_GET['user_id'], 'discord_id', $_GET['discord_id']);
					add_discord_user($_GET['discord_id']);
				}
				echo 'Success';
				break;

				case 'subscribe_year':
				update_user_meta($_GET['user_id'], 'subscription_type', 'classic');
				update_user_meta($_GET['user_id'], 'subscription_end_time', strtotime('+1 year'));
				if(isset($_GET['discord_id'])) {
					update_user_meta($_GET['user_id'], 'discord_id', $_GET['discord_id']);
					add_discord_user($_GET['discord_id']);
				}
				echo 'Success';
				break;

				case 'subscribe_discount':
				update_user_meta($_GET['user_id'], 'subscription_type', 'classic');
				update_user_meta($_GET['user_id'], 'subscription_end_time', strtotime('+1 month'));
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